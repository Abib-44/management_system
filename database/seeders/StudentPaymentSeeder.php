<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\StudentPayment;
use Illuminate\Database\Seeder;

class StudentPaymentSeeder extends Seeder
{
    public function run(): void
    {
        Student::all()->each(function (Student $student) {
            for ($i = 1; $i <= 3; $i++) {
                StudentPayment::factory()->create([
                    'student_id' => $student->id,
                    'installment_number' => $i,
                ]);
            }
        });
    }
}
