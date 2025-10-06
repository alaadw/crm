<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Category;
use Illuminate\Database\Seeder;

class CourseUpdateSeeder extends Seeder
{
    /**
     * Run the database migrations.
     */
    public function run(): void
    {
        $courses = [
            [
                'id' => 1,
                'name' => 'دورة برمجة المواقع الإلكترونية المتقدمة - ASP.Net',
                'name_en' => 'Advanced Web Development - ASP.Net',
                'name_ar' => 'دورة برمجة المواقع الإلكترونية المتقدمة - ASP.Net',
                'category_id' => 56, // Information Technology
            ],
            [
                'id' => 2,
                'name' => 'دورة برمجة المواقع الإلكترونية المتقدمة - PHP',
                'name_en' => 'Advanced Web Development - PHP',
                'name_ar' => 'دورة برمجة المواقع الإلكترونية المتقدمة - PHP',
                'category_id' => 56, // Information Technology
            ],
            [
                'id' => 3,
                'name' => 'دورة فحص البرمجيات - QA Software Testing',
                'name_en' => 'QA Software Testing',
                'name_ar' => 'دورة فحص البرمجيات - QA Software Testing',
                'category_id' => 56, // Information Technology
            ],
            [
                'id' => 4,
                'name' => 'دورة فحص البرمجيات المتقدم - QA SW Testing Automation',
                'name_en' => 'Advanced QA SW Testing Automation',
                'name_ar' => 'دورة فحص البرمجيات المتقدم - QA SW Testing Automation',
                'category_id' => 56, // Information Technology
            ],
            [
                'id' => 5,
                'name' => 'دورة تأهيل المحاسبين لسوق العمل',
                'name_en' => 'Accounting Professional Preparation',
                'name_ar' => 'دورة تأهيل المحاسبين لسوق العمل',
                'category_id' => 33, // Management, Accounting & Human Resources
            ],
            [
                'id' => 6,
                'name' => 'دورة برمجة المواقع الإلكترونية مبتدئ - PHP',
                'name_en' => 'Beginner Web Development - PHP',
                'name_ar' => 'دورة برمجة المواقع الإلكترونية مبتدئ - PHP',
                'category_id' => 56, // Information Technology
            ],
            [
                'id' => 7,
                'name' => 'دورة برنامج After Effects',
                'name_en' => 'After Effects Course',
                'name_ar' => 'دورة برنامج After Effects',
                'category_id' => 57, // Video & Graphics Technology
            ],
            [
                'id' => 13,
                'name' => 'دورة برنامج Adobe Premiere',
                'name_en' => 'Adobe Premiere Course',
                'name_ar' => 'دورة برنامج Adobe Premiere',
                'category_id' => 57, // Video & Graphics Technology
            ],
            [
                'id' => 21,
                'name' => 'دورة قواعد البيانات - ORACLE',
                'name_en' => 'Database Course - ORACLE',
                'name_ar' => 'دورة قواعد البيانات - ORACLE',
                'category_id' => 56, // Information Technology
            ],
            [
                'id' => 25,
                'name' => 'دورة لغة الآلة - Python - Machine Learning - AI',
                'name_en' => 'Python Machine Learning - AI',
                'name_ar' => 'دورة لغة الآلة - Python - Machine Learning - AI',
                'category_id' => 56, // Information Technology
            ],
            [
                'id' => 26,
                'name' => 'دورة برمجة المواقع الإلكترونية (مبتدئ) - Asp.net',
                'name_en' => 'Beginner Web Development - Asp.net',
                'name_ar' => 'دورة برمجة المواقع الإلكترونية (مبتدئ) - Asp.net',
                'category_id' => 56, // Information Technology
            ],
            [
                'id' => 27,
                'name' => 'دورة مساعد شبكات معتمد CCNA',
                'name_en' => 'CCNA Network Assistant',
                'name_ar' => 'دورة مساعد شبكات معتمد CCNA',
                'category_id' => 56, // Information Technology
            ],
            [
                'id' => 28,
                'name' => 'دورة علم البيانات - Deep Learning',
                'name_en' => 'Data Science - Deep Learning',
                'name_ar' => 'دورة علم البيانات - Deep Learning',
                'category_id' => 56, // Information Technology
            ],
            [
                'id' => 29,
                'name' => 'دورة تطوير تطبيقات الهواتف الذكية - Kotlin',
                'name_en' => 'Mobile App Development - Kotlin',
                'name_ar' => 'دورة تطوير تطبيقات الهواتف الذكية - Kotlin',
                'category_id' => 56, // Information Technology
            ],
            [
                'id' => 30,
                'name' => 'دورة (مبتدئ) - Cyber Security',
                'name_en' => 'Beginner Cyber Security',
                'name_ar' => 'دورة (مبتدئ) - Cyber Security',
                'category_id' => 56, // Information Technology
            ],
            [
                'id' => 31,
                'name' => 'دورة (متقدم) - Cyber Security Advanced',
                'name_en' => 'Advanced Cyber Security',
                'name_ar' => 'دورة (متقدم) - Cyber Security Advanced',
                'category_id' => 56, // Information Technology
            ],
            [
                'id' => 32,
                'name' => 'دورة برمجة تطبيقات الهواتف الذكية - Flutter',
                'name_en' => 'Mobile App Development - Flutter',
                'name_ar' => 'دورة برمجة تطبيقات الهواتف الذكية - Flutter',
                'category_id' => 56, // Information Technology
            ],
            [
                'id' => 33,
                'name' => 'دورة برمجة تطبيقات الهواتف الذكية المتقدمة - Advanced Mobile',
                'name_en' => 'Advanced Mobile App Development',
                'name_ar' => 'دورة برمجة تطبيقات الهواتف الذكية المتقدمة - Advanced Mobile',
                'category_id' => 56, // Information Technology
            ],
            [
                'id' => 34,
                'name' => 'دورة صناعة الفيديوهات الرقمية - Video Promotion',
                'name_en' => 'Digital Video Production - Video Promotion',
                'name_ar' => 'دورة صناعة الفيديوهات الرقمية - Video Promotion',
                'category_id' => 57, // Video & Graphics Technology
            ],
            [
                'id' => 35,
                'name' => 'دورة برمجة تطبيقات الهواتف الذكية المتقدمة - Kotlin',
                'name_en' => 'Advanced Mobile App Development - Kotlin',
                'name_ar' => 'دورة برمجة تطبيقات الهواتف الذكية المتقدمة - Kotlin',
                'category_id' => 56, // Information Technology
            ],
            [
                'id' => 36,
                'name' => 'دورة إدارة قواعد البيانات - Oracle Database Administration',
                'name_en' => 'Oracle Database Administration',
                'name_ar' => 'دورة إدارة قواعد البيانات - Oracle Database Administration',
                'category_id' => 56, // Information Technology
            ],
            [
                'id' => 37,
                'name' => 'دورة تصميم واجهات المستخدم - Front-End Web Design',
                'name_en' => 'Front-End Web Design',
                'name_ar' => 'دورة تصميم واجهات المستخدم - Front-End Web Design',
                'category_id' => 56, // Information Technology
            ],
            [
                'id' => 38,
                'name' => 'دورة ريفيت المعماري Revit Architecture',
                'name_en' => 'Revit Architecture',
                'name_ar' => 'دورة ريفيت المعماري Revit Architecture',
                'category_id' => 29, // Engineering Courses
            ],
            [
                'id' => 39,
                'name' => 'دورة 3d max معماري',
                'name_en' => 'Architectural 3D Max',
                'name_ar' => 'دورة 3d max معماري',
                'category_id' => 29, // Engineering Courses
            ],
            [
                'id' => 40,
                'name' => 'دورة لوميون الشاملة Lumion',
                'name_en' => 'Comprehensive Lumion Course',
                'name_ar' => 'دورة لوميون الشاملة Lumion',
                'category_id' => 29, // Engineering Courses
            ],
            [
                'id' => 41,
                'name' => 'دورة 3Ds Max Rendering V-RAY',
                'name_en' => '3Ds Max Rendering V-RAY',
                'name_ar' => 'دورة 3Ds Max Rendering V-RAY',
                'category_id' => 29, // Engineering Courses
            ],
            [
                'id' => 42,
                'name' => 'دورة ريفيت للهندسة المعمارية المتقدمة Revit Architecture Advanced',
                'name_en' => 'Advanced Revit Architecture',
                'name_ar' => 'دورة ريفيت للهندسة المعمارية المتقدمة Revit Architecture Advanced',
                'category_id' => 29, // Engineering Courses
            ],
            [
                'id' => 43,
                'name' => 'دورة الفوتوشوب المعماري - Architectural Photoshop',
                'name_en' => 'Architectural Photoshop',
                'name_ar' => 'دورة الفوتوشوب المعماري - Architectural Photoshop',
                'category_id' => 29, // Engineering Courses
            ],
            [
                'id' => 44,
                'name' => 'دورة شوب دروينج المعماري Shop Drawing',
                'name_en' => 'Architectural Shop Drawing',
                'name_ar' => 'دورة شوب دروينج المعماري Shop Drawing',
                'category_id' => 29, // Engineering Courses
            ],
            [
                'id' => 45,
                'name' => 'دورة سكتش اب SketchUp',
                'name_en' => 'SketchUp Course',
                'name_ar' => 'دورة سكتش اب SketchUp',
                'category_id' => 29, // Engineering Courses
            ],
            [
                'id' => 46,
                'name' => 'دورة الريفيت الانشائية Structural Revit',
                'name_en' => 'Structural Revit',
                'name_ar' => 'دورة الريفيت الانشائية Structural Revit',
                'category_id' => 29, // Engineering Courses
            ],
            [
                'id' => 47,
                'name' => 'دورة برنامج بروكون Prokon',
                'name_en' => 'Prokon Software Course',
                'name_ar' => 'دورة برنامج بروكون Prokon',
                'category_id' => 29, // Engineering Courses
            ],
            [
                'id' => 48,
                'name' => 'دورة التحليل الانشائي - ETABS & SAFE',
                'name_en' => 'Structural Analysis - ETABS & SAFE',
                'name_ar' => 'دورة التحليل الانشائي - ETABS & SAFE',
                'category_id' => 29, // Engineering Courses
            ],
            [
                'id' => 49,
                'name' => 'دورة برنامج الريفيت ميب Revit MEP',
                'name_en' => 'Revit MEP Course',
                'name_ar' => 'دورة برنامج الريفيت ميب Revit MEP',
                'category_id' => 29, // Engineering Courses
            ],
            [
                'id' => 50,
                'name' => 'دورة التصميم الصناعي المتقدم - CAD SOLIDWORKS',
                'name_en' => 'Advanced Industrial Design - CAD SOLIDWORKS',
                'name_ar' => 'دورة التصميم الصناعي المتقدم - CAD SOLIDWORKS',
                'category_id' => 29, // Engineering Courses
            ],
            [
                'id' => 51,
                'name' => 'دورة اللغة الإنجليزية للأعمال-Business English Level A',
                'name_en' => 'Business English Level A',
                'name_ar' => 'دورة اللغة الإنجليزية للأعمال-Business English Level A',
                'category_id' => 58, // Languages
            ],
            [
                'id' => 52,
                'name' => 'دورة محترف إدارة سلسلة الإمداد SCMP',
                'name_en' => 'Supply Chain Management Professional SCMP',
                'name_ar' => 'دورة محترف إدارة سلسلة الإمداد SCMP',
                'category_id' => 33, // Management, Accounting & Human Resources
            ],
            [
                'id' => 53,
                'name' => 'دورة برنامج Adobe Photoshop',
                'name_en' => 'Adobe Photoshop Course',
                'name_ar' => 'دورة برنامج Adobe Photoshop',
                'category_id' => 57, // Video & Graphics Technology
            ],
            [
                'id' => 55,
                'name' => 'دورة برنامج Adobe Illustrator',
                'name_en' => 'Adobe Illustrator Course',
                'name_ar' => 'دورة برنامج Adobe Illustrator',
                'category_id' => 57, // Video & Graphics Technology
            ],
            [
                'id' => 56,
                'name' => 'دورة تصميم وتحسين واجهات المستخدم - UI/UX Design',
                'name_en' => 'UI/UX Design Course',
                'name_ar' => 'دورة تصميم وتحسين واجهات المستخدم - UI/UX Design',
                'category_id' => 57, // Video & Graphics Technology
            ],
            [
                'id' => 59,
                'name' => 'دورة المُحترف المعتمد في التسويق الرقمي - (CDMP)',
                'name_en' => 'Certified Digital Marketing Professional (CDMP)',
                'name_ar' => 'دورة المُحترف المعتمد في التسويق الرقمي - (CDMP)',
                'category_id' => 33, // Management, Accounting & Human Resources
            ],
            [
                'id' => 60,
                'name' => 'دورة أخصائي موارد بشرية - aPHRi',
                'name_en' => 'HR Specialist - aPHRi',
                'name_ar' => 'دورة أخصائي موارد بشرية - aPHRi',
                'category_id' => 33, // Management, Accounting & Human Resources
            ],
            [
                'id' => 61,
                'name' => 'دورة محترف موارد بشرية - PHRi',
                'name_en' => 'HR Professional - PHRi',
                'name_ar' => 'دورة محترف موارد بشرية - PHRi',
                'category_id' => 33, // Management, Accounting & Human Resources
            ],
            [
                'id' => 62,
                'name' => 'دورة إدارة المشاريع الإحترافية PMP',
                'name_en' => 'Professional Project Management PMP',
                'name_ar' => 'دورة إدارة المشاريع الإحترافية PMP',
                'category_id' => 33, // Management, Accounting & Human Resources
            ],
            [
                'id' => 63,
                'name' => 'دورة إدارة المشاريع الرشيقة - PMI ACP (Agile)',
                'name_en' => 'Agile Project Management - PMI ACP',
                'name_ar' => 'دورة إدارة المشاريع الرشيقة - PMI ACP (Agile)',
                'category_id' => 33, // Management, Accounting & Human Resources
            ],
            [
                'id' => 64,
                'name' => 'دورة إدارة الجودة الشاملة - (TQM)',
                'name_en' => 'Total Quality Management (TQM)',
                'name_ar' => 'دورة إدارة الجودة الشاملة - (TQM)',
                'category_id' => 33, // Management, Accounting & Human Resources
            ],
            [
                'id' => 65,
                'name' => 'دورة MS Project',
                'name_en' => 'MS Project Course',
                'name_ar' => 'دورة MS Project',
                'category_id' => 33, // Management, Accounting & Human Resources
            ],
            [
                'id' => 66,
                'name' => 'دورة ماجستير إدارة الأعمال - Mini MBA',
                'name_en' => 'Mini MBA',
                'name_ar' => 'دورة ماجستير إدارة الأعمال - Mini MBA',
                'category_id' => 33, // Management, Accounting & Human Resources
            ],
            [
                'id' => 68,
                'name' => 'دورة الأنظمة السحابية - Cloud Computing',
                'name_en' => 'Cloud Computing',
                'name_ar' => 'دورة الأنظمة السحابية - Cloud Computing',
                'category_id' => 56, // Information Technology
            ],
            [
                'id' => 69,
                'name' => 'دورة اللغة الإنجليزية للأعمال-Business English Level B',
                'name_en' => 'Business English Level B',
                'name_ar' => 'دورة اللغة الإنجليزية للأعمال-Business English Level B',
                'category_id' => 58, // Languages
            ],
            [
                'id' => 70,
                'name' => 'دورة المحادثة باللغة الإنجليزية-English Conversation',
                'name_en' => 'English Conversation',
                'name_ar' => 'دورة المحادثة باللغة الإنجليزية-English Conversation',
                'category_id' => 58, // Languages
            ],
            [
                'id' => 71,
                'name' => 'دورة اللغة الإنجليزية المتقدمة-Advanced English',
                'name_en' => 'Advanced English',
                'name_ar' => 'دورة اللغة الإنجليزية المتقدمة-Advanced English',
                'category_id' => 58, // Languages
            ],
            [
                'id' => 72,
                'name' => 'دورة اللغة الإنجليزية الابتدائية-Elementary English',
                'name_en' => 'Elementary English',
                'name_ar' => 'دورة اللغة الإنجليزية الابتدائية-Elementary English',
                'category_id' => 58, // Languages
            ],
            [
                'id' => 73,
                'name' => 'دورة اللغة الانجليزية المتوسطة-Intermediate English',
                'name_en' => 'Intermediate English',
                'name_ar' => 'دورة اللغة الانجليزية المتوسطة-Intermediate English',
                'category_id' => 58, // Languages
            ],
            [
                'id' => 74,
                'name' => 'دورة اللغة الانجليزية قبل المتوسط-Pre intermediate English',
                'name_en' => 'Pre intermediate English',
                'name_ar' => 'دورة اللغة الانجليزية قبل المتوسط-Pre intermediate English',
                'category_id' => 58, // Languages
            ],
            [
                'id' => 75,
                'name' => 'دورة اللغة الانجليزية فوق المتوسط-Upper Intermediate English',
                'name_en' => 'Upper Intermediate English',
                'name_ar' => 'دورة اللغة الانجليزية فوق المتوسط-Upper Intermediate English',
                'category_id' => 58, // Languages
            ],
            [
                'id' => 76,
                'name' => 'IELTS Preparation Course',
                'name_en' => 'IELTS Preparation Course',
                'name_ar' => 'دورة تحضير IELTS',
                'category_id' => 58, // Languages
            ],
            [
                'id' => 77,
                'name' => 'محادثة باللغة الإنجليزية-English Conversation-Level A',
                'name_en' => 'English Conversation-Level A',
                'name_ar' => 'محادثة باللغة الإنجليزية-English Conversation-Level A',
                'category_id' => 58, // Languages
            ],
            [
                'id' => 78,
                'name' => 'دورة الإختراق الأخلاقي - Certified Ethical Hacker',
                'name_en' => 'Certified Ethical Hacker',
                'name_ar' => 'دورة الإختراق الأخلاقي - Certified Ethical Hacker',
                'category_id' => 56, // Information Technology
            ],
            [
                'id' => 79,
                'name' => 'دورة البريمافيرا Primavera P6',
                'name_en' => 'Primavera P6',
                'name_ar' => 'دورة البريمافيرا Primavera P6',
                'category_id' => 29, // Engineering Courses
            ],
            [
                'id' => 80,
                'name' => 'دورة برنامج Revit MEP المتقدمة',
                'name_en' => 'Advanced Revit MEP',
                'name_ar' => 'دورة برنامج Revit MEP المتقدمة',
                'category_id' => 29, // Engineering Courses
            ],
            [
                'id' => 81,
                'name' => 'دورة نظام الجودة الصناعية - QMS',
                'name_en' => 'Quality Management System - QMS',
                'name_ar' => 'دورة نظام الجودة الصناعية - QMS',
                'category_id' => 33, // Management, Accounting & Human Resources
            ],
            [
                'id' => 83,
                'name' => 'دورة حساب الكميات - Quantity Surveying',
                'name_en' => 'Quantity Surveying',
                'name_ar' => 'دورة حساب الكميات - Quantity Surveying',
                'category_id' => 29, // Engineering Courses
            ],
            [
                'id' => 84,
                'name' => 'دورة سلاسل الإمداد والتزويد - Supply Chain Management',
                'name_en' => 'Supply Chain Management',
                'name_ar' => 'دورة سلاسل الإمداد والتزويد - Supply Chain Management',
                'category_id' => 33, // Management, Accounting & Human Resources
            ],
            [
                'id' => 85,
                'name' => 'دورة التصميم والتصنيع 3D Printing باستخدام CAD',
                'name_en' => '3D Printing Design and Manufacturing using CAD',
                'name_ar' => 'دورة التصميم والتصنيع 3D Printing باستخدام CAD',
                'category_id' => 29, // Engineering Courses
            ],
            [
                'id' => 86,
                'name' => 'دورة تصميم وتطوير الألعاب - Game Development',
                'name_en' => 'Game Development',
                'name_ar' => 'دورة تصميم وتطوير الألعاب - Game Development',
                'category_id' => 56, // Information Technology
            ],
            [
                'id' => 87,
                'name' => 'دورة الذكاء الاصطناعي في القطاع الطبي - AI Machine Learning',
                'name_en' => 'AI Machine Learning in Medical Sector',
                'name_ar' => 'دورة الذكاء الاصطناعي في القطاع الطبي - AI Machine Learning',
                'category_id' => 56, // Information Technology
            ],
            [
                'id' => 88,
                'name' => 'دورة التصميم الصناعي - SOLIDWORKS',
                'name_en' => 'Industrial Design - SOLIDWORKS',
                'name_ar' => 'دورة التصميم الصناعي - SOLIDWORKS',
                'category_id' => 29, // Engineering Courses
            ],
            [
                'id' => 89,
                'name' => 'دورة تحليل الأنظمة البرمجية - Business Analysis',
                'name_en' => 'Software Systems Analysis - Business Analysis',
                'name_ar' => 'دورة تحليل الأنظمة البرمجية - Business Analysis',
                'category_id' => 56, // Information Technology
            ],
            [
                'id' => 90,
                'name' => 'دورة تصميم أنظمة الصرف الصحي والطرق-CIVIL 3D',
                'name_en' => 'Sewage and Road Systems Design-CIVIL 3D',
                'name_ar' => 'دورة تصميم أنظمة الصرف الصحي والطرق-CIVIL 3D',
                'category_id' => 29, // Engineering Courses
            ],
            [
                'id' => 91,
                'name' => 'دورة بناء تطبيقات الخادم باستخدام-Node.js',
                'name_en' => 'Server Application Development using Node.js',
                'name_ar' => 'دورة بناء تطبيقات الخادم باستخدام-Node.js',
                'category_id' => 56, // Information Technology
            ],
            [
                'id' => 92,
                'name' => 'دورة جداول البيانات - Excel',
                'name_en' => 'Spreadsheets - Excel',
                'name_ar' => 'دورة جداول البيانات - Excel',
                'category_id' => 33, // Management, Accounting & Human Resources
            ],
            [
                'id' => 93,
                'name' => 'دورة تصميم انظمة الطاقة الشمسية - PV System',
                'name_en' => 'Solar Energy Systems Design - PV System',
                'name_ar' => 'دورة تصميم انظمة الطاقة الشمسية - PV System',
                'category_id' => 29, // Engineering Courses
            ],
            [
                'id' => 94,
                'name' => 'محادثة باللغة الإنجليزية- English Conversation- Level B',
                'name_en' => 'English Conversation- Level B',
                'name_ar' => 'محادثة باللغة الإنجليزية- English Conversation- Level B',
                'category_id' => 58, // Languages
            ],
            [
                'id' => 95,
                'name' => 'دورة محادثة باللغة الإنجليزية- English Conversation',
                'name_en' => 'English Conversation Course',
                'name_ar' => 'دورة محادثة باللغة الإنجليزية- English Conversation',
                'category_id' => 58, // Languages
            ],
            [
                'id' => 97,
                'name' => '6 دورة سيجما الحزام الاصفر - Lean Six Sigma Yellow Belt',
                'name_en' => 'Lean Six Sigma Yellow Belt',
                'name_ar' => '6 دورة سيجما الحزام الاصفر - Lean Six Sigma Yellow Belt',
                'category_id' => 33, // Management, Accounting & Human Resources
            ],
            [
                'id' => 98,
                'name' => 'دورة التحكم الصناعي الكلاسيكي PLC',
                'name_en' => 'Classical Industrial Control PLC',
                'name_ar' => 'دورة التحكم الصناعي الكلاسيكي PLC',
                'category_id' => 29, // Engineering Courses
            ],
            [
                'id' => 99,
                'name' => 'دورة تنفيذ المنشات المعدنية-Steel Structural Design',
                'name_en' => 'Steel Structural Design',
                'name_ar' => 'دورة تنفيذ المنشات المعدنية-Steel Structural Design',
                'category_id' => 29, // Engineering Courses
            ],
            [
                'id' => 100,
                'name' => 'دورة المدير المالي المعتمد - Certified Financial Manager',
                'name_en' => 'Certified Financial Manager',
                'name_ar' => 'دورة المدير المالي المعتمد - Certified Financial Manager',
                'category_id' => 33, // Management, Accounting & Human Resources
            ],
            [
                'id' => 101,
                'name' => 'دورة اوتوكاد الاحترافية - AutoCAD',
                'name_en' => 'Professional AutoCAD',
                'name_ar' => 'دورة اوتوكاد الاحترافية - AutoCAD',
                'category_id' => 29, // Engineering Courses
            ],
            [
                'id' => 102,
                'name' => 'دورة مسؤول الشبكة - Server, Network, and Cloud Administration',
                'name_en' => 'Server, Network, and Cloud Administration',
                'name_ar' => 'دورة مسؤول الشبكة - Server, Network, and Cloud Administration',
                'category_id' => 56, // Information Technology
            ],
            [
                'id' => 103,
                'name' => 'دورة نظام إدارة المباني BMS',
                'name_en' => 'Building Management System BMS',
                'name_ar' => 'دورة نظام إدارة المباني BMS',
                'category_id' => 29, // Engineering Courses
            ],
            [
                'id' => 104,
                'name' => 'دورة أنظمة الجهد العالي-High Voltage Systems',
                'name_en' => 'High Voltage Systems',
                'name_ar' => 'دورة أنظمة الجهد العالي-High Voltage Systems',
                'category_id' => 29, // Engineering Courses
            ],
            [
                'id' => 105,
                'name' => 'دورة أنظمة التيار المنخفض – Low Current Systems',
                'name_en' => 'Low Current Systems',
                'name_ar' => 'دورة أنظمة التيار المنخفض – Low Current Systems',
                'category_id' => 29, // Engineering Courses
            ],
            [
                'id' => 106,
                'name' => 'دورة ChatGPT للمبتدئين',
                'name_en' => 'ChatGPT for Beginners',
                'name_ar' => 'دورة ChatGPT للمبتدئين',
                'category_id' => 56, // Information Technology
            ],
            [
                'id' => 107,
                'name' => 'دورة التحكم الآلي المتقدمة - PLC SCADA',
                'name_en' => 'Advanced Automation Control - PLC SCADA',
                'name_ar' => 'دورة التحكم الآلي المتقدمة - PLC SCADA',
                'category_id' => 29, // Engineering Courses
            ],
            [
                'id' => 108,
                'name' => 'دورة ذكاء الأعمال Power BI',
                'name_en' => 'Business Intelligence Power BI',
                'name_ar' => 'دورة ذكاء الأعمال Power BI',
                'category_id' => 56, // Information Technology
            ],
            [
                'id' => 109,
                'name' => 'تصميم المجوهرات وبرنامج Jewelry Design & Rhino',
                'name_en' => 'Jewelry Design & Rhino',
                'name_ar' => 'تصميم المجوهرات وبرنامج Jewelry Design & Rhino',
                'category_id' => 57, // Video & Graphics Technology
            ],
            [
                'id' => 110,
                'name' => 'دورة تصميم مجوهرات متقدمة - Rhino For Jewelry Design',
                'name_en' => 'Advanced Jewelry Design - Rhino For Jewelry Design',
                'name_ar' => 'دورة تصميم مجوهرات متقدمة - Rhino For Jewelry Design',
                'category_id' => 57, // Video & Graphics Technology
            ],
            [
                'id' => 111,
                'name' => 'دورة أوتوكاد للرسم الكهربائي - AutoCAD Electrical',
                'name_en' => 'AutoCAD Electrical',
                'name_ar' => 'دورة أوتوكاد للرسم الكهربائي - AutoCAD Electrical',
                'category_id' => 29, // Engineering Courses
            ],
            [
                'id' => 112,
                'name' => 'دورة المحاسب الإداري المعتمد (CMA) - الجزء الأول',
                'name_en' => 'Certified Management Accountant (CMA) - Part 1',
                'name_ar' => 'دورة المحاسب الإداري المعتمد (CMA) - الجزء الأول',
                'category_id' => 33, // Management, Accounting & Human Resources
            ],
            [
                'id' => 113,
                'name' => 'دورة المحاسب الإداري المعتمد (CMA) - الجزء الثاني',
                'name_en' => 'Certified Management Accountant (CMA) - Part 2',
                'name_ar' => 'دورة المحاسب الإداري المعتمد (CMA) - الجزء الثاني',
                'category_id' => 33, // Management, Accounting & Human Resources
            ],
            [
                'id' => 114,
                'name' => 'دورة إدارة المخاطر - PMI',
                'name_en' => 'Risk Management - PMI',
                'name_ar' => 'دورة إدارة المخاطر - PMI',
                'category_id' => 33, // Management, Accounting & Human Resources
            ],
            [
                'id' => 115,
                'name' => 'دورة الصحة والسلامة المهنية - OSHA - ISO 45001',
                'name_en' => 'Occupational Health and Safety - OSHA - ISO 45001',
                'name_ar' => 'دورة الصحة والسلامة المهنية - OSHA - ISO 45001',
                'category_id' => 33, // Management, Accounting & Human Resources
            ],
            [
                'id' => 116,
                'name' => 'دبلوم اللغة الإنجليزية - English Language Diploma',
                'name_en' => 'English Language Diploma',
                'name_ar' => 'دبلوم اللغة الإنجليزية - English Language Diploma',
                'category_id' => 58, // Languages
            ],
            [
                'id' => 117,
                'name' => 'دورة البرمجة لغير المبرمجين باستخدام بايثون',
                'name_en' => 'Programming for Non-Programmers using Python',
                'name_ar' => 'دورة البرمجة لغير المبرمجين باستخدام بايثون',
                'category_id' => 56, // Information Technology
            ],
            [
                'id' => 118,
                'name' => 'الخبير المعتمد في الضرائب السعودية - CSTP',
                'name_en' => 'Certified Saudi Tax Professional - CSTP',
                'name_ar' => 'الخبير المعتمد في الضرائب السعودية - CSTP',
                'category_id' => 33, // Management, Accounting & Human Resources
            ],
            [
                'id' => 119,
                'name' => 'شهادة التميز في الامتثال الضريبي السعودي - ETSA',
                'name_en' => 'Excellence in Saudi Tax Compliance - ETSA',
                'name_ar' => 'شهادة التميز في الامتثال الضريبي السعودي - ETSA',
                'category_id' => 33, // Management, Accounting & Human Resources
            ],
        ];

        foreach ($courses as $courseData) {
            Course::updateOrCreate(
                ['id' => $courseData['id']],
                [
                    'name' => $courseData['name'],
                    'name_en' => $courseData['name_en'],
                    'name_ar' => $courseData['name_ar'],
                    'category_id' => $courseData['category_id'],
                    'is_active' => true,
                    'code' => 'COURSE_' . str_pad($courseData['id'], 3, '0', STR_PAD_LEFT),
                ]
            );
        }

        $this->command->info('Updated ' . count($courses) . ' courses successfully.');
    }
}