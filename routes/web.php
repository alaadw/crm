<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\FollowUpController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CourseClassController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExpenseTypeController;

// Redirect root to students index
Route::get('/', function () {
    return redirect()->route('students.index');
});

// Authentication routes
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Language switching route
Route::get('language/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ar'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('language.switch');

// Protected routes (require authentication)
Route::middleware(['auth'])->group(function () {
    // Student routes
    Route::resource('students', StudentController::class);
    Route::post('students/import', [StudentController::class, 'import'])->name('students.import');
    Route::post('students/bulk-assign', [StudentController::class, 'bulkAssign'])->name('students.bulk-assign');
    Route::get('student-search', [StudentController::class, 'search'])->name('students.search');
    Route::get('courses-by-department', [StudentController::class, 'getCoursesByDepartment'])->name('courses.by-department');
    
    // (moved to routes/api.php) AJAX routes

    // Follow-up routes
    Route::resource('follow-ups', FollowUpController::class);
    Route::post('follow-ups/quick-add', [FollowUpController::class, 'quickAdd'])->name('follow-ups.quick-add');
    Route::post('follow-ups/{followUp}/complete', [FollowUpController::class, 'complete'])->name('follow-ups.complete');
    Route::post('follow-ups/{followUp}/cancel', [FollowUpController::class, 'cancel'])->name('follow-ups.cancel');
    Route::get('follow-ups/student/{student}', [FollowUpController::class, 'studentFollowUps'])->name('follow-ups.student');

    // Enrollment routes
    // Admin: Users management (controller enforces admin)
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('users', [UserController::class, 'store'])->name('users.store');
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');

    // Expenses (admin and department managers)
    Route::get('expenses/chart-data/{period?}/{year?}/{month?}', [ExpenseController::class, 'chartData'])->name('expenses.chart-data');
    Route::get('expenses/chart-by-type/{period?}/{year?}/{month?}', [ExpenseController::class, 'chartDataByType'])->name('expenses.chart-by-type');
    Route::get('expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::get('expenses/create', [ExpenseController::class, 'create'])->name('expenses.create');
    Route::post('expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::get('expenses/export/csv', [ExpenseController::class, 'exportCsv'])->name('expenses.export.csv');
    Route::get('expenses/export/pdf', [ExpenseController::class, 'exportPdf'])->name('expenses.export.pdf');
    Route::get('expenses/export/excel', [ExpenseController::class, 'exportExcel'])->name('expenses.export.excel');
    Route::delete('expenses/{id}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');

    // Expense Types (admin only)
    Route::get('expense-types', [ExpenseTypeController::class, 'index'])->name('expense-types.index');
    Route::get('expense-types/create', [ExpenseTypeController::class, 'create'])->name('expense-types.create');
    Route::post('expense-types', [ExpenseTypeController::class, 'store'])->name('expense-types.store');
    Route::get('expense-types/{type}/edit', [ExpenseTypeController::class, 'edit'])->name('expense-types.edit');
    Route::put('expense-types/{type}', [ExpenseTypeController::class, 'update'])->name('expense-types.update');
    Route::delete('expense-types/{type}', [ExpenseTypeController::class, 'destroy'])->name('expense-types.destroy');
    Route::get('enrollments/create', [EnrollmentController::class, 'create'])->name('enrollments.create');
    Route::post('enrollments', [EnrollmentController::class, 'store'])->name('enrollments.store');
    Route::get('enrollments/{enrollment}', [EnrollmentController::class, 'show'])->name('enrollments.show');
    Route::delete('enrollments/{enrollment}', [EnrollmentController::class, 'destroy'])->name('enrollments.destroy');

    // Payment routes
    Route::post('students/{student}/payments', [PaymentController::class, 'storeStudentPayment'])->name('students.payments.store');
    Route::post('enrollments/{enrollment}/payments', [PaymentController::class, 'storeEnrollmentPayment'])->name('enrollments.payments.store');

    // Course Classes & Other routes
    Route::resource('classes', CourseClassController::class);
    Route::get('classes/{class}/export-enrollments', [CourseClassController::class, 'exportEnrollments'])->name('classes.export-enrollments');
    Route::resource('enrollments', EnrollmentController::class);
    Route::get('payments/chart-data/{period?}/{year?}/{month?}', [PaymentController::class, 'chartData'])->name('payments.chart-data');
    Route::get('payments/chart-by-method/{period?}/{year?}/{month?}', [PaymentController::class, 'chartByMethod'])->name('payments.chart-by-method');
    Route::get('payments/export/excel', [PaymentController::class, 'exportExcel'])->name('payments.export.excel');
    Route::get('payments/export/pdf', [PaymentController::class, 'exportPdf'])->name('payments.export.pdf');
    Route::get('payments/print', [PaymentController::class, 'print'])->name('payments.print');
    Route::resource('payments', PaymentController::class);
    
    // Department Reports
    Route::get('reports/department', [CourseClassController::class, 'departmentReports'])->name('reports.department');
    Route::get('reports/financial', [CourseClassController::class, 'financialReports'])->name('reports.financial');
});
 