<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Services\PhoneService;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'full_name',
        'full_name_en',
        'email',
        'phone_primary',
        'phone_alt',
        'country_code',
        'reach_source',
        'department',
        'department_category_id',
        'preferred_course_id',
        'university',
        'major',
        'college',
        'notes',
        'moodle_user_id',
        'moodle_user_synced_at',
        'assigned_user_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'moodle_user_synced_at' => 'datetime',
    ];

    public function preferredCourse(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'preferred_course_id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function departmentCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'department_category_id');
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(FollowUp::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function activeEnrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class)->where('is_active', true);
    }

    public function latestFollowUp(): HasOne
    {
        return $this->hasOne(FollowUp::class)->latest();
    }

    public function activeFollowUps(): HasMany
    {
        return $this->followUps()->active();
    }

    public function scopeByPhone($query, string $phone)
    {
        return $query->where('phone_primary', $phone);
    }

    public function scopeByStudentId($query, string $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeByDepartment($query, string $department)
    {
        return $query->where('department', $department);
    }

    public function scopeByDepartmentCategories($query, array $categoryIds)
    {
        if (empty($categoryIds)) {
            return $query->whereRaw('1=0');
        }
        return $query->whereIn('department_category_id', $categoryIds);
    }

    public function scopeWithActiveFollowUps($query)
    {
        return $query->whereHas('followUps', function ($q) {
            $q->active();
        });
    }

    // Get current status based on latest follow-up
    public function getCurrentStatusAttribute(): string
    {
        $latestFollowUp = $this->followUps()->latest()->first();
        
        if (!$latestFollowUp) {
            return 'No Follow-ups';
        }

        // If the latest follow-up is Registered or Cancelled, check if there's a newer active one
        if (in_array($latestFollowUp->status, ['Registered', 'Cancelled'])) {
            $newerActiveFollowUp = $this->followUps()
                ->where('created_at', '>', $latestFollowUp->created_at)
                ->whereIn('status', ['Postponed', 'Expected to Register'])
                ->latest()
                ->first();
                
            if ($newerActiveFollowUp) {
                return $newerActiveFollowUp->status;
            }
        }

        return $latestFollowUp->status;
    }

    // Check if student needs follow-up today
    public function getNeedsFollowUpTodayAttribute(): bool
    {
        return $this->followUps()
            ->dueToday()
            ->exists();
    }

    // Check if student has overdue follow-ups
    public function getHasOverdueFollowUpAttribute(): bool
    {
        return $this->followUps()
            ->overdue()
            ->exists();
    }

    // Get next follow-up date
    public function getNextFollowUpDateAttribute(): ?string
    {
        $followUp = $this->followUps()
            ->active()
            ->whereNotNull('next_follow_up_date')
            ->orderBy('next_follow_up_date')
            ->first();

        return $followUp?->next_follow_up_date?->format('Y-m-d');
    }

    // Get latest follow-up note
    public function getLatestFollowUpNoteAttribute(): ?string
    {
        return $this->followUps()
            ->latest()
            ->first()
            ?->action_note;
    }

    // Get department name (bilingual)
    public function getDepartmentNameAttribute(): string
    {
        if ($this->departmentCategory) {
            return $this->departmentCategory->name;
        }
        
        return $this->department ?? '';
    }

    // Accessor for name (maps to full_name for backward compatibility)
    public function getNameAttribute(): string
    {
        return $this->full_name ?? '';
    }

    public function getFormattedPhonePrimaryAttribute(): string
    {
        return app(PhoneService::class)->formatForDisplay($this->phone_primary);
    }

    public function getFormattedPhoneAltAttribute(): ?string
    {
        if (!$this->phone_alt) {
            return null;
        }
        return app(PhoneService::class)->formatForDisplay($this->phone_alt);
    }

    // Localized label for reach_source (keeps stored enum values intact)
    public function getReachSourceLabelAttribute(): string
    {
        $value = $this->reach_source ?? '';
        if ($value === '') {
            return '';
        }
        $key = strtolower(str_replace(' ', '_', $value));
        $label = __("students.$key");
        return $label === "students.$key" ? $value : $label;
    }
}
