<?php

namespace Database\Seeders;

use App\Models\ServiceAssignment;
use Illuminate\Database\Seeder;

class ServiceAssignmentSeeder extends Seeder
{
    public function run(): void
    {
        $assignments = [
            [
                'type' => 'key',
                'name' => 'Chiave ingresso',
                'assignee_name' => 'Ahmed Verdi',
                'document_number' => 'DEMO-CI-K01',
                'delivered_at' => '2026-08-20',
                'returned_at' => null,
                'status' => 'delivered',
            ],
            [
                'type' => 'service',
                'name' => 'Pulizia moschea',
                'assignee_name' => 'Servizio esterno',
                'document_number' => null,
                'delivered_at' => '2026-08-19',
                'returned_at' => null,
                'status' => 'active',
            ],
        ];

        foreach ($assignments as $assignment) {
            ServiceAssignment::create($assignment);
        }
    }
}
