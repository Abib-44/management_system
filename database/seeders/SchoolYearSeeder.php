<?php

namespace Database\Seeders;

use App\Models\SchoolYear;
use Illuminate\Database\Seeder;

class SchoolYearSeeder extends Seeder
{
    public function run(): void
    {
        $years = [
            ['name' => '2022/2023', 'start' => '2022-09-01', 'end' => '2023-06-30', 'active' => false],
            ['name' => '2023/2024', 'start' => '2023-09-01', 'end' => '2024-06-30', 'active' => false],
            ['name' => '2024/2025', 'start' => '2024-09-01', 'end' => '2025-06-30', 'active' => false],
            ['name' => '2025/2026', 'start' => '2025-09-01', 'end' => '2026-06-30', 'active' => true],
        ];

        foreach ($years as $year) {
            SchoolYear::factory()->create([
                'name' => $year['name'],
                'start_date' => $year['start'],
                'end_date' => $year['end'],
                'is_active' => $year['active'],
            ]);
        }
    }
}
