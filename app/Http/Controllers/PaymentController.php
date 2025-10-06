<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Enrollment;
use App\Services\EnrollmentService;
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
    public function storeStudentPayment(Request $request, Student $student)
    {
        // Additional validation for student-specific payment
        $request->validate([
            'enrollment_id' => 'required|exists:enrollments,id',
        ]);

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

        // Check if payment amount doesn't exceed due amount
        if ($request->amount > $enrollment->due_amount) {
            return redirect()->back()->withErrors([
                'amount' => __('payments.amount_exceeds_due_amount', ['due' => number_format($enrollment->due_amount, 2)])
            ]);
        }

        $result = $this->enrollmentService->handlePaymentAddition(
            $enrollment->id, 
            $request->all(),
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
    public function storeEnrollmentPayment(Request $request, Enrollment $enrollment)
    {
        // Verify that the enrollment is active
        if (!$enrollment->is_active) {
            return redirect()->back()->withErrors([
                'enrollment' => __('payments.enrollment_not_active')
            ]);
        }

        // Check if payment amount doesn't exceed due amount
        if ($request->amount > $enrollment->due_amount) {
            return redirect()->back()->withErrors([
                'amount' => __('payments.amount_exceeds_due_amount', ['due' => number_format($enrollment->due_amount, 2)])
            ]);
        }

        $result = $this->enrollmentService->handlePaymentAddition(
            $enrollment->id, 
            $request->all(),
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
    public function store(Request $request)
    {
        //
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
}
