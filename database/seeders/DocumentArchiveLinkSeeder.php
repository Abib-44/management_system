<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Attachment;
use App\Models\DocumentArchive;
use App\Models\DocumentArchiveLink;
use App\Models\DocumentCategory;
use App\Models\FinancialTransaction;
use App\Models\Member;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DocumentArchiveLinkSeeder extends Seeder
{
    private const FILES = [
        'documents/carte_d_identita.webp',
        'documents/codice-fiscale.webp',
        'documents/passport.webp',
    ];

    public function run(): void
    {
        $userId = User::query()->value('id');

        if (! $userId) {
            return;
        }

        $archives = DocumentArchive::query()->get();

        /*
         * ACTIVITY
         */
        if (
            $archives->isNotEmpty()
            && ($activity = Activity::query()->first())
        ) {
            DocumentArchiveLink::create([
                'document_archive_id' => $archives->get(0)->id,
                'entity_type' => 'activity',
                'member_id' => null,
                'activity_id' => $activity->id,
                'transaction_id' => null,
                'student_id' => null,
                'created_by' => $userId,
            ]);
        }

        /*
         * TRANSACTION
         */
        if (
            $archives->count() > 1
            && ($transaction = FinancialTransaction::query()->first())
        ) {
            DocumentArchiveLink::create([
                'document_archive_id' => $archives->get(1)->id,
                'entity_type' => 'transaction',
                'member_id' => null,
                'activity_id' => null,
                'transaction_id' => $transaction->id,
                'student_id' => null,
                'created_by' => $userId,
            ]);
        }

        /*
         * Categoria documenti personali.
         */
        $category = DocumentCategory::query()
            ->where('name', 'Documenti personali')
            ->first()
            ?? DocumentCategory::forceCreate([
                'name' => 'Documenti personali',
                'slug' => 'documenti-personali',
                'active' => true,
            ]);

        /*
         * MEMBER
         */
        Member::query()->chunkById(200, function ($members) use (
            $category,
            $userId
        ) {
            foreach ($members as $member) {
                $this->createPersonalArchive(
                    $member,
                    'member',
                    'member_id',
                    $category->id,
                    $userId
                );
            }
        });

        /*
         * STUDENT
         */
        Student::query()->chunkById(200, function ($students) use (
            $category,
            $userId
        ) {
            foreach ($students as $student) {
                $this->createPersonalArchive(
                    $student,
                    'student',
                    'student_id',
                    $category->id,
                    $userId
                );
            }
        });
    }

    private function createPersonalArchive(
        $person,
        string $entityType,
        string $column,
        int $categoryId,
        int $userId
    ): void {
        $archive = DocumentArchive::factory()->create([
            'title' => "Documenti personali - {$person->last_name} {$person->first_name}",
            'document_category_id' => $categoryId,
            'status' => 'active',
            'created_by' => $userId,
            'updated_by' => $userId,
        ]);

        /*
         * Collegamento archivio -> Member/Student.
         */
        DocumentArchiveLink::create([
            'document_archive_id' => $archive->id,
            'entity_type' => $entityType,
            'member_id' => $entityType === 'member'
                ? $person->id
                : null,
            'activity_id' => null,
            'transaction_id' => null,
            'student_id' => $entityType === 'student'
                ? $person->id
                : null,
            'created_by' => $userId,
        ]);

        /*
         * Attachment.
         *
         * I file sono già stati preparati dal comando
         * app:ensure-storage-bucket.
         */
        foreach (self::FILES as $i => $path) {
            Attachment::factory()
                ->existingFile(
                    $path,
                    $this->getFileSize($path),
                    'image/webp',
                    'Documento '.($i + 1)
                )
                ->create([
                    'attachable_type' => DocumentArchive::class,
                    'attachable_id' => $archive->id,
                    'version' => $i + 1,
                    'uploaded_by' => $userId,
                ]);
        }
    }

    private function getFileSize(string $path): int
    {
        return Storage::disk('s3')
            ->size($path);
    }
}
