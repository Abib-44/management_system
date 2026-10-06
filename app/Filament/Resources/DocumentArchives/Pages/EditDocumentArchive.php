<?php

namespace App\Filament\Resources\DocumentArchives\Pages;

use App\Filament\Concerns\RedirectsToIndexAfterEdit;
use App\Filament\Resources\DocumentArchives\DocumentArchiveResource;
use App\Models\Attachment;
use App\Models\DocumentArchiveLink;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditDocumentArchive extends EditRecord
{
    use RedirectsToIndexAfterEdit;

    protected static string $resource = DocumentArchiveResource::class;

    protected array $uploadedFiles = [];

    protected array $documentLinks = [];

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->uploadedFiles = $data['uploaded_files'] ?? [];
        $this->documentLinks = $data['document_links'] ?? [];

        unset($data['uploaded_files'], $data['document_links']);

        $data['updated_by'] = auth()->id();

        return $data;
    }

    protected function afterSave(): void
    {
        $this->syncDocumentLinks();
        $this->persistUploadedFiles();
    }

    protected function syncDocumentLinks(): void
    {
        $this->record->documentLinks()->delete();

        foreach ($this->documentLinks as $link) {
            DocumentArchiveLink::create([
                'document_archive_id' => $this->record->getKey(),
                'member_id' => $link['member_id'] ?? null,
                'activity_id' => $link['activity_id'] ?? null,
                'transaction_id' => $link['transaction_id'] ?? null,
                'student_id' => $link['student_id'] ?? null,
                'created_by' => auth()->id(),
            ]);
        }
    }

    protected function persistUploadedFiles(): void
    {
        if (empty($this->uploadedFiles)) {
            return;
        }

        $disk = Storage::disk('s3');

        $nextVersion = ($this->record->attachments()->max('version') ?? 0) + 1;

        foreach ($this->uploadedFiles as $path) {
            abort_unless($disk->exists($path), 500, "Upload non riuscito: file {$path} non trovato su storage.");

            Attachment::create([
                'attachable_type' => $this->record->getMorphClass(),
                'attachable_id' => $this->record->getKey(),
                'file_path' => $path,
                'file_name' => basename($path),
                'mime_type' => $disk->mimeType($path),
                'file_size' => $disk->size($path),
                'label' => $this->record->title,
                'version' => $nextVersion,
                'uploaded_by' => auth()->id(),
            ]);

            $nextVersion++;
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
