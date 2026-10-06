<?php

namespace App\Filament\Resources\Members\Pages;

use App\Filament\Resources\Members\MemberResource;
use App\Models\Attachment;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateMember extends CreateRecord
{
    protected static string $resource = MemberResource::class;

    protected ?string $richiestaAdesione = null;

    protected ?string $verbaleApprovazione = null;

    /**
     * Modifica i dati del form prima della creazione del socio.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        /*
         * Recupera i percorsi dei documenti caricati.
         */
        $this->richiestaAdesione = $this->normalizeFilePath(
            $data['richiesta_adesione'] ?? null
        );

        $this->verbaleApprovazione = $this->normalizeFilePath(
            $data['verbale_approvazione'] ?? null
        );

        /*
         * I documenti non vengono salvati nella tabella members.
         * Verranno salvati successivamente nella tabella attachments.
         */
        unset(
            $data['richiesta_adesione'],
            $data['verbale_approvazione']
        );

        /*
         * =========================================================
         * DATA DI ISCRIZIONE
         * =========================================================
         *
         * La data viene stabilita automaticamente dal codice.
         *
         * Non viene quindi utilizzata nessuna data inserita
         * dall'utente nel form.
         */
        $data['data_iscrizione'] = now()->toDateString();

        /*
         * =========================================================
         * UTENTE CREAZIONE / MODIFICA
         * =========================================================
         */
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        return $data;
    }

    /**
     * Eseguito dopo la creazione del Member.
     */
    protected function afterCreate(): void
    {
        $this->persistMemberDocuments();
    }

    /**
     * Normalizza il valore restituito dal FileUpload.
     *
     * FileUpload può restituire:
     * - una stringa
     * - un array contenente il percorso
     */
    protected function normalizeFilePath($value): ?string
    {
        if (is_array($value)) {
            return array_values($value)[0] ?? null;
        }

        return $value;
    }

    /**
     * Salva i documenti caricati nella tabella attachments.
     */
    protected function persistMemberDocuments(): void
    {
        /*
         * Se non è stato caricato nessun documento,
         * non c'è nulla da salvare.
         */
        if (
            empty($this->richiestaAdesione) &&
            empty($this->verbaleApprovazione)
        ) {
            return;
        }

        $disk = Storage::disk('s3');

        /*
         * Documenti da associare al socio.
         */
        $documents = [
            [
                'path' => $this->richiestaAdesione,
                'label' => 'Richiesta di adesione',
            ],
            [
                'path' => $this->verbaleApprovazione,
                'label' => 'Verbale di approvazione',
            ],
        ];

        /*
         * Partiamo dalla versione 1.
         */
        $version = 1;

        foreach ($documents as $document) {
            /*
             * Documento non caricato.
             */
            if (empty($document['path'])) {
                continue;
            }

            $path = $document['path'];

            /*
             * Verifica che il file esista realmente
             * su MinIO / S3.
             */
            abort_unless(
                $disk->exists($path),
                500,
                "Upload non riuscito: file {$path} non trovato su storage."
            );

            /*
             * Salva il collegamento del documento
             * direttamente al Member.
             */
            Attachment::create([
                'attachable_type' => $this->record->getMorphClass(),
                'attachable_id' => $this->record->getKey(),

                'file_path' => $path,
                'file_name' => basename($path),

                'mime_type' => $disk->mimeType($path),
                'file_size' => $disk->size($path),

                'label' => $document['label'],

                'version' => $version,

                'uploaded_by' => auth()->id(),
            ]);

            $version++;
        }
    }
}
