<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'department',
        'department_category_id',
        'managed_departments',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'managed_departments' => 'array',
        ];
    }

    /**
     * Normalized list of managed department category IDs as integers.
     */
    public function getManagedDepartmentIdsAttribute(): array
    {
        $val = $this->managed_departments;
        if (is_null($val)) {
            // Fallback to single department or category field for legacy
            if (!empty($this->department_category_id)) {
                return [(int) $this->department_category_id];
            }
            if (!empty($this->department) && is_numeric($this->department)) {
                return [(int) $this->department];
            }
            if (is_string($this->department) && preg_match('/[,\s]/', $this->department)) {
                return array_values(array_filter(array_map('intval', preg_split('/[\s,]+/', $this->department))));
            }
            return [];
        }
        if (is_array($val)) {
            return array_values(array_unique(array_map('intval', $val)));
        }
        if (is_string($val)) {
            $val = trim($val);
            if ($val === '') return [];
            $decoded = json_decode($val, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return array_values(array_unique(array_map('intval', $decoded)));
            }
            return array_values(array_unique(array_map('intval', preg_split('/[\s,]+/', $val))));
        }
        return [];
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(FollowUp::class);
    }

    public function studentsRegistered(): HasMany
    {
        return $this->hasMany(Student::class, 'registered_by_user_id');
    }

    public function departmentCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'department_category_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isDepartmentManager(): bool
    {
        return $this->role === 'department_manager';
    }

    public function isSalesRep(): bool
    {
        return $this->role === 'sales_rep';
    }

    public function canViewStudent(Student $student): bool
    {
        if ($this->isAdmin()) {
            return true;
        }
        
        if ($this->isDepartmentManager()) {
            return $student->department === $this->department;
        }
        
        // Sales rep can view students they created or have follow-ups for
        return $student->followUps()->where('user_id', $this->id)->exists();
    }

    public function scopeByDepartment($query, string $department)
    {
        return $query->where('department', $department);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Get department name (bilingual)
    public function getDepartmentNameAttribute(): string
    {
        if ($this->departmentCategory) {
            return $this->departmentCategory->name;
        }
        
        return $this->department ?? '';
    }
}
