<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class PaymentService
{
    public function baseQuery(): Builder
    {
        return Payment::query()->with([
            'enrollment.student.assignedUser',
            'enrollment.courseClass',
            'receivedBy',
            'currency',
        ]);
    }

    public function restrictToUser(Builder $query, ?User $user): Builder
    {
        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->isAdmin()) {
            return $query;
        }

        if ($user->isDepartmentManager()) {
            $managed = $user->managed_department_ids ?? [];
            if (!$managed) {
                return $query->whereRaw('1 = 0');
            }

            return $query->whereHas('enrollment.student', fn (Builder $students) => $students->whereIn('department_category_id', $managed));
        }

        if ($user->isSalesRep()) {
            return $query->whereHas('enrollment.student', fn (Builder $students) => $students->where('assigned_user_id', $user->id));
        }

        return $query->whereHas('enrollment', fn (Builder $enrollments) => $enrollments->where('registered_by', $user->id));
    }

    public function applyFilters(Builder $query, array $filters): Builder
    {
        $start = $filters['start_date'] ?? null;
        if ($start) {
            $query->whereDate('payment_date', '>=', $start);
        }

        $end = $filters['end_date'] ?? null;
        if ($end) {
            $query->whereDate('payment_date', '<=', $end);
        }

        $method = $filters['payment_method'] ?? null;
        if ($method) {
            $query->where('payment_method', $method);
        }

        $salesRep = $filters['sales_rep_id'] ?? null;
        if ($salesRep) {
            $query->whereHas('enrollment.student', fn (Builder $students) => $students->where('assigned_user_id', $salesRep));
        }

        return $query;
    }

    public function totalAmount(Builder $query): float
    {
        return (float) (clone $query)->sum('amount_in_jod');
    }

    public function collect(array $filters, ?User $user): Collection
    {
        $query = $this->restrictToUser($this->baseQuery(), $user);
        $query = $this->applyFilters($query, $filters);

        return $query
            ->orderByDesc('payment_date')
            ->get();
    }

    public function availableSalesReps(): Collection
    {
        return User::query()
            ->where('role', 'sales_rep')
            ->active()
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function chartByPeriod(string $period, int $year, int $month, Builder $query): array
    {
        if ($period === 'day') {
            return $this->chartByDay($year, $month, $query);
        }

        if ($period === 'week') {
            return $this->chartByWeek($year, $month, $query);
        }

        return $this->chartByMonth($year, $query);
    }

    public function chartByMethod(string $period, int $year, int $month, Builder $query): array
    {
        $range = $this->periodRange($period, $year, $month);
        $payments = (clone $query)
            ->whereBetween('payment_date', [$range['start'], $range['end']])
            ->get();

        return $payments
            ->groupBy('payment_method')
            ->map(fn ($group, $method) => [
                'label' => $this->methodLabel($method),
                'value' => (float) $group->sum('amount_in_jod'),
            ])
            ->values()
            ->all();
    }

    private function chartByDay(int $year, int $month, Builder $query): array
    {
        $firstDay = Carbon::create($year, $month, 1);
        $days = $firstDay->daysInMonth;

        return collect(range(1, $days))->map(function (int $day) use ($year, $month, $query) {
            $date = Carbon::create($year, $month, $day)->toDateString();
            $amount = (clone $query)->whereDate('payment_date', $date)->sum('amount_in_jod');

            return [
                'label' => $day,
                'value' => (float) $amount,
            ];
        })->all();
    }

    private function chartByWeek(int $year, int $month, Builder $query): array
    {
        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();
        $current = $start->copy();
        $week = 1;
        $data = [];

        while ($current <= $end) {
            $weekEnd = $current->copy()->addDays(6);
            if ($weekEnd > $end) {
                $weekEnd = $end->copy();
            }

            $amount = (clone $query)
                ->whereBetween('payment_date', [$current->toDateString(), $weekEnd->toDateString()])
                ->sum('amount_in_jod');

            $data[] = [
                'label' => trans('payments.week_label', ['week' => $week]),
                'value' => (float) $amount,
            ];

            $current = $weekEnd->copy()->addDay();
            $week++;
        }

        return $data;
    }

    private function chartByMonth(int $year, Builder $query): array
    {
        return collect(range(1, 12))->map(function (int $month) use ($year, $query) {
            $start = Carbon::create($year, $month, 1)->startOfMonth();
            $end = $start->copy()->endOfMonth();
            $amount = (clone $query)
                ->whereBetween('payment_date', [$start->toDateString(), $end->toDateString()])
                ->sum('amount_in_jod');

            return [
                'label' => $start->format('M'),
                'value' => (float) $amount,
            ];
        })->all();
    }

    private function periodRange(string $period, int $year, int $month): array
    {
        if ($period === 'month') {
            return [
                'start' => Carbon::create($year, 1, 1)->startOfYear()->toDateString(),
                'end' => Carbon::create($year, 12, 31)->endOfYear()->toDateString(),
            ];
        }

        $base = Carbon::create($year, $month, 1);

        return [
            'start' => $base->copy()->startOfMonth()->toDateString(),
            'end' => $base->copy()->endOfMonth()->toDateString(),
        ];
    }

    public function methodLabel(?string $method): string
    {
        if (!$method) {
            return trans('payments.other');
        }

        $label = trans("payments.$method");
        return $label === "payments.$method" ? ucfirst(str_replace('_', ' ', $method)) : $label;
    }
}
