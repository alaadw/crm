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
        return $query->where('scheduled_date', '>=', Carbon::today())
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
        return match($this->status) {
            'No Follow-ups' => 'No Follow-ups',
            'Postponed' => 'Postponed',
            'Expected to Register' => 'Expected to Register',
            'Registered' => 'Registered',
            'Cancelled' => 'Cancelled',
            default => $this->status
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'No Follow-ups' => 'secondary',
            'Postponed' => 'warning',
            'Expected to Register' => 'success',
            'Registered' => 'primary',
            'Cancelled' => 'danger',
            default => 'secondary'
        };
    }

    // Static methods for filtering
    public static function getStatuses(): array
    {
        return [
            'pending' => 'Pending',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'postponed' => 'Postponed'
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
