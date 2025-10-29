<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Category;
use App\Models\Course;
use App\Http\Requests\ImportStudentsRequest;
use App\Http\Requests\BulkAssignStudentsRequest;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Services\StudentService;
use App\Services\PhoneService;
use App\Services\StudentImportService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    public function __construct(
        private StudentService $studentService,
        private PhoneService $phoneService,
        private StudentImportService $importService,
    ) {}

    /**
     * Display a listing of students
     */
    public function index(Request $request): View
    {
    $department = $request->get('department');
    $course = $request->get('course');
    $search = $request->get('search');
    $assignedUserFilter = $request->get('assigned_user_id');
    $user = $request->user();
        
    $query = Student::with(['preferredCourse', 'departmentCategory', 'assignedUser']);
        
        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'LIKE', "%{$search}%")
                  ->orWhere('full_name_en', 'LIKE', "%{$search}%")
                  ->orWhere('student_id', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone_primary', 'LIKE', "%{$search}%")
                  ->orWhere('phone_alt', 'LIKE', "%{$search}%");
            });
        }
        
        // Apply department filter
        if ($department) {
            if (is_numeric($department)) {
                // Filter by department_category_id (new system)
                $query->where('department_category_id', $department);
            } else {
                // Handle legacy department names (old enum values)
                $query->where('department', $department);
            }
        }
        
        // Apply course filter
        if ($course) {
            $query->where('preferred_course_id', $course);
        }

        if ($user && method_exists($user, 'isSalesRep') && $user->isSalesRep()) {
            $query->where('assigned_user_id', $user->id);
        } elseif ($assignedUserFilter !== null && $assignedUserFilter !== '') {
            $query->where('assigned_user_id', $assignedUserFilter);
        }
        
        $students = $query->orderBy('full_name')->paginate(20);
        
        // Preserve query parameters in pagination
        $students->appends($request->query());

    $departments = $this->studentService->getDepartmentsArrayForUser($user);
        $assignmentOptions = $this->studentService->getAssignmentFormOptions($user);
        $assignableUsers = $assignmentOptions['assignableUsers'];
        $defaultAssignedUserId = $assignmentOptions['defaultAssignedUserId'];
        $canChooseAssignedUser = $assignmentOptions['canChooseAssignedUser'];
        $importDepartments = [];
        $defaultImportDepartmentId = $user?->department_category_id;
    $showBulkAssignmentTools = $user && method_exists($user, 'isDepartmentManager') && $user->isDepartmentManager() && $assignableUsers->isNotEmpty();

        if ($user && method_exists($user, 'isAdmin') && $user->isAdmin()) {
            $importDepartments = $departments;
            $defaultImportDepartmentId = null;
        } elseif ($user && method_exists($user, 'isDepartmentManager') && $user->isDepartmentManager()) {
            $managedIds = $user->managed_department_ids ?? [];
            if (!empty($managedIds)) {
                $importDepartments = array_filter(
                    $departments,
                    fn ($name, $id) => in_array((int) $id, $managedIds),
                    ARRAY_FILTER_USE_BOTH
                );

                if (!$defaultImportDepartmentId && count($managedIds) === 1) {
                    $defaultImportDepartmentId = $managedIds[0];
                }
            }
        } else {
            $importDepartments = $departments;
        }
        
        // Get courses based on selected department
        $courses = [];
        if ($department && is_numeric($department)) {
            $courses = $this->studentService->getCoursesByDepartment($department);
        }
         
        $stats = $this->studentService->getStudentStats();

        $selectedAssignedUser = null;
        if ($assignedUserFilter !== null && $assignedUserFilter !== '') {
            $selectedAssignedUser = $assignableUsers->firstWhere('id', (int)$assignedUserFilter) ?? User::find($assignedUserFilter);
        }

        return view('students.index', compact(
            'students',
            'departments',
            'courses',
            'stats',
            'department',
            'course',
            'search',
            'assignedUserFilter',
            'selectedAssignedUser',
            'assignableUsers',
            'canChooseAssignedUser',
            'importDepartments',
            'defaultAssignedUserId',
            'defaultImportDepartmentId',
            'showBulkAssignmentTools'
        ));
    }

    /**
     * Handle Excel/CSV upload to import students.
     */
    public function import(ImportStudentsRequest $request)
    {
        $validated = $request->validated();

        $path = $request->file('file')->store('imports');
        $fullPath = Storage::path($path);

        $user = $request->user();

        // Use centralized service to resolve assigned user & department defaults
        $options = $this->studentService->resolveAssignmentOptions($validated, $user);

        $result = $this->importService->import($fullPath, $options);

        return redirect()->route('students.index')
            ->with('status', __('common.import_completed'))
            ->with('import_result', $result);
    }

    public function bulkAssign(BulkAssignStudentsRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $user = $request->user();

        $options = $this->studentService->resolveAssignmentOptions([
            'assigned_user_id' => $data['assigned_user_id'],
        ], $user);

        $resolvedAssignedUserId = $options['assigned_user_id'];
         if ($resolvedAssignedUserId === null || (int) $resolvedAssignedUserId !== (int) $data['assigned_user_id']) {
            return redirect()->back()
                ->withErrors(['assigned_user_id' => __('students.assigned_user_invalid_for_manager')])
                ->withInput($request->only('student_ids', 'assigned_user_id'));
        }

        $updatedCount = $this->studentService->bulkAssignStudents($data['student_ids'], $resolvedAssignedUserId, $user);

        if ($updatedCount === 0) {
            return redirect()->back()
                ->with('status', __('students.bulk_assign_none_updated'))
                ->withInput($request->only('student_ids', 'assigned_user_id'));
        }

        return redirect()->route('students.index')
            ->with('status', __('students.bulk_assign_success', ['count' => $updatedCount]));
    }

    /**
     * Show the form for creating a new student
     */
    public function create(): View
    {
        $user = auth()->user();
        $departments = $this->studentService->getDepartmentsForSelect();
        $categories = $this->studentService->getCategoriesHierarchy();
        $reachSources = $this->studentService->getReachSources();
        $courses = $this->studentService->getCoursesByDepartment();
        $countryCodes = $this->phoneService->getCountryCodes();

        $assignmentOptions = $this->studentService->getAssignmentFormOptions($user);

        return view('students.create', array_merge([
            'departments' => $departments,
            'categories' => $categories,
            'reachSources' => $reachSources,
            'courses' => $courses,
            'countryCodes' => $countryCodes,
        ], $assignmentOptions));
    }

    /**
     * Store a newly created student
     */
    public function store(StoreStudentRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();
            $options = $this->studentService->resolveAssignmentOptions($data, $request->user());
            // Merge resolved options back into payload before creating
            $data = array_merge($data, $options);

            $student = $this->studentService->createStudent($data);

            return redirect()->route('students.show', $student)
                ->with('success', __('students.student_created'));
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()
                ->withErrors(['phone_primary' => $e->getMessage()])
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => __('common.error_occurred')])
                ->withInput();
        }
    }

    /**
     * Display the specified student
     */
    public function show(Student $student): View
    {
        $student->load([
            'preferredCourse', 
            'departmentCategory', 
            'followUps' => function ($query) {
                $query->orderBy('scheduled_date', 'desc')->limit(10);
            },
            'activeEnrollments.courseClass.course',
            'activeEnrollments.courseClass'
        ]);
        
        // Get follow-ups statistics
        $followUpsStats = [
            'total' => $student->followUps()->count(),
            'pending' => $student->followUps()->where('status', 'pending')->count(),
            'completed' => $student->followUps()->where('status', 'completed')->count(),
            'overdue' => $student->followUps()->where('status', 'pending')
                ->where('scheduled_date', '<', now())->count(),
        ];
        
        return view('students.show', compact('student', 'followUpsStats'));
    }

    /**
     * Show the form for editing the student
     */
    public function edit(Student $student): View
    {
        $user = auth()->user();
        $departments = $this->studentService->getDepartmentsForSelect();
        $categories = $this->studentService->getCategoriesHierarchy();
        $reachSources = $this->studentService->getReachSources();
        
        // Get courses based on student's department category or legacy department
        if ($student->department_category_id) {
            $courses = $this->studentService->getCoursesByDepartment($student->department_category_id);
        } else {
            $courses = $this->studentService->getCoursesByLegacyDepartment($student->department);
        }
        
        $countryCodes = $this->phoneService->getCountryCodes();

        $assignmentOptions = $this->studentService->getAssignmentFormOptions($user);

        return view('students.edit', array_merge([
            'student' => $student,
            'departments' => $departments,
            'categories' => $categories,
            'reachSources' => $reachSources,
            'courses' => $courses,
            'countryCodes' => $countryCodes,
        ], $assignmentOptions));
    }

    /**
     * Update the specified student
     */
    public function update(UpdateStudentRequest $request, Student $student): RedirectResponse
    {
        try {
            $data = $request->validated();
            $options = $this->studentService->resolveAssignmentOptions($data, $request->user());
            $data = array_merge($data, $options);

            $updatedStudent = $this->studentService->updateStudent($student, $data);

            return redirect()->route('students.show', $updatedStudent)
                ->with('success', __('students.student_updated'));
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()
                ->withErrors(['phone_primary' => $e->getMessage()])
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => __('common.error_occurred')])
                ->withInput();
        }
    }

    /**
     * Remove the specified student
     */
    public function destroy(Student $student): RedirectResponse
    {
        try {
            $student->delete();

            return redirect()->route('students.index')
                ->with('success', 'Student deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'An error occurred while deleting the student.']);
        }
    }

    /**
     * Search for student by phone or student ID
     */
    public function search(Request $request): View|RedirectResponse
    {
        $searchTerm = $request->get('search');

        if (!$searchTerm) {
            return redirect()->route('students.create');
        }

        $student = $this->studentService->findStudent($searchTerm);

        if ($student) {
            return redirect()->route('students.show', $student);
        }

        // No student found, redirect to create with pre-filled phone
        return redirect()->route('students.create')
            ->with('prefill_phone', $searchTerm)
            ->with('info', 'No student found with this information. You can create a new student.');
    }

    /**
     * Get courses by department (AJAX)
     */
    public function getCoursesByDepartment(Request $request)
    {
        $department = $request->get('department');
        $courses = $this->studentService->getCoursesByDepartment($department);

        return response()->json($courses);
    }

    /**
     * Get subcategories by parent category ID (AJAX)
     */
    public function getSubcategories(Request $request)
    {
        $parentId = $request->get('parent_id');
        
        if (!$parentId) {
            return response()->json([]);
        }

        $subcategories = Category::active()
            ->visible()
            ->where('parent_id', $parentId)
            ->orderBy('order')
            ->orderBy('name_en')
            ->get()
            ->map(function($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name_ar ?? $category->name_en,
                    'name_en' => $category->name_en,
                    'has_children' => $category->children()->count() > 0
                ];
            });
        return response()->json($subcategories);
    }

    /**
     * Autocomplete students (AJAX JSON) for enrollment modal
     */
    public function autocomplete(Request $request)
    {
        $q = trim((string) $request->get('q'));
        if ($q === '') {
            return response()->json([]);
        }

        $students = Student::query()
            ->select(['id', 'student_id', 'full_name', 'full_name_en', 'phone_primary', 'email'])
            ->where(function($w) use ($q) {
                $like = "%" . str_replace('%', '\\%', $q) . "%";
                $w->where('full_name', 'like', $like)
                  ->orWhere('full_name_en', 'like', $like)
                  ->orWhere('student_id', 'like', $like)
                  ->orWhere('phone_primary', 'like', $like)
                  ->orWhere('email', 'like', $like);
            })
            ->orderBy('full_name')
            ->limit(20)
            ->get();

        $results = $students->map(function($s) {
            $label = trim($s->full_name ?: $s->full_name_en ?: '');
            $meta = [];
            if ($s->student_id) { $meta[] = "ID: {$s->student_id}"; }
            if ($s->phone_primary) { $meta[] = $s->phone_primary; }
            if ($s->email) { $meta[] = $s->email; }
            $text = $label . (count($meta) ? ' — ' . implode(' | ', $meta) : '');
            return [
                'id' => $s->id,
                'text' => $text,
            ];
        });

        return response()->json($results);
    }

    /**
     * Get courses by category ID (AJAX)
     */
    public function getCoursesByCategory(Request $request)
    {
        $categoryId = $request->get('category_id');
        
        if (!$categoryId) {
            return response()->json([]);
        }

        $courses = Course::active()
            ->where('category_id', $categoryId)
            ->orderBy('name')
            ->get()
            ->map(function($course) {
                return [
                    'id' => $course->id,
                    'name' => $course->name_ar ?? $course->name,
                    'name_en' => $course->name_en
                ];
            });

        return response()->json($courses);
    }
}
