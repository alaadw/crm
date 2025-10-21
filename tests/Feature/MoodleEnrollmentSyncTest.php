<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseClass;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class MoodleEnrollmentSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_enrollment_can_be_synced_to_moodle(): void
    {
        config([
            'services.moodle.url' => 'https://moodle.test/webservice/rest/server.php',
            'services.moodle.token' => 'demo-token',
        ]);

        Http::fake(function ($request) {
            $data = $request->data();
            return match ($data['wsfunction'] ?? null) {
                'core_user_get_users' => Http::response(['users' => []], 200),
                'core_user_create_users' => Http::response([
                    [
                        'id' => 777,
                        'username' => $data['users[0][username]'] ?? 'student@example.com',
                    ],
                ], 200),
                'enrol_manual_enrol_users' => Http::response([], 200),
                default => Http::response([], 404),
            };
        });

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
            'moodle_course_id' => 1234,
        ]);

        $class = CourseClass::create([
            'class_name' => 'Laravel Evening Batch',
            'class_code' => 'LEV-1',
            'course_id' => $course->id,
            'category_id' => $category->id,
            'start_date' => now()->subDay(),
            'end_date' => now()->addMonth(),
            'status' => 'registration',
            'default_price' => 200,
            'is_active' => true,
            'moodle_course_id' => $course->moodle_course_id,
        ]);

        $student = Student::create([
            'student_id' => 'STU-' . Str::random(6),
            'full_name' => 'Test Student',
            'email' => 'student@example.com',
            'phone_primary' => '+962700000000',
            'country_code' => '+962',
            'reach_source' => 'Social Media',
            'department' => 'IT',
            'department_category_id' => $category->id,
        ]);

        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'course_class_id' => $class->id,
            'registered_by' => $user->id,
            'enrollment_date' => now(),
            'total_amount' => 200,
            'paid_amount' => 0,
            'due_amount' => 200,
            'payment_status' => 'not_paid',
            'is_active' => true,
        ]);

        $token = 'test-token';
        $this->withSession(['_token' => $token]);

        $response = $this->postJson(route('api.enrollments.sync-moodle', $enrollment), [], [
            'X-CSRF-TOKEN' => $token,
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'data' => [
                    'status' => 'synced',
                    'moodle_user_id' => 777,
                ],
            ]);

        $enrollment->refresh();
        $student->refresh();

        $this->assertEquals('synced', $enrollment->moodle_sync_status);
        $this->assertNotNull($enrollment->moodle_enrolled_at);
        $this->assertEquals(777, $student->moodle_user_id);
    }

    public function test_sync_requires_student_email(): void
    {
        config([
            'services.moodle.url' => 'https://moodle.test/webservice/rest/server.php',
            'services.moodle.token' => 'demo-token',
        ]);

        Http::preventStrayRequests();

        $user = User::factory()->create();
        $this->actingAs($user);

        $category = Category::create([
            'name_en' => 'English',
            'name_ar' => 'اللغة الإنجليزية',
            'order' => 1,
            'status' => true,
            'show' => true,
            'parent_id' => 0,
        ]);

        $course = Course::create([
            'name' => 'English 101',
            'code' => 'EN101',
            'department' => 'English',
            'category_id' => $category->id,
            'is_active' => true,
            'moodle_course_id' => 4321,
        ]);

        $class = CourseClass::create([
            'class_name' => 'English Evening',
            'class_code' => 'ENG-1',
            'course_id' => $course->id,
            'category_id' => $category->id,
            'start_date' => now()->subDay(),
            'end_date' => now()->addWeek(),
            'status' => 'registration',
            'default_price' => 150,
            'is_active' => true,
            'moodle_course_id' => $course->moodle_course_id,
        ]);

        $student = Student::create([
            'student_id' => 'STU-NOEMAIL',
            'full_name' => 'No Email Student',
            'email' => null,
            'phone_primary' => '+962711111111',
            'country_code' => '+962',
            'reach_source' => 'Referral',
            'department' => 'English',
            'department_category_id' => $category->id,
        ]);

        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'course_class_id' => $class->id,
            'registered_by' => $user->id,
            'enrollment_date' => now(),
            'total_amount' => 150,
            'paid_amount' => 0,
            'due_amount' => 150,
            'payment_status' => 'not_paid',
            'is_active' => true,
        ]);

        $token = 'token-two';
        $this->withSession(['_token' => $token]);

        $response = $this->postJson(route('api.enrollments.sync-moodle', $enrollment), [], [
            'X-CSRF-TOKEN' => $token,
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);

        $enrollment->refresh();
        $this->assertEquals('not_synced', $enrollment->moodle_sync_status);
    }
}
