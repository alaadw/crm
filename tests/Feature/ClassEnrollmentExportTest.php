<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseClass;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ClassEnrollmentExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_class_enrollment_export_generates_excel_file(): void
    {
        Carbon::setTestNow(Carbon::create(2025, 10, 21, 10, 30, 0));

        $user = User::factory()->create();
        $this->actingAs($user);

        $category = Category::create([
            'name_en' => 'Technology',
            'name_ar' => 'تقنية',
            'order' => 1,
            'status' => true,
            'show' => true,
            'parent_id' => 0,
        ]);

        $course = Course::create([
            'name' => 'Laravel Mastery',
            'code' => 'LRV-200',
            'department' => 'IT',
            'category_id' => $category->id,
            'is_active' => true,
        ]);

        $class = CourseClass::create([
            'class_name' => 'Laravel Evening Batch',
            'class_code' => 'LEV-1',
            'course_id' => $course->id,
            'category_id' => $category->id,
            'start_date' => now()->subDays(5),
            'end_date' => now()->addWeeks(4),
            'status' => 'registration',
            'default_price' => 200,
            'is_active' => true,
        ]);

        $student = Student::create([
            'student_id' => 'STU-' . Str::random(6),
            'full_name' => 'Export Student',
            'email' => 'export@example.com',
            'phone_primary' => '+962790000000',
            'country_code' => '+962',
            'reach_source' => 'Social Media',
            'department' => 'IT',
            'department_category_id' => $category->id,
        ]);

        Enrollment::create([
            'student_id' => $student->id,
            'course_class_id' => $class->id,
            'registered_by' => $user->id,
            'enrollment_date' => now(),
            'total_amount' => 200,
            'paid_amount' => 100,
            'due_amount' => 100,
            'payment_status' => 'partial',
            'notes' => 'Paid half upfront',
            'is_active' => true,
            'moodle_sync_status' => 'synced',
        ]);

        $response = $this->get(route('classes.export-enrollments', $class));

        $expectedFilename = __('classes.export_filename', [
            'class' => Str::slug($class->class_name),
            'date' => now()->format('Ymd_His'),
        ]);

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->assertStringContainsString($expectedFilename, $response->headers->get('Content-Disposition'));

        $content = $response->streamedContent();
        $this->assertNotEmpty($content);
        $this->assertStringStartsWith('PK', $content, 'Excel export should be a ZIP archive starting with PK');

        Carbon::setTestNow();
    }
}
