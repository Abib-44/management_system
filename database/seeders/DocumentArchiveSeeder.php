<?php

namespace Database\Seeders;

use App\Models\Attachment;
use App\Models\DocumentArchive;
use Illuminate\Database\Seeder;

class DocumentArchiveSeeder extends Seeder
{
    public function run(): void
    {
        $archives = DocumentArchive::factory()
            ->count(30)
            ->create();

        foreach ($archives as $archive) {
            Attachment::factory()
                ->count(fake()->numberBetween(1, 2))
                ->create([
                    'attachable_type' => DocumentArchive::class,
                    'attachable_id' => $archive->id,
                    'uploaded_by' => $archive->created_by,
                ]);
        }
    }
}
