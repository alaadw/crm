<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_ar',
        'name_en',
        'code',
        'description',
        'description_ar',
        'description_en',
        'department',
        'category_id',
        'is_active',
        'moodle_course_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'preferred_course_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeByDepartment($query, string $department)
    {
        return $query->where('department', $department);
    }

    // Accessors for bilingual support
    public function getDisplayNameAttribute(): string
    {
        $locale = app()->getLocale();
        
        if ($locale === 'ar') {
            return $this->name_ar ?? $this->name_en ?? $this->name;
        }
        
        return $this->name_en ?? $this->name_ar ?? $this->name;
    }

    public function getDisplayDescriptionAttribute(): ?string
    {
        $locale = app()->getLocale();
        
        if ($locale === 'ar') {
            return $this->description_ar ?? $this->description_en ?? $this->description;
        }
        
        return $this->description_en ?? $this->description_ar ?? $this->description;
    }

    public function getDepartmentNameAttribute(): string
    {
        if ($this->category) {
            return $this->category->name;
        }
        
        return $this->department ?? '';
    }

    public function getFullNameAttribute(): string
    {
        $categoryName = $this->category ? $this->category->name . ' - ' : '';
        return $categoryName . $this->display_name;
    }
}
