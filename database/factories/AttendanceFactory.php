<?php

namespace Database\Factories;

use App\Models\ClassRoom;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceFactory extends Factory
{
    public function definition(): array
    {
        $status = fake()->randomElement([
            'present',
            'absent',
            'late',
        ]);

        $notes = match ($status) {
            'present' => fake()->randomElement([
                'Presenza regolare',
                'Presenza registrata',
                'Presenza confermata',
                'Tutto regolare',
                'Nessuna osservazione',
                null,
            ]),

            'absent' => fake()->randomElement([
                'Assenza non giustificata',
                'Assenza registrata',
                'Assenza segnalata',
                'Nessuna osservazione',
                null,
            ]),

            'late' => fake()->randomElement([
                'Ingresso in ritardo',
                'Ritardo registrato',
                'Ingresso posticipato',
                'Nessuna osservazione',
                null,
            ]),
        };

        return [
            'student_id' => Student::factory(),
            'class_room_id' => ClassRoom::factory(),
            'attendance_date' => fake()->dateTimeBetween('-90 days', 'today'),
            'status' => $status,
            'notes' => $notes,
        ];
    }
}
