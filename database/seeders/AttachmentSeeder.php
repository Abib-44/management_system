<?php

namespace Database\Seeders;

use App\Models\Attachment;
use App\Models\DocumentArchive;
use App\Models\User;
use Illuminate\Database\Seeder;

class AttachmentSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            return;
        }

        DocumentArchive::query()
            ->get()
            ->each(function (DocumentArchive $document) use ($users) {
                Attachment::factory()
                    ->count(random_int(0, 3))
                    ->state([
                        'document_archive_id' => $document->id,
                        'uploaded_by' => $users->random()->id,
                    ])
                    ->create();
            });
    }
}
