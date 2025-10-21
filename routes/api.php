<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CourseClassController;
use App\Http\Controllers\EnrollmentController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Use session-based auth for in-app AJAX calls rendered from web pages
Route::middleware(['web', 'auth'])->group(function () {
    // Hierarchical categories and courses
    Route::get('subcategories', [StudentController::class, 'getSubcategories'])->name('api.subcategories');
    Route::get('courses-by-category', [StudentController::class, 'getCoursesByCategory'])->name('api.courses-by-category');

    // Moodle courses list for class linking
    Route::get('classes/moodle-courses', [CourseClassController::class, 'getMoodleCourses'])->name('classes.moodle-courses');

    // Students autocomplete for enrollment modal
    Route::get('students/autocomplete', [StudentController::class, 'autocomplete'])->name('students.autocomplete');

    // Enrollment payments for View Payments modal
    Route::get('enrollments/{enrollment}/payments', [PaymentController::class, 'enrollmentPayments'])->name('api.enrollments.payments');
    Route::post('enrollments/{enrollment}/sync-moodle', [EnrollmentController::class, 'syncWithMoodle'])->name('api.enrollments.sync-moodle');
});
