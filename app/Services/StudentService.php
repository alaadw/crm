<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Course;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class StudentService
{
    public function __construct(
        private PhoneService $phoneService,
        private CategoryService $categoryService
    ) {}

    /**
     * Search for student by phone number or student ID
     */
    public function findStudent(string $searchTerm): ?Student
    {
        // Try to find by student ID first
        $student = Student::byStudentId($searchTerm)->first();
        
        if ($student) {
            return $student;
        }

        // Normalize phone number and search
        $normalizedPhone = $this->phoneService->normalizePhone($searchTerm);
        
        if ($normalizedPhone) {
            return Student::byPhone($normalizedPhone)->first();
        }

        return null;
    }

    /**
     * Create new student
     */
    public function createStudent(array $data): Student
    {
        $normalizedPhone = $this->phoneService->normalizePhone($data['phone_primary'], $data['country_code'] ?? null);
        
        if (!$normalizedPhone) {
            throw new \InvalidArgumentException('Invalid phone number format');
        }

        // Check if student with this phone already exists
        $existingStudent = Student::byPhone($normalizedPhone)->first();
        
        if ($existingStudent) {
            throw new \InvalidArgumentException('Student with this phone number already exists');
        }

        // Handle department category mapping
        if (isset($data['department'])) {
            if (is_numeric($data['department'])) {
                // New system: department contains category ID
                $data['department_category_id'] = (int)$data['department'];
                unset($data['department']); // Remove the department field as we're using department_category_id
            } else {
                // Legacy system: department contains name, map to category ID
                $data['department_category_id'] = $this->categoryService->getDepartmentIdByLegacyName($data['department']);
            }
        }

        $studentData = array_merge($data, [
            'student_id' => $this->generateStudentId($normalizedPhone),
            'phone_primary' => $normalizedPhone,
            'country_code' => $this->phoneService->extractCountryCode($normalizedPhone),
        ]);

        return Student::create($studentData);
    }

    /**
     * Update student information
     */
    public function updateStudent(Student $student, array $data): Student
    {
        // Handle phone number update
        if (isset($data['phone_primary']) && $data['phone_primary'] !== $student->phone_primary) {
            $normalizedPhone = $this->phoneService->normalizePhone($data['phone_primary'], $data['country_code'] ?? null);
            
            if (!$normalizedPhone) {
                throw new \InvalidArgumentException('Invalid phone number format');
            }

            // Check if another student has this phone
            $existingStudent = Student::byPhone($normalizedPhone)
                ->where('id', '!=', $student->id)
                ->first();
                
            if ($existingStudent) {
                throw new \InvalidArgumentException('Another student already has this phone number');
            }

            // Store old phone as alternative if it's not already set
            if (!$student->phone_alt) {
                $data['phone_alt'] = $student->phone_primary;
            }

            $data['phone_primary'] = $normalizedPhone;
            $data['country_code'] = $this->phoneService->extractCountryCode($normalizedPhone);
        }

        // Handle department category mapping
        if (isset($data['department'])) {
            if (is_numeric($data['department'])) {
                // New system: department contains category ID
                $data['department_category_id'] = (int)$data['department'];
                unset($data['department']); // Remove the department field as we're using department_category_id
            } else {
                // Legacy system: department contains name, map to category ID
                $data['department_category_id'] = $this->categoryService->getDepartmentIdByLegacyName($data['department']);
            }
        }

        $student->update($data);
        return $student->fresh();
    }

    /**
     * Get students by department category
     */
    public function getStudentsByDepartment(int $departmentCategoryId): Collection
    {
        return Student::where('department_category_id', $departmentCategoryId)
            ->orWhereHas('departmentCategory', function ($query) use ($departmentCategoryId) {
                $query->where('parent_id', $departmentCategoryId);
            })
            ->with(['preferredCourse', 'departmentCategory'])
            ->orderBy('full_name')
            ->get();
    }

    /**
     * Get students by legacy department name (backward compatibility)
     */
    public function getStudentsByLegacyDepartment(string $department): Collection
    {
        $categoryId = $this->categoryService->getDepartmentIdByLegacyName($department);
        
        if ($categoryId) {
            return $this->getStudentsByDepartment($categoryId);
        }

        return Student::byDepartment($department)
            ->with(['preferredCourse', 'departmentCategory'])
            ->orderBy('full_name')
            ->get();
    }

    /**
     * Get all departments (categories)
     */
    public function getDepartments(): Collection
    {
        return $this->categoryService->getDepartments();
    }

    /**
     * Get departments as array
     */
    public function getDepartmentsArray(): array
    {
        return $this->categoryService->getDepartmentsArray();
    }

    /**
     * Get departments for select dropdowns
     */
    public function getDepartmentsForSelect(): array
    {
        return $this->categoryService->getDepartmentsForSelect();
    }

    /**
     * Get categories hierarchy for preferred course selection
     */
    public function getCategoriesHierarchy(): Collection
    {
        return Category::active()
            ->visible()
            ->with(['children' => function($query) {
                $query->active()->visible()->orderBy('order')->orderBy('name_en');
            }])
            ->parents()
            ->orderedBySort()
            ->get();
    }

    /**
     * Get all reach sources
     */
    public function getReachSources(): array
    {
        return [
            'Social Media',
            'University Circular',
            'Purchased Data',
            'Referral',
            'Old Student',
            'Other'
        ];
    }

    /**
     * Get courses by department category
     */
    public function getCoursesByDepartment(?int $departmentCategoryId = null): Collection
    {
        if ($departmentCategoryId) {
            return $this->categoryService->getCoursesByCategory($departmentCategoryId);
        }
        
        return Course::active()->orderBy('name')->get();
    }

    /**
     * Get courses by legacy department name (backward compatibility)
     */
    public function getCoursesByLegacyDepartment(?string $department = null): Collection
    {
        if ($department) {
            $categoryId = $this->categoryService->getDepartmentIdByLegacyName($department);
            if ($categoryId) {
                return $this->getCoursesByDepartment($categoryId);
            }
            
            return Course::active()->byDepartment($department)->orderBy('name')->get();
        }
        
        return Course::active()->orderBy('name')->get();
    }

    /**
     * Generate unique student ID based on phone number
     */
    private function generateStudentId(string $phoneNumber): string
    {
        // Remove + and take last 10 digits, prefix with 'STU'
        $digits = preg_replace('/\D/', '', $phoneNumber);
        $lastDigits = substr($digits, -10);
        
        return 'STU' . $lastDigits;
    }

    /**
     * Get student statistics with category support
     */
    public function getStudentStats(): array
    {
        $total = Student::count();
        
        // Get stats by department categories
        $byDepartment = Student::select('department_category_id')
            ->selectRaw('COUNT(*) as count')
            ->with('departmentCategory')
            ->groupBy('department_category_id')
            ->get()
            ->mapWithKeys(function ($item) {
                $departmentName = $item->departmentCategory ? $item->departmentCategory->name : 'Unknown';
                return [$departmentName => $item->count];
            })
            ->toArray();

        // Include legacy department stats for backward compatibility
        $legacyByDepartment = Student::whereNull('department_category_id')
            ->selectRaw('department, COUNT(*) as count')
            ->groupBy('department')
            ->pluck('count', 'department')
            ->toArray();

        $byDepartment = array_merge($byDepartment, $legacyByDepartment);

        $byReachSource = Student::selectRaw('reach_source, COUNT(*) as count')
            ->groupBy('reach_source')
            ->pluck('count', 'reach_source')
            ->toArray();

        return [
            'total' => $total,
            'by_department' => $byDepartment,
            'by_reach_source' => $byReachSource,
        ];
    }
}