<?php

namespace Database\Factories;

use App\Models\ClassRoom;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

class LessonFactory extends Factory
{
    public function definition(): array
    {
        $teachers = [
            'Anna Bianchi',
            'Marco Rossi',
            'Laura Ferrari',
            'Paolo Romano',
            'Giulia Conti',
        ];

        $topics = [
            'Introduzione all’argomento',
            'Ripasso della lezione precedente',
            'Approfondimento del programma',
            'Esercitazione in classe',
            'Attività pratica',
            'Lezione ordinaria',
            'Approfondimento generale',
            'Ripasso e attività',
            null,
        ];

        return [
            'class_room_id' => ClassRoom::query()->inRandomOrder()->value('id'),
            'subject_id' => Subject::query()->inRandomOrder()->value('id')
                ?? Subject::factory(),
            'lesson_date' => now()->subDays(random_int(0, 180)),
            'teacher' => $teachers[array_rand($teachers)],
            'topics' => $topics[array_rand($topics)],
        ];
    }
}
