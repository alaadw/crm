<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Course;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = [
            // Management & Accounting Courses (Category ID: 33)
            [
                'name' => 'CMA Part 1',
                'name_en' => 'CMA Part 1',
                'name_ar' => 'محاسب إداري معتمد - الجزء الأول',
                'code' => 'CMA-P1',
                'description' => 'Certified Management Accountant Part 1',
                'description_en' => 'Certified Management Accountant Part 1',
                'description_ar' => 'دورة محاسب إداري معتمد - الجزء الأول',
                'department' => 'Management',
                'category_id' => 33,
                'is_active' => true,
            ],
            [
                'name' => 'CMA Part 2',
                'name_en' => 'CMA Part 2',
                'name_ar' => 'محاسب إداري معتمد - الجزء الثاني',
                'code' => 'CMA-P2',
                'description' => 'Certified Management Accountant Part 2',
                'description_en' => 'Certified Management Accountant Part 2',
                'description_ar' => 'دورة محاسب إداري معتمد - الجزء الثاني',
                'department' => 'Management',
                'category_id' => 33,
                'is_active' => true,
            ],
            [
                'name' => 'CPA',
                'name_en' => 'CPA',
                'name_ar' => 'محاسب قانوني معتمد',
                'code' => 'CPA-101',
                'description' => 'Certified Public Accountant',
                'description_en' => 'Certified Public Accountant',
                'description_ar' => 'دورة محاسب قانوني معتمد',
                'department' => 'Management',
                'category_id' => 33,
                'is_active' => true,
            ],
            [
                'name' => 'Business Administration',
                'name_en' => 'Business Administration',
                'name_ar' => 'إدارة الأعمال',
                'code' => 'BA-101',
                'description' => 'Fundamentals of Business Administration',
                'description_en' => 'Fundamentals of Business Administration',
                'description_ar' => 'أساسيات إدارة الأعمال',
                'department' => 'Management',
                'category_id' => 33,
                'is_active' => true,
            ],

            // Programming & Data Science Courses (Category ID: 2)
            [
                'name' => 'Web Development',
                'name_en' => 'Web Development',
                'name_ar' => 'تطوير المواقع',
                'code' => 'WEB-101',
                'description' => 'Full Stack Web Development Course',
                'description_en' => 'Full Stack Web Development Course',
                'description_ar' => 'دورة تطوير المواقع المتكاملة',
                'department' => 'IT',
                'category_id' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Mobile App Development',
                'name_en' => 'Mobile App Development',
                'name_ar' => 'تطوير تطبيقات الهاتف',
                'code' => 'MOB-101',
                'description' => 'iOS and Android Mobile App Development',
                'description_en' => 'iOS and Android Mobile App Development',
                'description_ar' => 'تطوير تطبيقات الآيفون والأندرويد',
                'department' => 'IT',
                'category_id' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Data Science',
                'name_en' => 'Data Science',
                'name_ar' => 'علم البيانات',
                'code' => 'DS-101',
                'description' => 'Introduction to Data Science and Analytics',
                'description_en' => 'Introduction to Data Science and Analytics',
                'description_ar' => 'مقدمة في علم البيانات والتحليل',
                'department' => 'IT',
                'category_id' => 2,
                'is_active' => true,
            ],

            // Networks & Security Courses (Category ID: 3)
            [
                'name' => 'Cybersecurity',
                'name_en' => 'Cybersecurity',
                'name_ar' => 'الأمن السيبراني',
                'code' => 'CYB-101',
                'description' => 'Cybersecurity Fundamentals',
                'description_en' => 'Cybersecurity Fundamentals',
                'description_ar' => 'أساسيات الأمن السيبراني',
                'department' => 'IT',
                'category_id' => 3,
                'is_active' => true,
            ],

            // Civil Engineering Courses (Category ID: 50)
            [
                'name' => 'Civil Engineering Fundamentals',
                'name_en' => 'Civil Engineering Fundamentals',
                'name_ar' => 'أساسيات الهندسة المدنية',
                'code' => 'CE-101',
                'description' => 'Basic principles of Civil Engineering',
                'description_en' => 'Basic principles of Civil Engineering',
                'description_ar' => 'المبادئ الأساسية للهندسة المدنية',
                'department' => 'Engineering',
                'category_id' => 50,
                'is_active' => true,
            ],

            // Mechanical Engineering Courses (Category ID: 51)
            [
                'name' => 'Mechanical Engineering',
                'name_en' => 'Mechanical Engineering',
                'name_ar' => 'الهندسة الميكانيكية',
                'code' => 'ME-101',
                'description' => 'Introduction to Mechanical Engineering',
                'description_en' => 'Introduction to Mechanical Engineering',
                'description_ar' => 'مقدمة في الهندسة الميكانيكية',
                'department' => 'Engineering',
                'category_id' => 51,
                'is_active' => true,
            ],
            [
                'name' => 'Electrical Engineering',
                'name_en' => 'Electrical Engineering',
                'name_ar' => 'الهندسة الكهربائية',
                'code' => 'EE-101',
                'description' => 'Electrical Engineering Basics',
                'description_en' => 'Electrical Engineering Basics',
                'description_ar' => 'أساسيات الهندسة الكهربائية',
                'department' => 'Engineering',
                'category_id' => 51,
                'is_active' => true,
            ],

            // Architecture Engineering Courses (Category ID: 48)
            [
                'name' => 'AutoCAD Professional',
                'name_en' => 'AutoCAD Professional',
                'name_ar' => 'أوتوكاد احترافي',
                'code' => 'CAD-PRO',
                'description' => 'Professional AutoCAD Training',
                'description_en' => 'Professional AutoCAD Training',
                'description_ar' => 'تدريب أوتوكاد احترافي',
                'department' => 'Engineering',
                'category_id' => 48,
                'is_active' => true,
            ],

            // IELTS Courses (Category ID: 54)
            [
                'name' => 'IELTS Preparation',
                'name_en' => 'IELTS Preparation',
                'name_ar' => 'تحضير آيلتس',
                'code' => 'IELTS-PREP',
                'description' => 'Comprehensive IELTS Exam Preparation',
                'description_en' => 'Comprehensive IELTS Exam Preparation',
                'description_ar' => 'تحضير شامل لامتحان آيلتس',
                'department' => 'English',
                'category_id' => 54,
                'is_active' => true,
            ],

            // General English Courses (Category ID: 49)
            [
                'name' => 'TOEFL Preparation',
                'name_en' => 'TOEFL Preparation',
                'name_ar' => 'تحضير توفل',
                'code' => 'TOEFL-PREP',
                'description' => 'TOEFL Exam Preparation Course',
                'description_en' => 'TOEFL Exam Preparation Course',
                'description_ar' => 'دورة تحضير امتحان توفل',
                'department' => 'English',
                'category_id' => 49,
                'is_active' => true,
            ],

            // Business English Courses (Category ID: 53)
            [
                'name' => 'Business English',
                'name_en' => 'Business English',
                'name_ar' => 'الإنجليزية التجارية',
                'code' => 'BIZ-ENG',
                'description' => 'English for Business Communications',
                'description_en' => 'English for Business Communications',
                'description_ar' => 'الإنجليزية للتواصل التجاري',
                'department' => 'English',
                'category_id' => 53,
                'is_active' => true,
            ],

            // English Diploma Courses (Category ID: 58)
            [
                'name' => 'Academic Writing',
                'name_en' => 'Academic Writing',
                'name_ar' => 'الكتابة الأكاديمية',
                'code' => 'ACAD-WRITE',
                'description' => 'Academic Writing Skills',
                'description_en' => 'Academic Writing Skills',
                'description_ar' => 'مهارات الكتابة الأكاديمية',
                'department' => 'English',
                'category_id' => 58,
                'is_active' => true,
            ],
        ];

        foreach ($courses as $course) {
            Course::create($course);
        }
    }
}
