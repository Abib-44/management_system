<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $materie = [
            [
                'name' => 'Matematica',
                'description' => 'Matematica e ragionamento logico.',
            ],
            [
                'name' => 'Italiano',
                'description' => 'Lingua e letteratura italiana.',
            ],
            [
                'name' => 'Inglese',
                'description' => 'Lingua inglese.',
            ],
            [
                'name' => 'Francese',
                'description' => 'Lingua francese.',
            ],
            [
                'name' => 'Scienze',
                'description' => 'Scienze naturali e scienze generali.',
            ],
            [
                'name' => 'Storia',
                'description' => 'Storia e studi storici.',
            ],
            [
                'name' => 'Geografia',
                'description' => 'Geografia e studi geografici.',
            ],
            [
                'name' => 'Informatica',
                'description' => 'Informatica e tecnologie digitali.',
            ],
        ];

        foreach ($materie as $materia) {
            Subject::create([
                ...$materia,
                'active' => true,
            ]);
        }
    }
}
