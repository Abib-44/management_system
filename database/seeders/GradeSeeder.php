<?php

namespace Database\Seeders;

use App\Models\Grade;
use App\Models\Student;
use Illuminate\Database\Seeder;

class GradeSeeder extends Seeder
{
    public function run(): void
    {
        Student::query()->each(function (Student $student) {
            Grade::factory()
                ->count(3)
                ->forStudent($student)
                ->create();
        });
    }
}
