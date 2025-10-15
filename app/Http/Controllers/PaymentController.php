<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Enrollment;
use App\Services\EnrollmentService;
use App\Http\Requests\StorePaymentRequest;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $enrollmentService;

    public function __construct(EnrollmentService $enrollmentService)
    {
        $this->enrollmentService = $enrollmentService;
    }

    /**
     * Store a new payment for a student's enrollment
     */
    public function storeStudentPayment(StorePaymentRequest $request, Student $student)
    {
        // Verify that the enrollment belongs to the student
        $enrollment = Enrollment::where('id', $request->enrollment_id)
            ->where('student_id', $student->id)
            ->where('is_active', true)
            ->first();

        if (!$enrollment) {
            return redirect()->back()->withErrors([
                'enrollment_id' => __('payments.enrollment_not_found_for_student')
            ]);
        }

        // Skip direct amount vs due comparison here because amount may be in a different currency.
        // EnrollmentService will handle currency conversion and updates safely.

        $result = $this->enrollmentService->handlePaymentAddition(
            $enrollment->id, 
            $request->validated(),
            route('students.show', $student)
        );

        if ($result['success']) {
            return redirect($result['redirect_route'])
                ->with('success', $result['message']);
        } else {
            return redirect()->back()
                ->withInput($result['input'])
                ->withErrors(['error' => $result['error']]);
        }
    }

    /**
     * Store a new payment for an enrollment from class view
     */
    public function storeEnrollmentPayment(StorePaymentRequest $request, Enrollment $enrollment)
    {
        // Verify that the enrollment is active
        if (!$enrollment->is_active) {
            return redirect()->back()->withErrors([
                'enrollment' => __('payments.enrollment_not_active')
            ]);
        }

        // Note: No amount validation here as payment can be in different currency
        
        $result = $this->enrollmentService->handlePaymentAddition(
            $enrollment->id, 
            $request->validated(),
            route('classes.show', $enrollment->course_class_id)
        );

        if ($result['success']) {
            return redirect($result['redirect_route'])
                ->with('success', $result['message']);
        } else {
            return redirect()->back()
                ->withInput($result['input'])
                ->withErrors(['error' => $result['error']]);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePaymentRequest $request)
    {
        $result = $this->enrollmentService->handlePaymentAddition(
            $request->enrollment_id, 
            $request->validated()
        );

        if ($result['success']) {
            return redirect()->back()
                ->with('success', $result['message']);
        } else {
            return redirect()->back()
                ->withInput($result['input'])
                ->withErrors(['error' => $result['error']]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Return payments for an enrollment (AJAX)
     */
    public function enrollmentPayments(Enrollment $enrollment)
    {
        $enrollment->load(['payments' => function($q) {
            $q->orderByDesc('payment_date');
        }, 'student']);

        $data = [
            'student' => [
                'id' => $enrollment->student?->id,
                'name' => $enrollment->student?->name,
            ],
            'totals' => [
                'total_amount' => (float) $enrollment->total_amount,
                'paid_amount' => (float) $enrollment->paid_amount,
                'due_amount' => (float) $enrollment->due_amount,
            ],
            'payments' => $enrollment->payments->map(function($p) {
                return [
                    'id' => $p->id,
                    'date' => optional($p->payment_date)->format('Y-m-d'),
                    'amount' => $p->amount,
                    'currency' => $p->currency_code,
                    'formatted_amount' => $p->formatted_amount,
                    'method' => $p->payment_method_label,
                    'notes' => $p->notes,
                ];
            }),
        ];

        return response()->json($data);
    }
}
