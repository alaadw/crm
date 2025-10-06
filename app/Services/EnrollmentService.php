<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\CourseClass;
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
            'enrollment_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
            'payment_method' => 'nullable|in:cash,bank_transfer,credit_card,check',
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

            // Calculate payment details
            $totalAmount = $data['total_amount'] ?? $courseClass->default_price ?? 0;
            $paidAmount = $data['paid_amount'] ?? 0;
            $dueAmount = $totalAmount - $paidAmount;

            // Create enrollment
            $enrollment = Enrollment::create([
                'student_id' => $student->id,
                'course_class_id' => $courseClass->id,
                'registered_by' => auth()->id() ?? 1, // Default to user ID 1 if not authenticated
                'enrollment_date' => $data['enrollment_date'],
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'payment_status' => $this->calculatePaymentStatus($totalAmount, $paidAmount),
                'notes' => $data['notes'] ?? null,
                'is_active' => true,
            ]);

            // Create payment record if amount paid
            if ($paidAmount > 0) {
                Payment::create([
                    'enrollment_id' => $enrollment->id,
                    'received_by' => auth()->id() ?? 1, // User who received the payment
                    'amount' => $paidAmount,
                    'payment_method' => $data['payment_method'] ?? 'cash',
                    'payment_date' => $data['enrollment_date'],
                    'notes' => 'Initial enrollment payment',
                ]);
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
     * Validate payment request data
     */
    public function validatePaymentRequest(array $data)
    {
        $validator = Validator::make($data, [
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank_transfer,credit_card,check',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            throw new Exception($validator->errors()->first());
        }

        return true;
    }

    /**
     * Handle payment addition with proper response
     */
    public function handlePaymentAddition($enrollmentId, array $paymentData, $redirectRoute = null)
    {
        try {
            $this->validatePaymentRequest($paymentData);
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

            // Create payment
            $payment = Payment::create([
                'enrollment_id' => $enrollment->id,
                'received_by' => auth()->id() ?? 1, // User who received the payment
                'amount' => $paymentData['amount'],
                'payment_method' => $paymentData['payment_method'],
                'payment_date' => $paymentData['payment_date'],
                'notes' => $paymentData['notes'] ?? null,
            ]);

            // Update enrollment paid amount and payment status
            $newPaidAmount = $enrollment->paid_amount + $paymentData['amount'];
            $newDueAmount = $enrollment->total_amount - $newPaidAmount;
            
            $enrollment->update([
                'paid_amount' => $newPaidAmount,
                'due_amount' => $newDueAmount,
                'payment_status' => $this->calculatePaymentStatus($enrollment->total_amount, $newPaidAmount),
            ]);

            return $payment->load('enrollment.student');
        });
    }
}