<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Category;
use App\Models\Course;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Services\StudentService;
use App\Services\PhoneService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    public function __construct(
        private StudentService $studentService,
        private PhoneService $phoneService
    ) {}

    /**
     * Display a listing of students
     */
    public function index(Request $request): View
    {
        $department = $request->get('department');
        $search = $request->get('search');
        
        $query = Student::with(['preferredCourse', 'departmentCategory']);
        
        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'LIKE', "%{$search}%")
                  ->orWhere('full_name_en', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone_primary', 'LIKE', "%{$search}%")
                  ->orWhere('phone_alt', 'LIKE', "%{$search}%");
            });
        }
        
        // Apply department filter
        if ($department) {
            if (is_numeric($department)) {
                $query->where('department', $department);
            } else {
                // Handle legacy department names
                $query->where('department', $department);
            }
        }
        
        $students = $query->orderBy('full_name')->paginate(20);
        
        // Preserve query parameters in pagination
        $students->appends($request->query());

        $departments = $this->studentService->getDepartmentsArray();
        $stats = $this->studentService->getStudentStats();

        return view('students.index', compact('students', 'departments', 'stats', 'department', 'search'));
    }

    /**
     * Show the form for creating a new student
     */
    public function create(): View
    {
        $departments = $this->studentService->getDepartmentsForSelect();
        $categories = $this->studentService->getCategoriesHierarchy();
        $reachSources = $this->studentService->getReachSources();
        $courses = $this->studentService->getCoursesByDepartment();
        $countryCodes = $this->phoneService->getCountryCodes();

        return view('students.create', compact('departments', 'categories', 'reachSources', 'courses', 'countryCodes'));
    }

    /**
     * Store a newly created student
     */
    public function store(StoreStudentRequest $request): RedirectResponse
    {
        try {
            $student = $this->studentService->createStudent($request->validated());

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

        return view('students.edit', compact('student', 'departments', 'categories', 'reachSources', 'courses', 'countryCodes'));
    }

    /**
     * Update the specified student
     */
    public function update(UpdateStudentRequest $request, Student $student): RedirectResponse
    {
        try {
            $updatedStudent = $this->studentService->updateStudent($student, $request->validated());

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
