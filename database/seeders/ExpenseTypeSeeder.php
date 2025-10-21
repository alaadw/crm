<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExpenseType;

class ExpenseTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'إيجار', 'name_en' => 'Rent'],
            ['name' => 'فواتير كهرباء', 'name_en' => 'Electricity Bills'],
            ['name' => 'فواتير ماء', 'name_en' => 'Water Bills'],
            ['name' => 'إنترنت', 'name_en' => 'Internet'],
            ['name' => 'قرطاسية', 'name_en' => 'Stationery'],
            ['name' => 'صيانة', 'name_en' => 'Maintenance'],
            ['name' => 'ضيافة', 'name_en' => 'Hospitality'],
            ['name' => 'نقل', 'name_en' => 'Transportation'],
            ['name' => 'رواتب يومية', 'name_en' => 'Daily Wages'],
            ['name' => 'أخرى', 'name_en' => 'Other'],
        ];

        foreach ($types as $t) {
            ExpenseType::updateOrCreate(
                ['name' => $t['name']],
                ['name_en' => $t['name_en'], 'is_active' => true]
            );
        }
    }
}
