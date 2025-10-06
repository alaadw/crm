<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Collection;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_en',
        'name_ar',
        'order',
        'status',
        'parent_id',
        'description',
        'keywords',
        'show',
        'name_alias',
        'img',
    ];

    protected $casts = [
        'status' => 'boolean',
        'show' => 'boolean',
        'parent_id' => 'integer',
    ];

    // Relationships
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'category_id');
    }

    public function courseClasses(): HasMany
    {
        return $this->hasMany(CourseClass::class, 'category_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'department_category_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'department_category_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeVisible($query)
    {
        return $query->where('show', 1);
    }

    public function scopeParents($query)
    {
        return $query->where('parent_id', 0);
    }

    public function scopeChildren($query)
    {
        return $query->where('parent_id', '>', 0);
    }

    public function scopeOrderedBySort($query)
    {
        return $query->orderBy('order')->orderBy('name_en');
    }

    // Accessors
    public function getNameAttribute(): string
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? ($this->name_ar ?? $this->name_en) : ($this->name_en ?? $this->name_ar);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->name;
    }

    public function getIsParentAttribute(): bool
    {
        return $this->parent_id == 0;
    }

    public function getHasChildrenAttribute(): bool
    {
        return $this->children()->count() > 0;
    }

    public function getFullPathAttribute(): string
    {
        if ($this->parent) {
            return $this->parent->name . ' > ' . $this->name;
        }
        return $this->name;
    }

    // Static methods
    public static function getDepartments(): Collection
    {
        return static::active()
            ->visible()
            ->parents()
            ->orderedBySort()
            ->get();
    }

    public static function getSubcategoriesByParent(int $parentId): Collection
    {
        return static::active()
            ->visible()
            ->where('parent_id', $parentId)
            ->orderedBySort()
            ->get();
    }

    // Helper methods
    public function getChildrenRecursive(): Collection
    {
        return $this->children()->with('children')->get();
    }

    public function getAllDescendants(): Collection
    {
        $descendants = collect();
        
        foreach ($this->children as $child) {
            $descendants->push($child);
            $descendants = $descendants->merge($child->getAllDescendants());
        }
        
        return $descendants;
    }
}
