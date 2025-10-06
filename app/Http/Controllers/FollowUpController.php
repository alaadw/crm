<?php

namespace App\Http\Controllers;

use App\Models\FollowUp;
use App\Models\Student;
use App\Services\FollowUpService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FollowUpController extends Controller
{
    public function __construct(
        private FollowUpService $followUpService
    ) {}

    /**
     * Display today's follow-ups dashboard
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        
        $todayFollowUps = $this->followUpService->getTodayFollowUps($user);
        $overdueFollowUps = $this->followUpService->getOverdueFollowUps($user);
        $stats = $this->followUpService->getFollowUpStats($user);

        return view('follow-ups.index', compact(
            'todayFollowUps',
            'overdueFollowUps',
            'stats'
        ));
    }

    /**
     * Show form to create new follow-up
     */
    public function create(Request $request): View|RedirectResponse
    {
        $studentId = $request->get('student_id');
        
        // If no student_id provided, redirect to students list
        if (!$studentId) {
            return redirect()->route('students.index')
                ->with('error', 'Please select a student first to create a follow-up.');
        }
        
        $student = Student::findOrFail($studentId);

        $statuses = FollowUp::getStatuses();
        $outcomes = FollowUp::getOutcomes();
        $cancellationReasons = FollowUp::getCancellationReasons();
        $priorities = FollowUp::getPriorities();
        
        // Get courses for selection
        $courses = \App\Models\Course::where('is_active', true)
                                    ->orderBy('name_ar')
                                    ->get();

        return view('follow-ups.create', compact(
            'student',
            'statuses',
            'outcomes',
            'cancellationReasons',
            'priorities',
            'courses'
        ));
    }

    /**
     * Store new follow-up
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = $this->validateFollowUpData($request);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $student = Student::findOrFail($request->student_id);
            
            // Add user_id and default status to validated data
            $data = $validator->validated();
            $data['user_id'] = Auth::id() ?? 1; // Default to user ID 1 if no auth
            $data['status'] = 'pending'; // Default status for new follow-ups
            
            $followUp = FollowUp::create($data);

            return redirect()->route('students.show', $student)
                ->with('success', 'Follow-up scheduled successfully.');
                
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'An error occurred while creating the follow-up.'])
                ->withInput();
        }
    }

    /**
     * Display follow-up details
     */
    public function show(FollowUp $followUp): View
    {
        $followUp->load(['student', 'user', 'course']);
        
        return view('follow-ups.show', compact('followUp'));
    }

    /**
     * Show form to edit follow-up
     */
    public function edit(FollowUp $followUp): View
    {
        $followUp->load(['student', 'course']);
        
        $statuses = FollowUp::getStatuses();
        $outcomes = FollowUp::getOutcomes();
        $cancellationReasons = FollowUp::getCancellationReasons();
        $priorities = FollowUp::getPriorities();
        
        // Get courses for selection
        $courses = \App\Models\Course::where('is_active', true)
                                    ->orderBy('name_ar')
                                    ->get();

        return view('follow-ups.edit', compact(
            'followUp',
            'statuses',
            'outcomes',
            'cancellationReasons',
            'priorities',
            'courses'
        ));
    }

    /**
     * Update follow-up
     */
    public function update(Request $request, FollowUp $followUp): RedirectResponse
    {
        $validator = $this->validateFollowUpData($request, $followUp->id);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $data = $validator->validated();
            
            // Remove student_id from update data if it exists (shouldn't be changed)
            unset($data['student_id']);
            
            $followUp->update($data);

            return redirect()->route('students.show', $followUp->student)
                ->with('success', 'Follow-up updated successfully.');
                
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'An error occurred while updating the follow-up.'])
                ->withInput();
        }
    }

    /**
     * Quick add follow-up (AJAX)
     */
    public function quickAdd(Request $request)
    {
        $validator = $this->validateFollowUpData($request);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $student = Student::findOrFail($request->student_id);
            $followUp = $this->followUpService->createFollowUp($student, $validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Follow-up added successfully',
                'follow_up' => $followUp->load(['student', 'user']),
                'redirect' => $this->getRedirectRoute($followUp)
            ]);
            
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while creating the follow-up'], 500);
        }
    }

    /**
     * Department manager view
     */
    public function departmentView(Request $request): View
    {
        $user = Auth::user();
        
        if (!$user->isDepartmentManager() && !$user->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $department = $user->department;
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $userId = $request->get('user_id');
        $status = $request->get('status');

        $followUps = $this->followUpService->getDepartmentFollowUps(
            $department,
            $startDate,
            $endDate,
            $userId,
            $status
        );

        $teamStats = $this->followUpService->getTeamStats($department, $startDate, $endDate);
        $cancellationStats = $this->followUpService->getCancellationStats($department, $startDate, $endDate);

        $departmentUsers = \App\Models\User::active()
            ->byDepartment($department)
            ->get();

        return view('follow-ups.department', compact(
            'followUps',
            'teamStats',
            'cancellationStats',
            'departmentUsers',
            'department',
            'startDate',
            'endDate',
            'userId',
            'status'
        ));
    }

    /**
     * Get follow-ups for a specific status (AJAX)
     */
    public function getByStatus(Request $request)
    {
        $status = $request->get('status');
        $user = Auth::user();

        $query = FollowUp::with(['student', 'user'])
            ->byStatus($status);

        if ($user->isSalesRep()) {
            $query->byUser($user->id);
        } elseif ($user->isDepartmentManager()) {
            $query->byDepartment($user->department);
        }

        $followUps = $query->orderBy('created_at', 'desc')->get();

        return response()->json($followUps);
    }

    /**
     * Validate follow-up data
     */
    private function validateFollowUpData(Request $request, ?int $excludeId = null): \Illuminate\Validation\Validator
    {
        $rules = [
            'scheduled_date' => 'required|date',
            'contact_method' => 'required|in:phone,whatsapp,email,in_person',
            'type' => 'required|in:initial_contact,course_inquiry,payment_reminder,enrollment_follow_up,customer_service,other',
            'purpose' => 'required|string|max:1000',
            'notes' => 'nullable|string|max:1000',
            'priority' => 'required|in:high,medium,low',
            'course_id' => 'nullable|exists:courses,id',
            // Legacy fields for backward compatibility
            'action_note' => 'nullable|string|max:1000',
            'outcome' => 'nullable|in:' . implode(',', FollowUp::getOutcomes()),
            'status' => 'nullable|in:' . implode(',', array_keys(FollowUp::getStatuses())),
            'next_follow_up_date' => 'nullable|date|after_or_equal:today',
            'cancellation_reason' => 'nullable|in:' . implode(',', FollowUp::getCancellationReasons()),
            'cancellation_details' => 'nullable|string|max:500',
        ];

        // Only require student_id for new records
        if (!$excludeId) {
            $rules['student_id'] = 'required|exists:students,id';
        }

        return Validator::make($request->all(), $rules);
    }

    /**
     * Show all follow-ups for a specific student
     */
    public function studentFollowUps(Student $student): View
    {
        $followUps = $student->followUps()
            ->with('user')
            ->orderBy('scheduled_date', 'desc')
            ->paginate(15);

        return view('follow-ups.student', compact('student', 'followUps'));
    }

    /**
     * Mark follow-up as completed
     */
    public function complete(FollowUp $followUp): RedirectResponse
    {
        try {
            $followUp->update([
                'status' => 'completed',
                'action_note' => 'Follow-up completed on ' . now()->format('M j, Y g:i A')
            ]);

            return redirect()->route('students.show', $followUp->student)
                ->with('success', 'Follow-up marked as completed.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'An error occurred while updating the follow-up.']);
        }
    }

    /**
     * Cancel follow-up
     */
    public function cancel(FollowUp $followUp): RedirectResponse
    {
        try {
            $followUp->update([
                'status' => 'cancelled',
                'cancellation_reason' => 'Other',
                'cancellation_details' => 'Follow-up cancelled on ' . now()->format('M j, Y g:i A')
            ]);

            return redirect()->route('students.show', $followUp->student)
                ->with('success', 'Follow-up cancelled.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'An error occurred while cancelling the follow-up.']);
        }
    }

    /**
     * Validate follow-up data
    }

    /**
     * Determine redirect route based on follow-up status
     */
    private function getRedirectRoute(FollowUp $followUp): string
    {
        return match($followUp->status) {
            'Registered' => route('students.show', $followUp->student), // TODO: Change to enrollments when implemented
            'Cancelled' => route('students.show', $followUp->student),
            default => route('follow-ups.index')
        };
    }
}
