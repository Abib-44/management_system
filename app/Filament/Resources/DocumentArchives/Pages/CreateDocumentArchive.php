<?php

namespace App\Filament\Resources\DocumentArchives\Pages;

use App\Filament\Resources\DocumentArchives\DocumentArchiveResource;
use App\Models\Attachment;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CreateDocumentArchive extends CreateRecord
{
    protected static string $resource = DocumentArchiveResource::class;

    /** Elementi del repeater: [['description' => ..., 'file' => ..., 'original_name' => ...], ...] */
    protected array $uploadedFiles = [];

    /** Elementi del repeater "Collegamenti" */
    protected array $documentLinks = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Questi due campi non sono colonne di document_archives: li gestiamo dopo la creazione.
        $this->uploadedFiles = $data['uploaded_files'] ?? [];
        $this->documentLinks = $data['document_links'] ?? [];

        unset($data['uploaded_files'], $data['document_links']);

        // created_by è NOT NULL (deve essere in $fillable di DocumentArchive)
        $data['created_by'] = auth()->id();

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->storeAttachments();
        $this->storeLinks();
    }

    protected function storeAttachments(): void
    {
        $disk = Storage::disk('s3');
        $version = 1;

        foreach ($this->uploadedFiles as $item) {
            $path = $item['file'] ?? null;

            // A seconda della versione, FileUpload può restituire [uuid => path]
            if (is_array($path)) {
                $path = collect($path)->first();
            }

            if (! is_string($path) || $path === '') {
                continue;
            }

            abort_unless(
                $disk->exists($path),
                500,
                "Upload non riuscito: file {$path} non trovato su storage."
            );

            Attachment::create([
                'attachable_type' => $this->record->getMorphClass(),
                'attachable_id' => $this->record->getKey(),
                'file_path' => $path,
                'file_name' => $item['original_name'] ?? basename($path),
                'mime_type' => $disk->mimeType($path),
                'file_size' => $disk->size($path),
                'label' => $item['description'] ?? null,
                'version' => $version++,
                'uploaded_by' => auth()->id(),
            ]);
        }
    }

    protected function storeLinks(): void
    {
        $now = now();

        $rows = collect($this->documentLinks)
            ->map(function (array $link) use ($now) {
                $type = $link['entity_type'] ?? null;

                $column = match ($type) {
                    'member' => 'member_id',
                    'student' => 'student_id',
                    'activity' => 'activity_id',
                    'transaction' => 'transaction_id',
                    default => null,
                };

                $entityId = $column ? ($link[$column] ?? null) : null;

                if (! $column || ! $entityId) {
                    return null;
                }

                // Il CHECK chk_one_entity_only richiede esattamente un *_id valorizzato
                return [
                    'document_archive_id' => $this->record->getKey(),
                    'entity_type' => $type,
                    'member_id' => null,
                    'student_id' => null,
                    'activity_id' => null,
                    'transaction_id' => null,
                    $column => $entityId,
                    'created_by' => auth()->id(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            })
            ->filter()
            // Evita violazioni dei vincoli UNIQUE (archivio + entità) se lo stesso collegamento è duplicato
            ->unique(fn (array $row) => $row['entity_type'].'-'.($row['member_id'] ?? $row['student_id'] ?? $row['activity_id'] ?? $row['transaction_id']))
            ->values()
            ->all();

        if ($rows !== []) {
            DB::table('document_archive_links')->insert($rows);
        }
    }
}
