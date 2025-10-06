<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Enrollment;
use App\Services\EnrollmentService;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    protected $enrollmentService;

    public function __construct(EnrollmentService $enrollmentService)
    {
        $this->enrollmentService = $enrollmentService;
    }

    /**
     * Show the form for creating a new enrollment.
     */
    public function create(Request $request)
    {
        $student = Student::findOrFail($request->get('student_id'));
        
        // Get available departments and course classes data
        $departments = $this->enrollmentService->getAvailableDepartments();
        $courseClassesData = $this->enrollmentService->prepareCourseClassesData($departments);

        return view('enrollments.create', compact('student', 'departments', 'courseClassesData'));
    }

    /**
     * Store a newly created enrollment in storage.
     */
    public function store(Request $request)
    {
        $result = $this->enrollmentService->handleEnrollmentCreation($request->all());
        
        if ($result['success']) {
            return redirect($result['redirect_to'])
                ->with('success', $result['message']);
        } else {
            return redirect()->back()
                ->withInput($result['input'] ?? [])
                ->withErrors(['error' => $result['error']]);
        }
    }

    /**
     * Display the specified enrollment.
     */
    public function show(Enrollment $enrollment)
    {
        $enrollment = $this->enrollmentService->getEnrollment($enrollment->id);
        return view('enrollments.show', compact('enrollment'));
    }

    /**
     * Remove the specified enrollment from storage.
     */
    public function destroy(Enrollment $enrollment)
    {
        $result = $this->enrollmentService->handleEnrollmentDeletion($enrollment);
        
        if ($result['success']) {
            return redirect($result['redirect_to'])
                ->with('success', $result['message']);
        } else {
            return redirect()->back()
                ->withErrors(['error' => $result['error']]);
        }
    }
}
