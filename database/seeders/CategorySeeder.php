<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // Main Departments (parent_id = 0)
            [
                'id' => 33,
                'name_en' => 'Management, Accounting & Human Resources',
                'name_ar' => 'الإدارة والمحاسبة و الموارد البشرية',
                'order' => '1',
                'status' => 1,
                'parent_id' => 0,
                'show' => 1,
                'img' => '674ba2437f543.webp'
            ],
            [
                'id' => 29,
                'name_en' => 'Engineering Courses',
                'name_ar' => 'الدورات الهندسية',
                'order' => '2',
                'status' => 1,
                'parent_id' => 0,
                'show' => 1,
                'img' => '674ba2b1d09c5.webp'
            ],
            [
                'id' => 56,
                'name_en' => 'Information Technology',
                'name_ar' => 'تكنولوجيا المعلومات',
                'order' => '3',
                'status' => 1,
                'parent_id' => 0,
                'show' => 1,
                'img' => '674ba2e5af26c.webp'
            ],
            [
                'id' => 57,
                'name_en' => 'Video & Graphics Technology',
                'name_ar' => 'تقنيات الفيديو والجرافيك',
                'order' => '4',
                'status' => 1,
                'parent_id' => 0,
                'show' => 1,
                'img' => '674ba3212c77d.webp'
            ],
            [
                'id' => 47,
                'name_en' => 'Languages',
                'name_ar' => 'اللغات',
                'order' => '5',
                'status' => 1,
                'parent_id' => 0,
                'show' => 1,
                'img' => '674b90621c686.webp'
            ],

            // IT Subcategories
            [
                'id' => 3,
                'name_en' => 'Networks, Information Security & Cloud Systems',
                'name_ar' => 'الشبكات وأمن المعلومات والأنظمه السحابية',
                'order' => '1',
                'status' => 1,
                'parent_id' => 56,
                'show' => 1,
                'img' => '674ba3c1b8816.webp'
            ],
            [
                'id' => 2,
                'name_en' => 'Programming & Data Science',
                'name_ar' => 'البرمجة وعلم البيانات',
                'order' => '2',
                'status' => 1,
                'parent_id' => 56,
                'show' => 1,
                'img' => '674ba3f30ce23.webp'
            ],

            // Video & Graphics Subcategories
            [
                'id' => 45,
                'name_en' => 'Graphic Design',
                'name_ar' => 'التصميم الجرافيكي',
                'order' => '1',
                'status' => 1,
                'parent_id' => 57,
                'show' => 1,
                'img' => '674ba374baa81.webp'
            ],
            [
                'id' => 46,
                'name_en' => 'Video Editing & Film Making',
                'name_ar' => 'المونتاج و صناعة الأفلام',
                'order' => '2',
                'status' => 1,
                'parent_id' => 57,
                'show' => 1,
                'img' => '674ba3848e0bf.webp'
            ],

            // Engineering Subcategories
            [
                'id' => 48,
                'name_en' => 'Architecture Engineering',
                'name_ar' => 'هندسة العماره',
                'order' => '1',
                'status' => 1,
                'parent_id' => 29,
                'show' => 1,
                'img' => '674ba43f8977f.webp'
            ],
            [
                'id' => 50,
                'name_en' => 'Civil Engineering',
                'name_ar' => 'الهندسة المدنية',
                'order' => '2',
                'status' => 1,
                'parent_id' => 29,
                'show' => 1,
                'img' => '674ba4b68d161.webp'
            ],
            [
                'id' => 51,
                'name_en' => 'Electrical, Mechanical & Industrial Engineering',
                'name_ar' => 'الهندسة الكهربائية والميكانيكية والصناعية',
                'order' => '3',
                'status' => 1,
                'parent_id' => 29,
                'show' => 1,
                'img' => '674ba5302ceac.webp'
            ],
            [
                'id' => 55,
                'name_en' => 'Biomedical Engineering',
                'name_ar' => 'الهندسة الطبية',
                'order' => '4',
                'status' => 1,
                'parent_id' => 29,
                'show' => 1,
                'img' => '674ba6c7f1e85.webp'
            ],

            // Language Subcategories
            [
                'id' => 49,
                'name_en' => 'General English',
                'name_ar' => 'General English',
                'order' => '1',
                'status' => 1,
                'parent_id' => 47,
                'show' => 1,
                'img' => '674ba477b9f3c.webp'
            ],
            [
                'id' => 52,
                'name_en' => 'English Conversation',
                'name_ar' => 'English Conversation',
                'order' => '2',
                'status' => 1,
                'parent_id' => 47,
                'show' => 1,
                'img' => '674ba586c913b.webp'
            ],
            [
                'id' => 53,
                'name_en' => 'English For Business',
                'name_ar' => 'English For Business',
                'order' => '3',
                'status' => 1,
                'parent_id' => 47,
                'show' => 1,
                'img' => '674ba6228a19d.webp'
            ],
            [
                'id' => 54,
                'name_en' => 'IELTS',
                'name_ar' => 'IELTS',
                'order' => '4',
                'status' => 1,
                'parent_id' => 47,
                'show' => 1,
                'img' => '674ba66ee9723.webp'
            ],
            [
                'id' => 58,
                'name_en' => 'English Language Diploma',
                'name_ar' => 'دبلوم اللغة الإنجليزية',
                'order' => '5',
                'status' => 1,
                'parent_id' => 47,
                'show' => 1,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
