<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_name',
        'class_code',
        'course_id',
        'category_id',
        'start_date',
        'end_date',
        'status',
        'default_price',
        'description',
        'max_students',
        'instructor_name',
        'is_active',
        'moodle_course_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'default_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function activeEnrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class)->where('is_active', true);
    }

    // Accessors & Mutators
    public function getClassFeeAttribute(): ?float
    {
        return $this->attributes['default_price'] ?? null;
    }

    public function setClassFeeAttribute($value): void
    {
        $this->attributes['default_price'] = $value;
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'registration' => 'قيد التسجيل (Registration)',
            'in_progress' => 'قيد التنفيذ (In Progress)',
            'completed' => 'منتهية (Completed)',
            default => $this->status
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'registration' => 'primary',
            'in_progress' => 'warning',
            'completed' => 'success',
            default => 'secondary'
        };
    }

    // Calculated attributes
    public function getTotalEnrolledStudentsAttribute(): int
    {
        return $this->enrollments()->where('is_active', true)->count();
    }

    public function getTotalRequiredAmountAttribute(): float
    {
        return $this->enrollments()->where('is_active', true)->sum('total_amount');
    }

    public function getTotalPaidAmountAttribute(): float
    {
        return $this->enrollments()->where('is_active', true)->sum('paid_amount');
    }

    public function getTotalDueAmountAttribute(): float
    {
        return $this->enrollments()->where('is_active', true)->sum('due_amount');
    }

    public function getCollectionRateAttribute(): float
    {
        $required = $this->total_required_amount;
        return $required > 0 ? ($this->total_paid_amount / $required) * 100 : 0;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByDepartment($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
