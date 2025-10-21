<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class FollowUp extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'user_id',
        'scheduled_date',
        'contact_method',
        'type',
        'purpose',
        'notes',
        'action_note',
        'outcome',
        'status',
        'next_follow_up_date',
        'cancellation_reason',
        'cancellation_details',
        'priority',
    ];

    protected $casts = [
        'scheduled_date' => 'datetime',
        'next_follow_up_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeDueToday($query)
    {
        return $query->where('scheduled_date', '>=', Carbon::today()->subDay())
                    ->where('scheduled_date', '<', Carbon::tomorrow())
                    ->where('status', 'pending');
    }

    public function scopeOverdue($query)
    {
        return $query->where('scheduled_date', '<', Carbon::today())
                    ->where('status', 'pending');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByDepartment($query, string $department)
    {
        return $query->whereHas('student', function ($q) use ($department) {
            $q->where('department', $department);
        });
    }

    public function scopeByDepartmentCategories($query, array $categoryIds)
    {
        if (empty($categoryIds)) {
            return $query->whereRaw('1=0');
        }
        return $query->whereHas('student', function ($q) use ($categoryIds) {
            $q->whereIn('department_category_id', $categoryIds);
        });
    }

    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    // Accessors
    public function getIsOverdueAttribute(): bool
    {
        return $this->next_follow_up_date && 
               $this->next_follow_up_date->lt(Carbon::today()) &&
               in_array($this->status, ['Postponed', 'Expected to Register']);
    }

    public function getIsDueTodayAttribute(): bool
    {
        return $this->next_follow_up_date && 
               $this->next_follow_up_date->isToday() &&
               in_array($this->status, ['Postponed', 'Expected to Register']);
    }

    public function getPriorityLabelAttribute(): string
    {
        return match($this->priority) {
            'high' => 'High',
            'medium' => 'Medium',
            'low' => 'Low',
            default => 'Medium'
        };
    }

    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'high' => 'danger',
            'medium' => 'warning',
            'low' => 'secondary',
            default => 'warning'
        };
    }

    public function getStatusLabelAttribute(): string
    {
        $map = [
            'pending' => 'Pending',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'postponed' => 'Postponed',
            'expected' => 'Expected to Register',
            'registered' => 'Registered',
            'no_follow_ups' => 'No Follow-ups',
        ];
        $key = $this->normalizeStatus($this->status);
        return $map[$key] ?? ($this->status ?: '');
    }

    public function getStatusColorAttribute(): string
    {
        $key = $this->normalizeStatus($this->status);
        $colors = [
            'pending' => 'secondary',
            'completed' => 'success',
            'cancelled' => 'danger',
            'postponed' => 'warning',
            'expected' => 'success',
            'registered' => 'primary',
            'no_follow_ups' => 'secondary',
        ];
        return $colors[$key] ?? 'secondary';
    }

    /**
     * Canonical normalized key for status (for form bindings)
     */
    public function getStatusKeyAttribute(): string
    {
        return $this->normalizeStatus($this->attributes['status'] ?? '');
    }

    /**
     * Mutator to normalize status values on write
     */
    public function setStatusAttribute($value): void
    {
        $this->attributes['status'] = $this->normalizeStatus((string) $value);
    }

    /**
     * Normalize status to canonical lowercase keys
     */
    protected function normalizeStatus(?string $status): string
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

    // Static methods for filtering
    public static function getStatuses(): array
    {
        return [
            'pending' => 'Pending',
            'postponed' => 'Postponed',
            'expected' => 'Expected to Register',
            'registered' => 'Registered',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];
    }

    public static function getOutcomes(): array
    {
        return [
            'Interested',
            'No Answer',
            'Wrong Number',
            'Not Interested',
            'Callback Requested',
            'Other'
        ];
    }

    public static function getCancellationReasons(): array
    {
        return [
            'Price',
            'Time',
            'Registered Elsewhere',
            'Not Interested',
            'Other'
        ];
    }

    public static function getPriorities(): array
    {
        return [
            'high' => 'High',
            'medium' => 'Medium',
            'low' => 'Low'
        ];
    }
}
