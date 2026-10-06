<?php

namespace Database\Seeders;

use App\Models\Activity;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        $activities = [
            [
                'title' => 'Laboratorio estivo',
                'activity_date' => today()->subDays(30),
                'location' => 'Sede',
                'status' => 'in_progress',
                'responsible_name' => 'Giulia Ferrari',
                'notes' => 'Attività per bambini',
            ],
            [
                'title' => 'Calcio domenica',
                'activity_date' => today()->addDays(7),
                'location' => 'Campo sportivo',
                'status' => 'scheduled',
                'responsible_name' => 'Marco Bianchi',
                'notes' => 'Attività esterna',
            ],
            [
                'title' => 'Lezione adulti',
                'activity_date' => today()->addDays(14),
                'location' => 'Sala principale',
                'status' => 'scheduled',
                'responsible_name' => 'Marco Bianchi',
                'notes' => 'Lezione dedicata agli adulti',
            ],
        ];

        foreach ($activities as $activity) {
            Activity::create($activity);
        }
    }
}
