<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseClass;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Lang;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class ClassExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_class_enrollments_can_be_exported_to_excel(): void
    {
        Carbon::setTestNow('2025-10-21 10:00:00');

        $user = User::factory()->create();
        $this->actingAs($user);

        $category = Category::create([
            'name_en' => 'IT',
            'name_ar' => 'تقنية المعلومات',
            'order' => 1,
            'status' => true,
            'show' => true,
            'parent_id' => 0,
        ]);

        $course = Course::create([
            'name' => 'Laravel Basics',
            'code' => 'LB101',
            'department' => 'IT',
            'category_id' => $category->id,
            'is_active' => true,
        ]);

        $class = CourseClass::create([
            'class_name' => 'Laravel Evening',
            'class_code' => 'LEV-001',
            'course_id' => $course->id,
            'category_id' => $category->id,
            'start_date' => now()->subDay(),
            'end_date' => now()->addWeek(),
            'status' => 'registration',
            'default_price' => 150,
            'is_active' => true,
        ]);

        $student = Student::create([
            'student_id' => 'STU-123456',
            'full_name' => 'Test Student',
            'email' => 'student@example.com',
            'phone_primary' => '+962700000000',
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
            'total_amount' => 150,
            'paid_amount' => 50,
            'due_amount' => 100,
            'payment_status' => 'partial',
            'notes' => 'Initial deposit',
            'is_active' => true,
            'moodle_sync_status' => 'synced',
            'moodle_enrolled_at' => now(),
        ]);

        app()->setLocale('en');

        $response = $this->get(route('classes.export-enrollments', $class));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->assertDownload('laravel-evening-enrollments-20251021_100000.xlsx');

    $tempPath = tempnam(sys_get_temp_dir(), 'xlsx');
    file_put_contents($tempPath, $response->streamedContent());

        $spreadsheet = IOFactory::load($tempPath);
        $sheet = $spreadsheet->getActiveSheet();

    $this->assertSame('Test Student', $sheet->getCell('A2')->getValue());
    $this->assertSame('student@example.com', $sheet->getCell('B2')->getValue());
    $expectedPaymentStatus = Lang::get('classes.partial', locale: app()->getLocale());
    $this->assertSame($expectedPaymentStatus, (string) $sheet->getCell('E2')->getValue());
    $this->assertSame('150', (string) $sheet->getCell('F2')->getCalculatedValue());

        unlink($tempPath);
    }
}
