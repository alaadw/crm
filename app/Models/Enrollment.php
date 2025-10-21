<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Enrollment extends Model
{
    use HasFactory;

    /**
     * Small tolerance for floating point/rounding differences (in JOD)
     */
    public const EPSILON = 0.01;

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
        'moodle_sync_status',
        'moodle_enrolled_at',
        'moodle_last_error',
    ];

    protected $casts = [
        'enrollment_date' => 'date',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
        'is_active' => 'boolean',
        'moodle_enrolled_at' => 'datetime',
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

    /**
     * Recalculate paid_amount, due_amount, and payment_status from payments (JOD-based)
     *
     * - Sums payments.amount_in_jod
     * - Applies rounding and epsilon tolerance to avoid false "partial" due to tiny differences
     *
     * @param bool $save Persist recalculated values when true
     * @return $this
     */
    public function recalcFromPayments(bool $save = true): self
    {
        // Sum all payments in JOD
        $paidJod = (float) $this->payments()->sum('amount_in_jod');
        $paidRounded = round($paidJod, 2);
        $totalRounded = round((float) $this->total_amount, 2);

        // Compute due using rounding and clamp to zero
        $due = max(0.0, round($totalRounded - $paidRounded, 2));

        // Determine status with epsilon tolerance
        if ($paidRounded <= self::EPSILON) {
            $status = 'not_paid';
        } elseif (abs($totalRounded - $paidRounded) <= self::EPSILON || $paidRounded > $totalRounded) {
            // Consider as completed when within epsilon or overpaid
            $status = 'completed';
        } else {
            $status = 'partial';
        }

        // Optionally persist
        if ($save) {
            $this->paid_amount = $paidRounded;
            $this->due_amount = $due; // persisted for reporting; accessor also guards to zero
            $this->payment_status = $status;
            $this->save();
        } else {
            // Keep values in-memory for immediate use
            $this->setRawAttributes(array_merge($this->getAttributes(), [
                'paid_amount' => $paidRounded,
                'due_amount' => $due,
                'payment_status' => $status,
            ]));
        }

        return $this;
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
