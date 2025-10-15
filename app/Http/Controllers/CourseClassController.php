<?php

namespace App\Http\Controllers;

use App\Models\CourseClass;
use App\Models\Category;
use App\Models\Course;
use App\Http\Requests\StoreCourseClassRequest;
use App\Http\Requests\UpdateCourseClassRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class CourseClassController extends Controller
{
    /**
     * Display a listing of classes
     */
    public function index(Request $request): View
    {
        $query = CourseClass::with(['course', 'category', 'enrollments.student', 'enrollments.payments'])
                           ->active();

        // Apply filters
        if ($request->filled('department')) {
            $query->where('category_id', $request->department);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('start_date')) {
            $query->where('start_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('end_date', '<=', $request->end_date);
        }

        // Apply user permissions
        $user = Auth::user();
        if ($user && method_exists($user, 'isDepartmentManager') && $user->isDepartmentManager()) {
            $query->where('category_id', $user->department);
        }

        $classes = $query->orderBy('start_date', 'desc')->paginate(15);
        
        // Get departments for filter
        $departments = Category::whereIn('id', [29, 33, 56, 58])->get(); // Main departments
        
        // Calculate totals
        $totalClasses = $classes->total();
        $totalStudents = $classes->sum(function($class) {
            return $class->total_enrolled_students;
        });
        $totalPaid = $classes->sum(function($class) {
            return $class->total_paid_amount;
        });
        $totalDue = $classes->sum(function($class) {
            return $class->total_due_amount;
        });

        return view('classes.index', compact(
            'classes',
            'departments',
            'totalClasses',
            'totalStudents',
            'totalPaid',
            'totalDue'
        ));
    }

    /**
     * Show the form for creating a new class
     */
    public function create(): View
    {
        $departments = Category::whereIn('id', [29, 33, 56, 58])->get();
        $courses = []; // Will be loaded via AJAX when department is selected
        
        return view('classes.create', compact('courses', 'departments'));
    }

    /**
     * Store a newly created class
     */
    public function store(StoreCourseClassRequest $request): RedirectResponse
    {
        CourseClass::create($request->validated());

        return redirect()->route('classes.index')
                        ->with('success', __('classes.class_created'));
    }

    /**
     * Display the specified class
     */
    public function show(CourseClass $class): View
    {
        $class->load([
            'course', 
            'category', 
            'enrollments.student', 
            'enrollments.registeredBy',
            'enrollments.payments'
        ]);

        $currencies = \App\Models\Currency::getActiveCurrencies();

        return view('classes.show', compact('class', 'currencies'));
    }

    /**
     * Show the form for editing the specified class
     */
    public function edit(CourseClass $class): View
    {
        $departments = Category::whereIn('id', [29, 33, 56, 58])->get();
        
        // Get courses for the selected department
        $courses = [];
        if ($class->category_id) {
            $courses = Course::where('is_active', true)
                            ->where('category_id', $class->category_id)
                            ->orderBy('name_ar')
                            ->get();
        }
        
        return view('classes.edit', compact('class', 'courses', 'departments'));
    }

    /**
     * Update the specified class
     */
    public function update(UpdateCourseClassRequest $request, CourseClass $class): RedirectResponse
    {
        $class->update($request->validated());

        return redirect()->route('classes.show', $class)
                        ->with('success', __('classes.class_updated'));
    }

    /**
     * Remove the specified class
     */
    public function destroy(CourseClass $class): RedirectResponse
    {
        // Check if class has enrollments
        if ($class->enrollments()->exists()) {
            return redirect()->route('classes.index')
                            ->with('error', 'لا يمكن حذف الشعبة لوجود طلاب مسجلين بها');
        }

        $class->delete();

        return redirect()->route('classes.index')
                        ->with('success', 'تم حذف الشعبة بنجاح');
    }
}
