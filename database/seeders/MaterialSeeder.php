<?php

namespace Database\Seeders;

use App\Models\Material;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    public function run(): void
    {
        $materiali = [
            [
                'subject' => 'Matematica',
                'name' => 'Libro di matematica',
                'description' => 'Libro di testo principale per matematica.',
                'type' => 'libro',
                'quantity' => 25,
                'available_quantity' => 22,
                'location' => 'Aula',
            ],
            [
                'subject' => 'Matematica',
                'name' => 'Kit di geometria',
                'description' => 'Strumenti per le esercitazioni di geometria.',
                'type' => 'kit',
                'quantity' => 15,
                'available_quantity' => 13,
                'location' => 'Magazzino',
            ],
            [
                'subject' => 'Italiano',
                'name' => 'Libro di grammatica italiana',
                'description' => 'Manuale per lo studio della grammatica italiana.',
                'type' => 'libro',
                'quantity' => 25,
                'available_quantity' => 23,
                'location' => 'Aula',
            ],
            [
                'subject' => 'Inglese',
                'name' => 'Workbook di inglese',
                'description' => 'Libro di esercizi per la lingua inglese.',
                'type' => 'libro',
                'quantity' => 25,
                'available_quantity' => 24,
                'location' => 'Aula',
            ],
            [
                'subject' => 'Francese',
                'name' => 'Libro di francese',
                'description' => 'Libro di testo per la lingua francese.',
                'type' => 'libro',
                'quantity' => 20,
                'available_quantity' => 18,
                'location' => 'Aula',
            ],
            [
                'subject' => 'Scienze',
                'name' => 'Kit di laboratorio',
                'description' => 'Materiale per le attività pratiche di laboratorio.',
                'type' => 'kit',
                'quantity' => 10,
                'available_quantity' => 8,
                'location' => 'Magazzino',
            ],
            [
                'subject' => 'Storia',
                'name' => 'Libro di storia',
                'description' => 'Libro di riferimento per lo studio della storia.',
                'type' => 'libro',
                'quantity' => 20,
                'available_quantity' => 18,
                'location' => 'Biblioteca',
            ],
            [
                'subject' => 'Geografia',
                'name' => 'Carte geografiche',
                'description' => 'Carte geografiche per le lezioni.',
                'type' => 'attrezzatura',
                'quantity' => 8,
                'available_quantity' => 8,
                'location' => 'Aula',
            ],
            [
                'subject' => 'Informatica',
                'name' => 'Manuale di programmazione',
                'description' => 'Materiale didattico per la programmazione.',
                'type' => 'libro',
                'quantity' => 20,
                'available_quantity' => 17,
                'location' => 'Aula',
            ],
        ];

        foreach ($materiali as $materiale) {
            $materia = Subject::where(
                'name',
                $materiale['subject']
            )->first();

            if (! $materia) {
                continue;
            }

            Material::create([
                'subject_id' => $materia->id,
                'name' => $materiale['name'],
                'description' => $materiale['description'],
                'type' => $materiale['type'],
                'quantity' => $materiale['quantity'],
                'available_quantity' => $materiale['available_quantity'],
                'location' => $materiale['location'],
                'active' => true,
            ]);
        }
    }
}
