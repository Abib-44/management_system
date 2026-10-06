<?php

namespace Database\Factories;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

class MaterialFactory extends Factory
{
    public function definition(): array
    {
        $quantita = fake()->numberBetween(1, 30);

        return [
            'subject_id' => Subject::factory(),

            'name' => fake()->randomElement([
                'Libro di testo',
                'Quaderno di esercizi',
                'Manuale dell’insegnante',
                'Kit didattico',
                'Kit di laboratorio',
                'Carte didattiche',
                'Atlante geografico',
                'Libro di riferimento',
                'Materiale didattico',
                'Strumenti per esercitazioni',
            ]),

            'description' => fake()->optional()->sentence(),

            'type' => fake()->randomElement([
                'libro',
                'kit',
                'attrezzatura',
                'cancelleria',
                'digitale',
                'altro',
            ]),

            'quantity' => $quantita,

            'available_quantity' => fake()->numberBetween(
                0,
                $quantita
            ),

            'location' => fake()->randomElement([
                'Aula',
                'Biblioteca',
                'Magazzino',
                'Sala insegnanti',
            ]),

            'active' => true,
        ];
    }
}
