<?php

namespace Database\Factories;

use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Member>
 */
class MemberFactory extends Factory
{
    protected $model = Member::class;

    public function definition(): array
    {
        $firstNames = [
            'Marco',
            'Luca',
            'Andrea',
            'Matteo',
            'Davide',
            'Francesco',
            'Alessandro',
            'Simone',
            'Giulia',
            'Anna',
            'Laura',
            'Sara',
            'Elena',
            'Chiara',
            'Martina',
        ];

        $lastNames = [
            'Rossi',
            'Bianchi',
            'Ferrari',
            'Romano',
            'Conti',
            'Esposito',
            'Ricci',
            'Marino',
            'Greco',
            'Bruno',
            'Gallo',
            'Costa',
            'Fontana',
            'Moretti',
            'Rinaldi',
        ];

        $activityNotes = [
            'Nessuna nota particolare',
            'Iscritto regolarmente',
            'Partecipazione regolare alle attività',
            'Socio attivo',
            'Nessuna segnalazione',
            'Tutto regolare',
            'Partecipazione occasionale',
            null,
        ];

        $statuses = [
            'active',
            'active',
            'active',
            'inactive',
            'suspended',
        ];

        $assemblyStatuses = [
            'active',
            'active',
            'inactive',
        ];

        $annualFees = [
            20.00,
            30.00,
            40.00,
            50.00,
            60.00,
            75.00,
            100.00,
            120.00,
            150.00,
        ];

        $firstName = $firstNames[array_rand($firstNames)];
        $lastName = $lastNames[array_rand($lastNames)];

        return [
            'last_name' => $lastName,

            'first_name' => $firstName,

            'phone' => '3'.random_int(
                100000000,
                999999999
            ),

            'email' => strtolower(
                $firstName.'.'.$lastName.random_int(1, 999)
                .'@example.com'
            ),

            'registration_date' => now()->subDays(
                random_int(30, 1095)
            ),

            'renewal_date' => random_int(0, 10) > 1
                ? now()->addDays(random_int(1, 365))
                : null,

            'status' => $statuses[array_rand($statuses)],

            'assembly_status' => $assemblyStatuses[
                array_rand($assemblyStatuses)
            ],

            'activity_notes' => $activityNotes[
                array_rand($activityNotes)
            ],

            'annual_fee' => $annualFees[
                array_rand($annualFees)
            ],
        ];
    }
}
