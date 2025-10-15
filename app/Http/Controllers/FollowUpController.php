<?php

namespace App\Http\Controllers;

use App\Models\FollowUp;
use App\Models\Student;
use App\Services\FollowUpService;
use App\Http\Requests\StoreFollowUpRequest;
use App\Http\Requests\UpdateFollowUpRequest;
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

        // Departments for quick add modal dependent course select
        $departments = \App\Models\Category::getDepartments();

        return view('follow-ups.index', compact(
            'todayFollowUps',
            'overdueFollowUps',
            'stats',
            'departments'
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
        
    // Departments (top-level categories) for dependent course select
    $departments = \App\Models\Category::getDepartments();

        return view('follow-ups.create', compact(
            'student',
            'statuses',
            'outcomes',
            'cancellationReasons',
            'priorities',
            'departments'
        ));
    }

    /**
     * Store new follow-up
     */
    public function store(StoreFollowUpRequest $request): RedirectResponse
    {
        try {
            $student = Student::findOrFail($request->student_id);
            
            // Add user_id and default status to validated data
            $data = $request->validated();
            $data['user_id'] = Auth::id() ?? 1; // Default to user ID 1 if no auth
            $data['status'] = 'pending'; // Default status for new follow-ups
            
            $followUp = FollowUp::create($data);

            return redirect()->route('students.show', $student)
                ->with('success', __('follow_ups.follow_up_created'));
                
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
        
        // Departments for dependent course select and selected department
        $departments = \App\Models\Category::getDepartments();
        $selectedDepartmentId = $followUp->course?->category_id;

        return view('follow-ups.edit', compact(
            'followUp',
            'statuses',
            'outcomes',
            'cancellationReasons',
            'priorities',
            'departments',
            'selectedDepartmentId'
        ));
    }

    /**
     * Update follow-up
     */
    public function update(UpdateFollowUpRequest $request, FollowUp $followUp): RedirectResponse
    {
        try {
            $data = $request->validated();
            
            // Remove student_id from update data if it exists (shouldn't be changed)
            unset($data['student_id']);
            
            $followUp->update($data);

            return redirect()->route('students.show', $followUp->student)
                ->with('success', __('follow_ups.follow_up_updated'));
                
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => __('common.error_occurred')])
                ->withInput();
        }
    }

    /**
     * Quick add follow-up (AJAX)
     */
    public function quickAdd(StoreFollowUpRequest $request)
    {
        try {
            $student = Student::findOrFail($request->student_id);
            $followUp = $this->followUpService->createFollowUp($student, $request->validated());

            return response()->json([
                'success' => true,
                'message' => __('follow_ups.follow_up_added'),
                'follow_up' => $followUp->load(['student', 'user']),
                'redirect' => $this->getRedirectRoute($followUp)
            ]);
            
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => __('common.error_occurred')], 500);
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
