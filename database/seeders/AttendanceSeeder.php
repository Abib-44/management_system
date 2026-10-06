<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Student;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $classRoom = ClassRoom::first() ?? ClassRoom::factory()->create();

        Student::all()->each(function (Student $student) use ($classRoom) {
            foreach (range(1, 5) as $i) {
                Attendance::factory()->create([
                    'student_id' => $student->id,
                    'class_room_id' => $classRoom->id,
                    'attendance_date' => now()->subDays($i),
                ]);
            }
        });
    }
}
