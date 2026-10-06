<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SubjectFactory extends Factory
{
    public function definition(): array
    {
        $subjects = [
            [
                'name' => 'Matematica',
                'description' => 'Lezioni e attività di matematica',
            ],
            [
                'name' => 'Italiano',
                'description' => 'Lezioni di lingua e letteratura italiana',
            ],
            [
                'name' => 'Inglese',
                'description' => 'Lezioni di lingua inglese',
            ],
            [
                'name' => 'Francese',
                'description' => 'Lezioni di lingua francese',
            ],
            [
                'name' => 'Scienze',
                'description' => 'Lezioni e attività scientifiche',
            ],
            [
                'name' => 'Storia',
                'description' => 'Lezioni di storia',
            ],
            [
                'name' => 'Geografia',
                'description' => 'Lezioni di geografia',
            ],
            [
                'name' => 'Informatica',
                'description' => 'Lezioni di informatica',
            ],
            [
                'name' => 'Fisica',
                'description' => 'Lezioni di fisica',
            ],
            [
                'name' => 'Chimica',
                'description' => 'Lezioni di chimica',
            ],
        ];

        $subject = $subjects[array_rand($subjects)];

        return [
            'name' => $subject['name'],
            'description' => $subject['description'],
            'active' => true,
        ];
    }
}
