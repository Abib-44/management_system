<?php

namespace App\Filament\Resources\Members\Pages;

use App\Filament\Concerns\RedirectsToIndexAfterEdit;
use App\Filament\Resources\Members\MemberResource;
use App\Models\Attachment;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditMember extends EditRecord
{
    use RedirectsToIndexAfterEdit;

    protected static string $resource = MemberResource::class;

    protected ?string $newRichiestaAdesione = null;

    protected ?string $newVerbaleApprovazione = null;

    protected ?string $currentRichiestaAdesione = null;

    protected ?string $currentVerbaleApprovazione = null;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    /**
     * Precarica nei campi FileUpload l'ultimo Attachment salvato per ciascuna
     * etichetta, così in modifica si vede il documento già presente.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $this->currentRichiestaAdesione = $this->latestAttachmentPath('Richiesta di adesione');
        $this->currentVerbaleApprovazione = $this->latestAttachmentPath('Verbale di approvazione');

        $data['richiesta_adesione'] = $this->currentRichiestaAdesione;
        $data['verbale_approvazione'] = $this->currentVerbaleApprovazione;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->newRichiestaAdesione = $this->normalizeFilePath($data['richiesta_adesione'] ?? null);
        $this->newVerbaleApprovazione = $this->normalizeFilePath($data['verbale_approvazione'] ?? null);

        unset($data['richiesta_adesione'], $data['verbale_approvazione']);

        $data['updated_by'] = auth()->id();

        return $data;
    }

    protected function afterSave(): void
    {
        $this->persistUpdatedMemberDocuments();
    }

    protected function normalizeFilePath($value): ?string
    {
        if (is_array($value)) {
            return array_values($value)[0] ?? null;
        }

        return $value;
    }

    /**
     * Recupera il path dell'ultimo Attachment (versione più alta) con la
     * label indicata, collegato a questo Member.
     */
    protected function latestAttachmentPath(string $label): ?string
    {
        return Attachment::query()
            ->where('attachable_type', $this->record->getMorphClass())
            ->where('attachable_id', $this->record->getKey())
            ->where('label', $label)
            ->orderByDesc('version')
            ->value('file_path');
    }

    /**
     * Se l'utente ha caricato un nuovo file al posto di uno esistente,
     * crea un nuovo Attachment con versione incrementata mantenendo lo
     * storico delle versioni precedenti. Se il campo è stato svuotato,
     * non viene toccato nulla (nessuna cancellazione automatica).
     */
    protected function persistUpdatedMemberDocuments(): void
    {
        $documents = [
            [
                'new_path' => $this->newRichiestaAdesione,
                'current_path' => $this->currentRichiestaAdesione,
                'label' => 'Richiesta di adesione',
            ],
            [
                'new_path' => $this->newVerbaleApprovazione,
                'current_path' => $this->currentVerbaleApprovazione,
                'label' => 'Verbale di approvazione',
            ],
        ];

        $disk = Storage::disk('s3');

        foreach ($documents as $document) {
            $newPath = $document['new_path'];
            $currentPath = $document['current_path'];
            $label = $document['label'];

            // Nessun file caricato, oppure file invariato: non fare nulla.
            if (empty($newPath) || $newPath === $currentPath) {
                continue;
            }

            abort_unless(
                $disk->exists($newPath),
                500,
                "Upload non riuscito: file {$newPath} non trovato su storage."
            );

            $nextVersion = Attachment::query()
                ->where('attachable_type', $this->record->getMorphClass())
                ->where('attachable_id', $this->record->getKey())
                ->where('label', $label)
                ->max('version');

            $nextVersion = $nextVersion ? $nextVersion + 1 : 1;

            Attachment::create([
                'attachable_type' => $this->record->getMorphClass(),
                'attachable_id' => $this->record->getKey(),
                'file_path' => $newPath,
                'file_name' => basename($newPath),
                'mime_type' => $disk->mimeType($newPath),
                'file_size' => $disk->size($newPath),
                'label' => $label,
                'version' => $nextVersion,
                'uploaded_by' => auth()->id(),
            ]);
        }
    }
}
