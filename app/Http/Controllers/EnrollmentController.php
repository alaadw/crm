<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Enrollment;
use App\Services\EnrollmentService;
use App\Models\Currency;
use App\Services\MoodleService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
        
        // Get active currencies
        $currencies = Currency::getActiveCurrencies();

        return view('enrollments.create', compact('student', 'departments', 'courseClassesData', 'currencies'));
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

    /**
     * Sync an enrollment's student with Moodle and enroll into linked course.
     */
    public function syncWithMoodle(Request $request, Enrollment $enrollment, MoodleService $moodleService): JsonResponse
    {
        $enrollment->loadMissing('student', 'courseClass.course');

        if (!$enrollment->is_active) {
            return response()->json([
                'success' => false,
                'message' => __('classes.moodle_sync_inactive_enrollment'),
            ], 422);
        }

        $courseClass = $enrollment->courseClass;
        $courseId = $courseClass?->moodle_course_id ?? $courseClass?->course?->moodle_course_id;

        if (!$courseId) {
            return response()->json([
                'success' => false,
                'message' => __('classes.moodle_sync_missing_course'),
            ], 422);
        }

        $student = $enrollment->student;

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => __('classes.moodle_sync_missing_student'),
            ], 404);
        }

        if (!$student->email) {
            return response()->json([
                'success' => false,
                'message' => __('classes.moodle_sync_missing_email'),
            ], 422);
        }

        $fullName = trim($student->full_name ?? $student->full_name_en ?? $student->name ?? '');
        $nameParts = preg_split('/\s+/', $fullName) ?: [];
        $firstName = $nameParts[0] ?? ($student->full_name ?? 'Student');
        $lastName = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : ($student->full_name_en ?? 'Learner');

        if (!trim($lastName)) {
            $lastName = 'Learner';
        }

        $username = strtolower(trim($student->email));
        $password = $this->generateMoodlePassword($student);

        $enrollment->update([
            'moodle_sync_status' => 'syncing',
            'moodle_last_error' => null,
        ]);

        try {
            $moodleUser = $moodleService->createUser([
                'username' => $username,
                'email' => trim($student->email),
                'firstname' => $firstName,
                'lastname' => $lastName,
                'password' => $password,
            ]);

            try {
                $moodleService->enrolUser($moodleUser['id'], $courseId);
            } catch (\Exception $enrolException) {
                $message = strtolower($enrolException->getMessage());
                if (!Str::contains($message, 'already') && !Str::contains($message, 'duplicate')) {
                    throw $enrolException;
                }
            }

            DB::transaction(function () use ($student, $enrollment, $moodleUser) {
                $student->moodle_user_id = $moodleUser['id'];
                $student->moodle_user_synced_at = now();
                $student->save();

                $enrollment->moodle_sync_status = 'synced';
                $enrollment->moodle_enrolled_at = now();
                $enrollment->moodle_last_error = null;
                $enrollment->save();
            });

            $enrollment->refresh();

            return response()->json([
                'success' => true,
                'message' => $moodleUser['exists']
                    ? __('classes.moodle_sync_existing_user')
                    : __('classes.moodle_sync_created_user'),
                'data' => [
                    'status' => $enrollment->moodle_sync_status,
                    'synced_at' => optional($enrollment->moodle_enrolled_at)->toDateTimeString(),
                    'moodle_user_id' => $student->moodle_user_id,
                    'user_exists' => $moodleUser['exists'],
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('Moodle sync failed', [
                'enrollment_id' => $enrollment->id,
                'student_id' => $enrollment->student_id,
                'class_id' => $enrollment->course_class_id,
                'error' => $e->getMessage(),
            ]);

            try {
                $enrollment->update([
                    'moodle_sync_status' => 'failed',
                    'moodle_last_error' => $e->getMessage(),
                ]);
            } catch (\Throwable $persistException) {
                Log::warning('Unable to persist Moodle sync failure state', [
                    'enrollment_id' => $enrollment->id,
                    'error' => $persistException->getMessage(),
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => __('classes.moodle_sync_failed'),
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    private function generateMoodlePassword(Student $student): string
    {
        $digits = preg_replace('/\D+/', '', $student->phone_primary ?? '');
        $suffix = strlen($digits) >= 4 ? substr($digits, -4) : strtoupper(Str::random(4));

        return 'Hcrm@' . $suffix;
    }
}
