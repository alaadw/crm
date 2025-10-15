<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\CourseClass;
use App\Models\Currency;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Exception;

class EnrollmentService
{
    /**
     * Handle enrollment creation with proper response
     */
    public function handleEnrollmentCreation(array $data)
    {
        try {
            $enrollment = $this->createEnrollment($data);
            
            return [
                'success' => true,
                'enrollment' => $enrollment,
                'message' => __('enrollments.enrollment_created_successfully'),
                'redirect_to' => route('students.show', $data['student_id'])
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'input' => $data
            ];
        }
    }

    /**
     * Handle enrollment deletion/cancellation
     */
    public function handleEnrollmentDeletion(Enrollment $enrollment, $reason = null)
    {
        $studentId = $enrollment->student_id;
        
        try {
            $this->cancelEnrollment($enrollment->id, $reason ?? __('enrollments.deleted_by_admin'));
            
            return [
                'success' => true,
                'message' => __('enrollments.enrollment_deleted_successfully'),
                'redirect_to' => route('students.show', $studentId)
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Validate enrollment request data
     */
    public function validateEnrollmentRequest(array $data)
    {
        $validator = Validator::make($data, [
            'student_id' => 'required|exists:students,id',
            'department' => 'nullable|exists:categories,id',
            'course_class_id' => 'required|exists:course_classes,id',
            'total_amount' => 'required|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'currency_code' => 'nullable|exists:currencies,code',
            'enrollment_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
            'payment_method' => 'nullable|in:cash,bank_transfer,credit_card,check,zaincash,other',
        ]);

        if ($validator->fails()) {
            throw new Exception($validator->errors()->first());
        }

        // Additional business logic validation
        $paid_amount = $data['paid_amount'] ?? 0;
        if ($paid_amount > $data['total_amount']) {
            throw new Exception(__('enrollments.paid_amount_exceeds_total'));
        }

        return true;
    }

    /**
     * Get departments with available course classes for enrollment
     */
    public function getAvailableDepartments()
    {
        return Category::whereHas('courseClasses', function($query) {
            $query->where('is_active', true)
                  ->whereIn('status', ['in_progress', 'registration', 'upcoming']);
        })->with(['courseClasses' => function($query) {
            $query->where('is_active', true)
                  ->whereIn('status', ['in_progress', 'registration', 'upcoming'])
                  ->with('course');
        }])->get();
    }

    /**
     * Prepare course classes data for JavaScript frontend
     */
    public function prepareCourseClassesData($departments)
    {
        $courseClassesData = [];
        foreach ($departments as $department) {
            $courseClassesData[$department->id] = $department->courseClasses->map(function($class) {
                return [
                    'id' => $class->id,
                    'name' => $class->class_name,
                    'course_name' => $class->course->name,
                    'price' => $class->default_price,
                    'start_date' => $class->start_date ? $class->start_date->format('M d, Y') : 'TBD',
                    'instructor' => $class->instructor_name ?? 'TBD'
                ];
            })->toArray();
        }
        return $courseClassesData;
    }

    /**
     * Validate enrollment requirements
     */
    public function validateEnrollment(Student $student, CourseClass $courseClass)
    {
        $errors = [];

        // Check if student is already enrolled in this class (only active enrollments)
        $existingEnrollment = Enrollment::where('student_id', $student->id)
            ->where('course_class_id', $courseClass->id)
            ->where('is_active', true)
            ->first();

        if ($existingEnrollment) {
            $errors[] = __('enrollments.already_enrolled');
        }

        // Check if class is at capacity (only count active enrollments)
        if ($courseClass->max_students) {
            $currentEnrollments = $courseClass->enrollments()->where('is_active', true)->count();
            if ($currentEnrollments >= $courseClass->max_students) {
                $errors[] = __('enrollments.class_full');
            }
        }

        // Check if class is still accepting enrollments
        if (!in_array($courseClass->status, ['registration', 'in_progress', 'upcoming'])) {
            $errors[] = __('enrollments.enrollment_closed');
        }

        // Check if class is active
        if (!$courseClass->is_active) {
            $errors[] = __('enrollments.class_inactive');
        }

        return $errors;
    }

    /**
     * Create a new enrollment with payment
     */
    public function createEnrollment(array $data)
    {
        // Validate request data
        $this->validateEnrollmentRequest($data);

        return DB::transaction(function() use ($data) {
            // Get student and course class
            $student = Student::findOrFail($data['student_id']);
            $courseClass = CourseClass::findOrFail($data['course_class_id']);

            // Validate enrollment business rules
            $validationErrors = $this->validateEnrollment($student, $courseClass);
            if (!empty($validationErrors)) {
                throw new Exception(implode(', ', $validationErrors));
            }

            // Get currency and calculate amounts in JOD
            $currencyCode = $data['currency_code'] ?? 'JOD';
            $currency = \App\Models\Currency::where('code', $currencyCode)->first();
            $exchangeRate = $currency->exchange_rate_to_jod ?? 1;
            
            // Total amount in original currency
            $totalAmountOriginal = $data['total_amount'] ?? $courseClass->default_price ?? 0;
            $paidAmountOriginal = $data['paid_amount'] ?? 0;
            
            // Convert to JOD for enrollment table
            $totalAmountJOD = $currency ? $currency->convertToJOD($totalAmountOriginal) : $totalAmountOriginal;
            $paidAmountJOD = $currency ? $currency->convertToJOD($paidAmountOriginal) : $paidAmountOriginal;
            $dueAmountJOD = $totalAmountJOD - $paidAmountJOD;

            // Create enrollment (amounts in JOD)
            $enrollment = Enrollment::create([
                'student_id' => $student->id,
                'course_class_id' => $courseClass->id,
                'registered_by' => auth()->id() ?? 1, // Default to user ID 1 if not authenticated
                'enrollment_date' => $data['enrollment_date'],
                'total_amount' => $totalAmountJOD,
                'paid_amount' => $paidAmountJOD,
                'due_amount' => $dueAmountJOD,
                'payment_status' => $this->calculatePaymentStatus($totalAmountJOD, $paidAmountJOD),
                'notes' => $data['notes'] ?? null,
                'is_active' => true,
            ]);

            // Create payment record if amount paid (store original currency)
            if ($paidAmountOriginal > 0) {
                Payment::create([
                    'enrollment_id' => $enrollment->id,
                    'received_by' => auth()->id() ?? 1,
                    'amount' => $paidAmountOriginal,
                    'currency_code' => $currencyCode,
                    'amount_in_jod' => $paidAmountJOD,
                    'exchange_rate' => $exchangeRate,
                    'payment_method' => $data['payment_method'] ?? 'cash',
                    'payment_date' => $data['enrollment_date'],
                    'notes' => 'Initial enrollment payment',
                ]);

                // Recalculate enrollment from authoritative payment sums (handles rounding/epsilon)
                $enrollment->recalcFromPayments();
            }

            return $enrollment->load(['student', 'courseClass.course', 'payments']);
        });
    }

    /**
     * Calculate payment status based on amounts
     */
    private function calculatePaymentStatus($totalAmount, $paidAmount)
    {
        if ($paidAmount == 0) {
            return 'not_paid';
        } elseif ($paidAmount >= $totalAmount) {
            return 'completed';
        } else {
            return 'partial';
        }
    }

    /**
     * Get enrollment with related data
     */
    public function getEnrollment($id)
    {
        return Enrollment::with([
            'student',
            'courseClass.course',
            'courseClass.category',
            'payments'
        ])->findOrFail($id);
    }

    /**
     * Get student enrollments
     */
    public function getStudentEnrollments(Student $student, $includeInactive = false)
    {
        $query = $student->enrollments()
            ->with(['courseClass.course', 'payments'])
            ->orderBy('enrollment_date', 'desc');
            
        if (!$includeInactive) {
            $query->where('is_active', true);
        }
        
        return $query->get();
    }

    /**
     * Cancel enrollment
     */
    public function cancelEnrollment($enrollmentId, $reason = null)
    {
        return DB::transaction(function() use ($enrollmentId, $reason) {
            $enrollment = Enrollment::findOrFail($enrollmentId);
            
            $enrollment->update([
                'is_active' => false,
                'notes' => $enrollment->notes . "\n" . __('enrollments.cancelled_reason', ['reason' => $reason ?? __('enrollments.no_reason_provided')])
            ]);

            return $enrollment;
        });
    }

    /**
     * Handle payment addition with proper response
     */
    public function handlePaymentAddition($enrollmentId, array $paymentData, $redirectRoute = null)
    {
        try {
            $payment = $this->addPayment($enrollmentId, $paymentData);
            
            return [
                'success' => true,
                'payment' => $payment,
                'message' => __('payments.payment_added_successfully'),
                'redirect_route' => $redirectRoute
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'input' => $paymentData
            ];
        }
    }

    /**
     * Add payment to existing enrollment
     */
    public function addPayment($enrollmentId, array $paymentData)
    {
        return DB::transaction(function() use ($enrollmentId, $paymentData) {
            $enrollment = Enrollment::findOrFail($enrollmentId);

            // Get currency and calculate amount in JOD
            $currencyCode = $paymentData['currency_code'] ?? 'JOD';
            $currency = Currency::where('code', $currencyCode)->first();
            $amountInJOD = $currency ? $currency->convertToJOD($paymentData['amount']) : $paymentData['amount'];

            // Create payment
            $payment = Payment::create([
                'enrollment_id' => $enrollment->id,
                'received_by' => auth()->id() ?? 1,
                'amount' => $paymentData['amount'],
                'currency_code' => $currencyCode,
                'amount_in_jod' => $amountInJOD,
                'exchange_rate' => $currency->exchange_rate_to_jod ?? 1,
                'payment_method' => $paymentData['payment_method'],
                'payment_date' => $paymentData['payment_date'],
                'notes' => $paymentData['notes'] ?? null,
            ]);

            // Recalculate enrollment from payments to ensure consistency and avoid float drift
            $enrollment->recalcFromPayments();

            return $payment->load('enrollment.student');
        });
    }
}