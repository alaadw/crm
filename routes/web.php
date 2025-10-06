<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\FollowUpController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\Auth\LoginController;

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
    Route::get('student-search', [StudentController::class, 'search'])->name('students.search');
    Route::get('courses-by-department', [StudentController::class, 'getCoursesByDepartment'])->name('courses.by-department');
    
    // AJAX routes for hierarchical category selection
    Route::get('api/subcategories', [StudentController::class, 'getSubcategories'])->name('api.subcategories');
    Route::get('api/courses-by-category', [StudentController::class, 'getCoursesByCategory'])->name('api.courses-by-category');

    // Follow-up routes
    Route::resource('follow-ups', FollowUpController::class);
    Route::post('follow-ups/quick-add', [FollowUpController::class, 'quickAdd'])->name('follow-ups.quick-add');
    Route::post('follow-ups/{followUp}/complete', [FollowUpController::class, 'complete'])->name('follow-ups.complete');
    Route::post('follow-ups/{followUp}/cancel', [FollowUpController::class, 'cancel'])->name('follow-ups.cancel');
    Route::get('follow-ups/student/{student}', [FollowUpController::class, 'studentFollowUps'])->name('follow-ups.student');

    // Enrollment routes
    Route::get('enrollments/create', [EnrollmentController::class, 'create'])->name('enrollments.create');
    Route::post('enrollments', [EnrollmentController::class, 'store'])->name('enrollments.store');
    Route::get('enrollments/{enrollment}', [EnrollmentController::class, 'show'])->name('enrollments.show');
    Route::delete('enrollments/{enrollment}', [EnrollmentController::class, 'destroy'])->name('enrollments.destroy');

    // Payment routes
    Route::post('students/{student}/payments', [\App\Http\Controllers\PaymentController::class, 'storeStudentPayment'])->name('students.payments.store');
    Route::post('enrollments/{enrollment}/payments', [\App\Http\Controllers\PaymentController::class, 'storeEnrollmentPayment'])->name('enrollments.payments.store');

    // Course Classes & Other routes
    Route::resource('classes', \App\Http\Controllers\CourseClassController::class);
    Route::resource('enrollments', \App\Http\Controllers\EnrollmentController::class);
    Route::resource('payments', \App\Http\Controllers\PaymentController::class);
    
    // Department Reports
    Route::get('reports/department', [\App\Http\Controllers\CourseClassController::class, 'departmentReports'])->name('reports.department');
    Route::get('reports/financial', [\App\Http\Controllers\CourseClassController::class, 'financialReports'])->name('reports.financial');
});
 