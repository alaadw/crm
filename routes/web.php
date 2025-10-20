<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\FollowUpController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CourseClassController;
use App\Http\Controllers\UserController;

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
    // Admin: Users management for managed departments
    Route::middleware(function ($request, $next) {
        if (!auth()->user() || !auth()->user()->isAdmin()) {
            abort(403);
        }
        return $next($request);
    })->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
    });
    Route::get('enrollments/create', [EnrollmentController::class, 'create'])->name('enrollments.create');
    Route::post('enrollments', [EnrollmentController::class, 'store'])->name('enrollments.store');
    Route::get('enrollments/{enrollment}', [EnrollmentController::class, 'show'])->name('enrollments.show');
    Route::delete('enrollments/{enrollment}', [EnrollmentController::class, 'destroy'])->name('enrollments.destroy');

    // Payment routes
    Route::post('students/{student}/payments', [PaymentController::class, 'storeStudentPayment'])->name('students.payments.store');
    Route::post('enrollments/{enrollment}/payments', [PaymentController::class, 'storeEnrollmentPayment'])->name('enrollments.payments.store');

    // Course Classes & Other routes
    Route::resource('classes', CourseClassController::class);
    Route::resource('enrollments', EnrollmentController::class);
    Route::resource('payments', PaymentController::class);
    
    // Department Reports
    Route::get('reports/department', [CourseClassController::class, 'departmentReports'])->name('reports.department');
    Route::get('reports/financial', [CourseClassController::class, 'financialReports'])->name('reports.financial');
});
 