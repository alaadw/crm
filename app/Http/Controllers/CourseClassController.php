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
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

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

        // Apply user permissions (multi-department managers)
        $user = Auth::user();
        if ($user && method_exists($user, 'isDepartmentManager') && $user->isDepartmentManager()) {
            $managedIds = method_exists($user, 'getManagedDepartmentIdsAttribute') ? $user->managed_department_ids : $this->parseDepartmentIds($user->department);
            if (!empty($managedIds)) {
                $query->whereIn('category_id', $managedIds);
            }
        }

        $classes = $query->orderBy('start_date', 'desc')->paginate(15);
        
        // Get departments for filter (top-level only), restricted for managers
        $departmentsQuery = Category::query()->where('parent_id', 0);
        if ($user && method_exists($user, 'isDepartmentManager') && $user->isDepartmentManager()) {
            $managedIds = method_exists($user, 'getManagedDepartmentIdsAttribute') ? $user->managed_department_ids : $this->parseDepartmentIds($user->department);
            if (!empty($managedIds)) {
                $departmentsQuery->whereIn('id', $managedIds);
            } else {
                // If no managed IDs parsed, return empty result to avoid exposing other departments
                $departmentsQuery->whereRaw('1=0');
            }
        }
        $departments = $departmentsQuery->get();
        
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
        // Top-level departments only; restrict to manager's departments if applicable
        $departmentsQuery = Category::query()->where('parent_id', 0);
        $user = Auth::user();
        if ($user && method_exists($user, 'isDepartmentManager') && $user->isDepartmentManager()) {
            $managedIds = method_exists($user, 'getManagedDepartmentIdsAttribute') ? $user->managed_department_ids : $this->parseDepartmentIds($user->department);
            if (!empty($managedIds)) {
                $departmentsQuery->whereIn('id', $managedIds);
            } else {
                $departmentsQuery->whereRaw('1=0');
            }
        }
        $departments = $departmentsQuery->get();
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
        // Top-level departments only; restrict to manager's departments if applicable
        $departmentsQuery = Category::query()->where('parent_id', 0);
        $user = Auth::user();
        if ($user && method_exists($user, 'isDepartmentManager') && $user->isDepartmentManager()) {
            $managedIds = $this->parseDepartmentIds($user->department);
            if (!empty($managedIds)) {
                $departmentsQuery->whereIn('id', $managedIds);
            } else {
                $departmentsQuery->whereRaw('1=0');
            }
        }
        $departments = $departmentsQuery->get();
        
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
    /**
     * Parse a user's managed department IDs from various possible formats.
     * Accepts: array of ints, comma-separated string ("29,56"), JSON array string,
     * integer, or null. Returns an array of unique integer IDs.
     */
    /**
     * Get all available Moodle courses for dropdown
     */
    public function getMoodleCourses()
    {
        try {
            $moodleService = new \App\Services\MoodleService();
            $courses = $moodleService->getAllCourses();
            return response()->json(['success' => true, 'data' => $courses]);
        } catch (\Exception $e) {
            \Log::error('CourseClassController::getMoodleCourses Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to fetch Moodle courses'], 500);
        }
    }

    public function exportEnrollments(CourseClass $class)
    {
        $class->load(['course', 'enrollments' => function ($query) {
            $query->where('is_active', true)->with(['student', 'registeredBy']);
        }]);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(__('classes.enrolled_students'));

        $headers = [
            __('students.name'),
            __('students.email'),
            __('students.primary_phone'),
            __('classes.enrollment_date'),
            __('classes.payment_status'),
            __('classes.total_amount'),
            __('classes.paid_amount'),
            __('classes.due_amount'),
            __('classes.notes'),
            __('common.registered_by'),
            __('classes.moodle_status'),
        ];

        foreach ($headers as $index => $heading) {
            $column = Coordinate::stringFromColumnIndex($index + 1);
            $sheet->setCellValue($column . '1', $heading);
        }

        $row = 2;
        foreach ($class->enrollments as $enrollment) {
            $student = $enrollment->student;
            $sheet->setCellValue('A' . $row, $student?->full_name ?? '');
            $sheet->setCellValue('B' . $row, $student?->email ?? '');
            $sheet->setCellValue('C' . $row, $student?->formatted_phone_primary ?? $student?->phone_primary ?? '');
            $sheet->setCellValue('D' . $row, optional($enrollment->enrollment_date)->format('Y-m-d'));
            $sheet->setCellValue('E' . $row, $enrollment->payment_status_label ?? $enrollment->payment_status);
            $sheet->setCellValue('F' . $row, (float) $enrollment->total_amount);
            $sheet->setCellValue('G' . $row, (float) $enrollment->paid_amount);
            $sheet->setCellValue('H' . $row, (float) $enrollment->due_amount);
            $sheet->setCellValue('I' . $row, $enrollment->notes ?? '');
            $sheet->setCellValue('J' . $row, $enrollment->registeredBy?->name ?? '');
            $sheet->setCellValue('K' . $row, match ($enrollment->moodle_sync_status) {
                'synced' => __('classes.moodle_status_synced'),
                'syncing' => __('classes.moodle_status_syncing'),
                'failed' => __('classes.moodle_status_failed'),
                default => __('classes.moodle_status_not_synced'),
            });
            $row++;
        }

        foreach (range('A', 'K') as $columnId) {
            $sheet->getColumnDimension($columnId)->setAutoSize(true);
        }

        $sheet->freezePane('A2');

        $sheet->getStyle('F2:H' . max($row - 1, 2))->getNumberFormat()->setFormatCode('#,##0.00');

        $filename = __('classes.export_filename', [
            'class' => Str::slug($class->class_name ?? 'class'),
            'date' => now()->format('Ymd_His'),
        ]);

        if (!Str::endsWith($filename, '.xlsx')) {
            $filename .= '.xlsx';
        }

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function parseDepartmentIds($value): array
    {
        if (is_array($value)) {
            return array_values(array_unique(array_map('intval', $value)));
        }

        if (is_numeric($value)) {
            return [intval($value)];
        }

        if (is_string($value)) {
            $value = trim($value);
            if ($value === '') {
                return [];
            }
            // Try JSON array
            if ((str_starts_with($value, '[') && str_ends_with($value, ']'))) {
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    return array_values(array_unique(array_map('intval', $decoded)));
                }
            }
            // Fallback: comma/space separated list
            $parts = preg_split('/[\s,]+/', $value);
            return array_values(array_unique(array_map('intval', array_filter($parts, fn($p) => $p !== ''))));
        }

        return [];
    }
}
