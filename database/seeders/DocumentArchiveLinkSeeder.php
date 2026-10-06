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
use RuntimeException;

class DocumentArchiveLinkSeeder extends Seeder
{
    /**
     * File fixture presenti nel progetto.
     *
     * Verranno caricati su MinIO nella cartella "documents"
     * solo se non esistono già.
     */
    private const FILES = [
        'carte_d_identita.webp',
        'codice-fiscale.webp',
        'passport.webp',
    ];

    /**
     * Cartella locale delle fixture.
     */
    private const LOCAL_PATH = 'images/minio';

    /**
     * Cartella remota su MinIO.
     */
    private const MINIO_PATH = 'documents';

    public function run(): void
    {
        $userId = User::query()->value('id');

        if (! $userId) {
            return;
        }

        /*
         * Prima garantiamo che i file esistano su MinIO.
         *
         * Se esistono:
         *     non viene fatto nulla.
         *
         * Se non esistono:
         *     vengono caricati dalle fixture locali.
         */
        $files = $this->prepareFilesOnMinio();

        $archives = DocumentArchive::query()->get();

        /*
         * ACTIVITY
         */
        if ($archives->isNotEmpty() && ($activity = Activity::query()->first())) {
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
        if ($archives->count() > 1 && ($transaction = FinancialTransaction::query()->first())) {
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
            $files,
            $category,
            $userId
        ) {
            foreach ($members as $member) {
                $this->createPersonalArchive(
                    $member,
                    'member',
                    'member_id',
                    $files,
                    $category->id,
                    $userId
                );
            }
        });

        /*
         * STUDENT
         */
        Student::query()->chunkById(200, function ($students) use (
            $files,
            $category,
            $userId
        ) {
            foreach ($students as $student) {
                $this->createPersonalArchive(
                    $student,
                    'student',
                    'student_id',
                    $files,
                    $category->id,
                    $userId
                );
            }
        });
    }

    /**
     * Garantisce che tutti i file fixture esistano su MinIO.
     *
     * Se il file esiste già su MinIO:
     *     viene semplicemente utilizzato.
     *
     * Se il file non esiste:
     *     viene caricato dalla cartella locale
     *     public/images/minio.
     */
    private function prepareFilesOnMinio(): array
    {
        $disk = Storage::disk('s3');

        return collect(self::FILES)
            ->map(function (string $filename) use ($disk) {
                $localPath = public_path(self::LOCAL_PATH.'/'.$filename);
                $minioPath = self::MINIO_PATH.'/'.$filename;

                /*
                 * Controllo fixture locale.
                 */
                if (! is_file($localPath)) {
                    throw new RuntimeException(
                        "Fixture locale non trovata: {$localPath}"
                    );
                }

                /*
                 * Controllo MinIO.
                 */
                if (! $disk->exists($minioPath)) {
                    $this->command?->info(
                        "Upload MinIO: {$minioPath}"
                    );

                    $disk->put(
                        $minioPath,
                        file_get_contents($localPath),
                        [
                            'ContentType' => 'image/webp',
                        ]
                    );
                } else {
                    $this->command?->line(
                        "File già presente su MinIO: {$minioPath}"
                    );
                }

                /*
                 * Verifica finale.
                 *
                 * Se per qualche motivo l'upload è fallito,
                 * il seeder si interrompe con un errore chiaro.
                 */
                if (! $disk->exists($minioPath)) {
                    throw new RuntimeException(
                        "File {$minioPath} non presente su MinIO dopo l'upload."
                    );
                }

                return [
                    'path' => $minioPath,
                    'size' => $disk->size($minioPath),
                ];
            })
            ->all();
    }

    /**
     * Crea l'archivio personale e i relativi attachment.
     */
    private function createPersonalArchive(
        $person,
        string $entityType,
        string $column,
        array $files,
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
            'member_id' => $entityType === 'member' ? $person->id : null,
            'activity_id' => null,
            'transaction_id' => null,
            'student_id' => $entityType === 'student' ? $person->id : null,
            'created_by' => $userId,
        ]);

        /*
         * Attachment.
         */
        foreach ($files as $i => $file) {
            Attachment::factory()
                ->existingFile(
                    $file['path'],
                    $file['size'],
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
}