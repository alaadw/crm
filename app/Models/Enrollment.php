<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_class_id',
        'registered_by',
        'enrollment_date',
        'total_amount',
        'paid_amount',
        'due_amount',
        'payment_status',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'enrollment_date' => 'date',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function courseClass(): BelongsTo
    {
        return $this->belongsTo(CourseClass::class);
    }

    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // Accessors & Mutators
    public function getDueAmountAttribute($value): float
    {
        // Always calculate due amount dynamically to ensure accuracy
        return max(0, $this->total_amount - $this->paid_amount);
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return match($this->payment_status) {
            'not_paid' => 'لم يدفع',
            'partial' => 'متبقي',
            'completed' => 'مكتمل',
            default => $this->payment_status
        };
    }

    public function getPaymentStatusColorAttribute(): string
    {
        return match($this->payment_status) {
            'not_paid' => 'danger',
            'partial' => 'warning',
            'completed' => 'success',
            default => 'secondary'
        };
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByPaymentStatus($query, $status)
    {
        return $query->where('payment_status', $status);
    }

    public function scopeByEmployee($query, $userId)
    {
        return $query->where('registered_by', $userId);
    }
}
