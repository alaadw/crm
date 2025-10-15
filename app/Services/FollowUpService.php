<?php

namespace App\Services;

use App\Models\FollowUp;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class FollowUpService
{
    /**
     * Get today's follow-ups for a user
     */
    public function getTodayFollowUps(?User $user = null): Collection
    {
        $user = $user ?? Auth::user();
        
        $query = FollowUp::with(['student', 'user', 'course'])
            ->dueToday()
            ->orderByRaw("CASE WHEN status = 'pending' THEN 1 ELSE 2 END")
            ->orderBy('priority')
            ->orderBy('scheduled_date')
            ->orderBy('created_at');

        // Only apply user filters if user is authenticated and has role methods
        if ($user && method_exists($user, 'isSalesRep')) {
            if ($user->isSalesRep()) {
                $query->byUser($user->id);
            } elseif ($user->isDepartmentManager()) {
                $query->byDepartment($user->department);
            }
        }

        return $query->get();
    }

    /**
     * Get overdue follow-ups for a user
     */
    public function getOverdueFollowUps(?User $user = null): Collection
    {
        $user = $user ?? Auth::user();
        
        $query = FollowUp::with(['student', 'user', 'course'])
            ->overdue()
            ->orderByRaw("CASE WHEN status = 'pending' THEN 1 ELSE 2 END")
            ->orderBy('priority')
            ->orderBy('scheduled_date')
            ->orderBy('created_at');

        // Only apply user filters if user is authenticated and has role methods
        if ($user && method_exists($user, 'isSalesRep')) {
            if ($user->isSalesRep()) {
                $query->byUser($user->id);
            } elseif ($user->isDepartmentManager()) {
                $query->byDepartment($user->department);
            }
        }

        return $query->get();
    }

    /**
     * Get follow-up statistics for dashboard
     */
    public function getFollowUpStats(?User $user = null, ?string $startDate = null, ?string $endDate = null): array
    {
        $user = $user ?? Auth::user();
        $startDate = $startDate ?? Carbon::now()->startOfMonth()->toDateString();
        $endDate = $endDate ?? Carbon::now()->endOfMonth()->toDateString();

        $query = FollowUp::query();

        // Only apply user filters if user is authenticated and has role methods
        if ($user && method_exists($user, 'isSalesRep')) {
            if ($user->isSalesRep()) {
                $query->byUser($user->id);
            } elseif ($user->isDepartmentManager()) {
                $query->byDepartment($user->department);
            }
        }

        $todayCount = (clone $query)->dueToday()->count();
        $overdueCount = (clone $query)->overdue()->count();
        //clone means create a copy of the query to avoid modifying the original query
        // Build normalized status counts so UI doesn't depend on exact casing/phrasing
        $statuses = (clone $query)
            ->inDateRange($startDate, $endDate)
            ->pluck('status');

        $statusCounts = [];
        foreach ($statuses as $status) {
            $key = $this->normalizeStatusKey($status);
            if ($key === '') { continue; }
            $statusCounts[$key] = ($statusCounts[$key] ?? 0) + 1;
        }

        return [
            'today' => $todayCount,
            'overdue' => $overdueCount,
            'by_status' => $statusCounts,
            'total_active' => $todayCount + $overdueCount,
        ];
    }

    /**
     * Normalize various status values (legacy and current) to canonical keys
     */
    private function normalizeStatusKey(?string $status): string
    {
        $s = trim(strtolower((string) $status));
        return match($s) {
            'postponed' => 'postponed',
            'expected', 'expected to register' => 'expected',
            'registered' => 'registered',
            'cancelled' => 'cancelled',
            'completed' => 'completed',
            'pending' => 'pending',
            'no follow-ups', 'no_follow_ups' => 'no_follow_ups',
            default => $s,
        };
    }

    /**
     * Create a new follow-up
     */
    public function createFollowUp(Student $student, array $data, ?User $user = null): FollowUp
    {
        $user = $user ?? Auth::user();

        // Validate required fields based on status
        $this->validateFollowUpData($data);

        $followUpData = array_merge($data, [
            'student_id' => $student->id,
            'user_id' => $user->id,
        ]);

        $followUp = FollowUp::create($followUpData);

        // Handle status transitions
        $this->handleStatusTransition($followUp);

        return $followUp;
    }

    /**
     * Update existing follow-up
     */
    public function updateFollowUp(FollowUp $followUp, array $data): FollowUp
    {
        $this->validateFollowUpData($data);

        $followUp->update($data);

        $this->handleStatusTransition($followUp);

        return $followUp->fresh();
    }

    /**
     * Get follow-ups for department manager view
     */
    public function getDepartmentFollowUps(
        string $department,
        ?string $startDate = null,
        ?string $endDate = null,
        ?int $userId = null,
        ?string $status = null
    ): Collection {
        $startDate = $startDate ?? Carbon::now()->startOfMonth()->toDateString();
        $endDate = $endDate ?? Carbon::now()->endOfMonth()->toDateString();

        $query = FollowUp::with(['student', 'user'])
            ->byDepartment($department)
            ->inDateRange($startDate, $endDate);

        if ($userId) {
            $query->byUser($userId);
        }

        if ($status) {
            $query->byStatus($status);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get team performance stats for department manager
     */
    public function getTeamStats(
        string $department,
        ?string $startDate = null,
        ?string $endDate = null
    ): array {
        $startDate = $startDate ?? Carbon::now()->startOfMonth()->toDateString();
        $endDate = $endDate ?? Carbon::now()->endOfMonth()->toDateString();

        $users = User::active()
            ->byDepartment($department)
            ->with(['followUps' => function ($query) use ($startDate, $endDate) {
                $query->inDateRange($startDate, $endDate);
            }])
            ->get();

        $teamStats = [];

        foreach ($users as $user) {
            $todayFollowUps = FollowUp::byUser($user->id)->dueToday()->count();
            $overdueFollowUps = FollowUp::byUser($user->id)->overdue()->count();

            $statusCounts = $user->followUps
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('status')
                ->map(function ($group) {
                    return $group->count();
                })
                ->toArray();

            $teamStats[] = [
                'user' => $user,
                'today_count' => $todayFollowUps,
                'overdue_count' => $overdueFollowUps,
                'status_counts' => $statusCounts,
                'last_activity' => $user->followUps()->latest()->first()?->created_at,
            ];
        }

        return $teamStats;
    }

    /**
     * Get students requiring follow-up action
     */
    public function getStudentsRequiringAction(?User $user = null): Collection
    {
        $user = $user ?? Auth::user();

        $query = Student::with(['latestFollowUp', 'preferredCourse'])
            ->withActiveFollowUps();

        if ($user->isSalesRep()) {
            $query->whereHas('followUps', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        } elseif ($user->isDepartmentManager()) {
            $query->byDepartment($user->department);
        }

        return $query->get()->filter(function ($student) {
            return $student->needs_follow_up_today || $student->has_overdue_follow_up;
        });
    }

    /**
     * Validate follow-up data based on business rules
     */
    private function validateFollowUpData(array $data): void
    {
        $status = $data['status'] ?? null;

        // Postponed requires next_follow_up_date
        if ($status === 'Postponed' && empty($data['next_follow_up_date'])) {
            throw new \InvalidArgumentException('Next follow-up date is required for postponed status');
        }

        // Cancelled requires cancellation_reason
        if ($status === 'Cancelled' && empty($data['cancellation_reason'])) {
            throw new \InvalidArgumentException('Cancellation reason is required for cancelled status');
        }

        // Expected to Register should have next_follow_up_date (recommended)
        if ($status === 'Expected to Register' && empty($data['next_follow_up_date'])) {
            // This is a warning, not an error - business rule says it's recommended
        }
    }

    /**
     * Handle status transitions and business logic
     */
    private function handleStatusTransition(FollowUp $followUp): void
    {
        // Future: Handle automatic transitions to enrollment screen for "Registered" status
        // Future: Handle automatic target calculations
        
        if ($followUp->status === 'Registered') {
            // TODO: Redirect to enrollment screen
            // TODO: Update sales target calculations
        }
    }

    /**
     * Get cancellation reasons statistics
     */
    public function getCancellationStats(
        ?string $department = null,
        ?string $startDate = null,
        ?string $endDate = null
    ): array {
        $startDate = $startDate ?? Carbon::now()->startOfMonth()->toDateString();
        $endDate = $endDate ?? Carbon::now()->endOfMonth()->toDateString();

        $query = FollowUp::byStatus('Cancelled')
            ->inDateRange($startDate, $endDate);

        if ($department) {
            $query->byDepartment($department);
        }

        return $query
            ->selectRaw('cancellation_reason, COUNT(*) as count')
            ->groupBy('cancellation_reason')
            ->pluck('count', 'cancellation_reason')
            ->toArray();
    }
}