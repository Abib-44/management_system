<?php

namespace Database\Factories;

use App\Models\DocumentArchive;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentArchiveLinkFactory extends Factory
{
    public function definition(): array
    {
        return [
            'document_archive_id' => DocumentArchive::factory(),

            'entity_type' => 'member',

            'member_id' => null,

            'activity_id' => null,

            'transaction_id' => null,

            'student_id' => null,

            'created_by' => User::inRandomOrder()->value('id') ?? User::factory(),
        ];
    }
}
