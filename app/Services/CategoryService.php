<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    /**
     * Get all main departments (parent categories)
     */
    public function getDepartments(): Collection
    {
        return Category::where('parent_id', 0)
            ->where('status', 1)
            ->where('show', 1)
            ->orderBy('order')
            ->orderBy('name_en')
            ->get();
    }

    /**
     * Get subcategories by parent ID
     */
    public function getSubcategories(int $parentId): Collection
    {
        return Category::active()
            ->visible()
            ->where('parent_id', $parentId)
            ->orderedBySort()
            ->get();
    }

    /**
     * Get all categories as array for dropdowns
     */
    public function getDepartmentsArray(): array
    {
        return $this->getDepartments()
            ->pluck('name', 'id')
            ->toArray();
    }

    /**
     * Get all categories as structured array for forms
     */
    public function getDepartmentsForSelect(): array
    {
        return $this->getDepartments()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'name_en' => $category->name_en,
                ];
            })
            ->toArray();
    }

    /**
     * Get courses by category ID
     */
    public function getCoursesByCategory(int $categoryId): Collection
    {
        $category = Category::find($categoryId);
        
        if (!$category) {
            return collect();
        }

        return $category->courses()->active()->get();
    }

    /**
     * Get all courses grouped by main departments
     */
    public function getCoursesGroupedByDepartment(): array
    {
        $departments = $this->getDepartments();
        $grouped = [];

        foreach ($departments as $department) {
            $courses = collect();
            
            // Get courses directly assigned to this department
            $directCourses = $department->courses()->active()->get();
            $courses = $courses->merge($directCourses);
            
            // Get courses from subcategories
            foreach ($department->children as $subcategory) {
                $subcategoryCourses = $subcategory->courses()->active()->get();
                $courses = $courses->merge($subcategoryCourses);
            }
            
            if ($courses->count() > 0) {
                $grouped[$department->name] = $courses;
            }
        }

        return $grouped;
    }

    /**
     * Get department hierarchy for display
     */
    public function getDepartmentHierarchy(): array
    {
        $departments = $this->getDepartments();
        $hierarchy = [];

        foreach ($departments as $department) {
            $hierarchy[$department->id] = [
                'name' => $department->name,
                'name_en' => $department->name_en,
                'name_ar' => $department->name_ar,
                'children' => $department->children->map(function ($child) {
                    return [
                        'id' => $child->id,
                        'name' => $child->name,
                        'name_en' => $child->name_en,
                        'name_ar' => $child->name_ar,
                    ];
                })->toArray()
            ];
        }

        return $hierarchy;
    }

    /**
     * Get department ID by legacy department name (for backward compatibility)
     */
    public function getDepartmentIdByLegacyName(string $departmentName): ?int
    {
        $mapping = [
            'Management' => 33, // الإدارة والمحاسبة و الموارد البشرية
            'IT' => 56,         // تكنولوجيا المعلومات
            'Engineering' => 29, // الدورات الهندسية
            'English' => 47,     // اللغات
        ];

        return $mapping[$departmentName] ?? null;
    }

    /**
     * Get legacy department name by category ID (for backward compatibility)
     */
    public function getLegacyDepartmentName(int $categoryId): ?string
    {
        $mapping = [
            33 => 'Management',
            56 => 'IT',
            29 => 'Engineering',
            47 => 'English',
        ];

        return $mapping[$categoryId] ?? null;
    }

    /**
     * Search categories by name (bilingual)
     */
    public function searchCategories(string $query): Collection
    {
        return Category::active()
            ->visible()
            ->where(function ($q) use ($query) {
                $q->where('name_en', 'LIKE', "%{$query}%")
                  ->orWhere('name_ar', 'LIKE', "%{$query}%");
            })
            ->orderedBySort()
            ->get();
    }

    /**
     * Get all categories as tree structure
     */
    public function getCategoryTree(): array
    {
        $parents = $this->getDepartments();
        $tree = [];

        foreach ($parents as $parent) {
            $tree[] = [
                'id' => $parent->id,
                'name' => $parent->name,
                'name_en' => $parent->name_en,
                'name_ar' => $parent->name_ar,
                'children' => $parent->children->map(function ($child) {
                    return [
                        'id' => $child->id,
                        'name' => $child->name,
                        'name_en' => $child->name_en,
                        'name_ar' => $child->name_ar,
                        'parent_id' => $child->parent_id,
                    ];
                })->toArray()
            ];
        }

        return $tree;
    }
}