<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Course;
use App\Models\Category;
use App\Models\User;
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

    public function getDepartmentsArrayForUser(?User $user = null): array
    {
        $departments = $this->getDepartmentsArray();

        if (!$user || $user->isAdmin()) {
            return $departments;
        }

        $allowedIds = $this->collectAllowedDepartmentIdsForUser($user);
        $allowedIds = array_values(array_unique(array_filter(array_map('intval', $allowedIds))));

        if (!$allowedIds) {
            return [];
        }

        $allowedMap = array_fill_keys($allowedIds, true);

        return array_filter(
            $departments,
            fn ($label, $id) => isset($allowedMap[(int) $id]),
            ARRAY_FILTER_USE_BOTH
        );
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

    /**
     * Build assignment selection options for student forms based on the current user.
     */
    public function getAssignmentFormOptions(?User $user = null): array
    {
        $assignableUsers = collect();
        $defaultAssignedUserId = $user?->id;
        $canChooseAssignedUser = false;

        if ($user && $user->isAdmin()) {
            $assignableUsers = User::orderBy('name')->get(['id', 'name', 'email']);
            $defaultAssignedUserId = null;
            $canChooseAssignedUser = $assignableUsers->isNotEmpty();
        } elseif ($user && $user->isDepartmentManager()) {
            $assignableUsers = User::query()
                ->where(function ($query) use ($user) {
                    $query->where('id', $user->id)
                        ->orWhere('manager_responsible_id', $user->id);
                })
                ->orderBy('name')
                ->get(['id', 'name', 'email']);

            $defaultAssignedUserId = $user->id;
            $canChooseAssignedUser = $assignableUsers->isNotEmpty();
        }

        return [
            'assignableUsers' => $assignableUsers,
            'defaultAssignedUserId' => $defaultAssignedUserId,
            'canChooseAssignedUser' => $canChooseAssignedUser,
        ];
    }

    /**
     * Resolve assigned user id and department_category_id based on input and current user role.
     * Returns an array with 'assigned_user_id' and 'department_category_id' keys (may be null).
     *
     * This centralizes the logic so controllers and import services don't duplicate it.
     *
     * @param array $input Incoming data that may contain assigned_user_id and department_category_id
     * @param User|null $user Currently authenticated user
     * @return array
     */
    public function resolveAssignmentOptions(array $input, ?User $user = null): array
    {
        $assignedUserId = $input['assigned_user_id'] ?? null;
        $departmentId = $input['department_category_id'] ?? null;

        // Default assigned user to current user if not provided
        if (!$assignedUserId && $user) {
            $assignedUserId = $user->id;
        }

        // Determine department defaulting rules
        if (!$departmentId && $user) {
            if ($user->isSalesRep()) {
                $departmentId = $user->department_category_id ?? null;
            } elseif ($user->isDepartmentManager()) {
                $managedIds = $user->managed_department_ids ?? [];
                if (count($managedIds) === 1) {
                    $departmentId = $managedIds[0];
                }
            }
        }

        // Role based assignment restrictions: sales reps cannot assign to others
        if ($user && $user->isSalesRep()) {
            $assignedUserId = $user->id;
        }

        // Department managers may only assign to themselves or users they are responsible for
        if ($user && $user->isDepartmentManager()) {
            // If an assignedUserId was provided and it's not allowed, fallback to manager
            if ($assignedUserId && $assignedUserId !== $user->id) {
                $allowed = User::where('id', $assignedUserId)
                    ->where(function ($q) use ($user) {
                        $q->where('id', $user->id)
                          ->orWhere('manager_responsible_id', $user->id);
                    })->exists();

                if (!$allowed) {
                    $assignedUserId = $user->id;
                }
            }
        }

        return [
            'assigned_user_id' => $assignedUserId ? (int)$assignedUserId : null,
            'department_category_id' => $departmentId ? (int)$departmentId : null,
        ];
    }

    public function bulkAssignStudents(array $studentIds, int $assignedUserId, ?User $actor = null): int
    {
        $query = Student::query()
            ->whereIn('id', $studentIds)
            ->whereNull('assigned_user_id');
         
        $isAdmin = $actor && $actor->isAdmin();

        if (!$isAdmin && $actor && $actor->isDepartmentManager()) {
            $managedIds = $actor->managed_department_ids ?? [];
            if (!empty($managedIds)) {
                $query->where(function ($q) use ($managedIds) {
                    $q->whereIn('department_category_id', $managedIds)
                        ->orWhereNull('department_category_id');
                });
            } elseif ($actor->department_category_id) {
                $query->where(function ($q) use ($actor) {
                    $q->where('department_category_id', $actor->department_category_id)
                        ->orWhereNull('department_category_id');
                });
            }
        }

        return $query->update([
            'assigned_user_id' => $assignedUserId,
            'updated_at' => now(),
        ]);
    }

    private function collectAllowedDepartmentIdsForUser(User $user): array
    {
        if ($user->isDepartmentManager()) {
            return $this->collectManagerDepartmentIds($user);
        }

        if ($user->isSalesRep()) {
            return $this->collectSalesRepDepartmentIds($user);
        }

        return $this->collectDepartmentIdsFromUser($user);
    }

    private function collectManagerDepartmentIds(User $user): array
    {
        $managedIds = $user->managed_department_ids ?? [];

        if ($managedIds) {
            return $this->collectDepartmentIdsFromUser($user, $managedIds);
        }

        return $this->collectDepartmentIdsFromUser($user);
    }

    private function collectSalesRepDepartmentIds(User $user): array
    {
        $ids = $this->collectDepartmentIdsFromUser($user);

        $manager = $user->relationLoaded('responsibleManager')
            ? $user->getRelation('responsibleManager')
            : $user->responsibleManager;

        if (!$manager) {
            return $ids;
        }

        $ids = array_merge($ids, $manager->managed_department_ids ?? []);

        return $this->collectDepartmentIdsFromUser($manager, $ids);
    }

    private function collectDepartmentIdsFromUser(User $user, array $seed = []): array
    {
        $ids = $seed;
        $this->appendDepartmentFromUser($ids, $user);

        return $ids;
    }

    private function appendDepartmentFromUser(array &$bucket, User $user): void
    {
        if (!empty($user->department_category_id)) {
            $bucket[] = (int) $user->department_category_id;
        }

        if (!empty($user->department)) {
            if (is_numeric($user->department)) {
                $bucket[] = (int) $user->department;
            } else {
                $legacyId = $this->categoryService->getDepartmentIdByLegacyName($user->department);
                if ($legacyId) {
                    $bucket[] = $legacyId;
                }
            }
        }
    }
}