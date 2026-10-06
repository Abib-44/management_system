<?php

namespace Database\Factories;

use App\Models\ClassRoom;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        $firstNames = [
            'Luca',
            'Matteo',
            'Andrea',
            'Marco',
            'Davide',
            'Alessandro',
            'Francesco',
            'Simone',
            'Giulia',
            'Sofia',
            'Emma',
            'Anna',
            'Chiara',
            'Martina',
            'Alice',
        ];

        $lastNames = [
            'Rossi',
            'Bianchi',
            'Ferrari',
            'Romano',
            'Conti',
            'Ricci',
            'Marino',
            'Greco',
            'Bruno',
            'Gallo',
            'Costa',
            'Fontana',
            'Moretti',
            'Rinaldi',
            'Esposito',
        ];

        $motherNames = [
            'Anna',
            'Laura',
            'Sara',
            'Elena',
            'Chiara',
            'Martina',
            'Francesca',
            'Valentina',
        ];

        $addresses = [
            'Via Roma 10',
            'Via Garibaldi 25',
            'Via Dante 18',
            'Via Verdi 42',
            'Via Milano 15',
            'Via Torino 8',
            'Via Mazzini 31',
        ];

        $notes = [
            'Nessuna nota particolare',
            'Iscritto regolarmente',
            'Frequenza regolare',
            'Nessuna segnalazione',
            'Tutto regolare',
            'Partecipazione regolare alle attività',
            'Da monitorare la frequenza',
            null,
        ];

        $fees = [
            500.00,
            750.00,
            1000.00,
            1200.00,
            1500.00,
            1800.00,
            2000.00,
        ];

        $installmentPlans = [
            'no_interest',
            'installment_1',
            'installment_2',
        ];

        $firstName = $firstNames[array_rand($firstNames)];
        $lastName = $lastNames[array_rand($lastNames)];

        return [
            'class_room_id' => ClassRoom::query()->inRandomOrder()->value('id')
                ?? ClassRoom::factory(),

            'first_name' => $firstName,

            'last_name' => $lastName,

            'birth_date' => now()
                ->subYears(random_int(5, 18))
                ->subDays(random_int(0, 364)),

            'father_name' => $firstNames[array_rand($firstNames)]
                .' '
                .$lastNames[array_rand($lastNames)],

            'mother_name' => $motherNames[array_rand($motherNames)]
                .' '
                .$lastNames[array_rand($lastNames)],

            'mother_phone' => '333 '.random_int(1000000, 9999999),

            'father_phone' => '333 '.random_int(1000000, 9999999),

            'address' => $addresses[array_rand($addresses)],

            'email' => strtolower(
                $firstName.'.'.$lastName.random_int(1, 999)
                .'@example.com'
            ),

            'total_fee' => $fees[array_rand($fees)],

            'installment_plan' => $installmentPlans[
                array_rand($installmentPlans)
            ],

            'notes' => $notes[array_rand($notes)],
        ];
    }
}
