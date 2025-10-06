<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateCourseCategoriesSeeder extends Seeder
{
    /**
     * Run the database migrations.
     */
    public function run(): void
    {
        // Course to category mappings based on the provided data
        $courseCategoryMappings = [
            1 => [2, 56],
            2 => [2, 56],
            3 => [2, 56],
            4 => [2, 56],
            5 => [33],
            6 => [2, 56],
            7 => [46, 57],
            13 => [46, 57],
            21 => [2, 56],
            25 => [2, 56],
            26 => [2, 56],
            27 => [3, 56],
            28 => [2, 56],
            29 => [2, 56],
            30 => [3, 56],
            31 => [3, 56],
            32 => [2, 56],
            33 => [2, 56],
            34 => [46, 57],
            35 => [2, 56],
            36 => [2, 56],
            37 => [2, 56],
            38 => [29, 48],
            39 => [29, 48],
            40 => [29, 48],
            41 => [29, 48],
            42 => [29, 48],
            43 => [29, 48],
            44 => [29, 48],
            45 => [29, 48],
            46 => [29, 50],
            47 => [29, 50],
            48 => [29, 50],
            49 => [29, 51],
            50 => [29, 51],
            51 => [47, 53],
            52 => [33],
            53 => [45, 57],
            55 => [45, 57],
            56 => [45, 57],
            59 => [33],
            60 => [33],
            61 => [33],
            62 => [33, 29, 48, 50, 51, 55],
            63 => [33, 55],
            64 => [33],
            65 => [33],
            66 => [33],
            68 => [3, 56],
            69 => [47, 53],
            70 => [47, 52],
            71 => [47, 49],
            72 => [47, 49],
            73 => [47, 49],
            74 => [47, 49],
            75 => [47, 49],
            76 => [47, 54],
            77 => [47, 52],
            78 => [3, 56],
            79 => [29, 50],
            80 => [29, 51],
            81 => [29, 51],
            83 => [29, 48, 50],
            84 => [33, 51],
            85 => [29, 51],
            86 => [2, 56],
            87 => [29, 55],
            88 => [29, 51],
            89 => [2, 56],
            90 => [29, 50],
            91 => [2, 56],
            92 => [33],
            93 => [29, 51],
            94 => [52],
            95 => [52],
            97 => [33],
            98 => [29, 51],
            99 => [29, 50],
            100 => [33],
            101 => [29, 48],
            102 => [3, 56],
            103 => [29, 51],
            104 => [29, 51],
            105 => [51, 29],
            106 => [33],
            107 => [29, 51],
            108 => [33],
            109 => [45, 57],
            110 => [45, 57],
            111 => [29, 51],
            112 => [33],
            113 => [33],
            114 => [33],
            115 => [51],
            116 => [47, 58],
            117 => [33],
            118 => [33],
            119 => [33],
        ];

        // Update each course with its primary category (first one in the list)
        foreach ($courseCategoryMappings as $courseId => $categoryIds) {
            // Use the first category as the primary category
            $primaryCategoryId = $categoryIds[0];
            
            Course::where('id', $courseId)->update([
                'category_id' => $primaryCategoryId
            ]);
        }

        $this->command->info('Updated course categories successfully for ' . count($courseCategoryMappings) . ' courses.');
        
        // Display some statistics
        $categoryStats = Course::select('category_id', DB::raw('count(*) as course_count'))
            ->groupBy('category_id')
            ->orderBy('course_count', 'desc')
            ->get();
            
        $this->command->info('Course distribution by category:');
        foreach ($categoryStats as $stat) {
            $categoryName = \App\Models\Category::find($stat->category_id)->name ?? 'Unknown';
            $this->command->info("Category {$stat->category_id} ({$categoryName}): {$stat->course_count} courses");
        }
    }
}