<?php

namespace App\Filament\Pages;

use App\Models\Attachment;
use App\Models\DocumentArchive;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;

class DocumentsDashboard extends Page
{
    use HasPageShield;

    protected static ?string $navigationLabel = 'Documenti';

    protected static ?string $title = 'Dashboard documenti';

    public function getView(): string
    {
        return 'filament.pages.documents-dashboard';
    }

    public function getTotalFiles(): int
    {
        return Attachment::count();
    }

    public function getTotalSize(): string
    {
        return $this->formatBytes(
            Attachment::sum('file_size')
        );
    }

    public function getTotalArchives(): int
    {
        return DocumentArchive::count();
    }

    public function getActiveArchives(): int
    {
        return DocumentArchive::where('status', 'active')->count();
    }

    public function getArchivedArchives(): int
    {
        return DocumentArchive::where('status', 'archived')->count();
    }

    public function getExpiredArchives(): int
    {
        return DocumentArchive::whereDate(
            'expires_at',
            '<',
            now()
        )->count();
    }

    public function getFilesForEntity(string $entityType): array
    {
        $attachments = Attachment::query()
            ->where(
                'attachable_type',
                DocumentArchive::class
            )
            ->whereHasMorph(
                'attachable',
                [DocumentArchive::class],
                function ($query) use ($entityType) {
                    $query->whereHas(
                        'documentLinks',
                        function ($query) use ($entityType) {
                            $query->where(
                                'entity_type',
                                $entityType
                            );
                        }
                    );
                }
            );

        return [
            'count' => (clone $attachments)->count(),
            'size' => $this->formatBytes(
                (clone $attachments)->sum('file_size')
            ),
        ];
    }

    public function getGenericFiles(): int
    {
        return Attachment::query()
            ->where(
                'attachable_type',
                DocumentArchive::class
            )
            ->whereDoesntHave(
                'attachable.documentLinks'
            )
            ->count();
    }

    public function getPdfFiles(): int
    {
        return Attachment::where(
            'mime_type',
            'application/pdf'
        )->count();
    }

    public function getImageFiles(): int
    {
        return Attachment::where(
            'mime_type',
            'like',
            'image/%'
        )->count();
    }

    public function getWordFiles(): int
    {
        return Attachment::whereIn(
            'mime_type',
            [
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ]
        )->count();
    }

    public function getExcelFiles(): int
    {
        return Attachment::whereIn(
            'mime_type',
            [
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]
        )->count();
    }

    protected function formatBytes(?int $bytes): string
    {
        $bytes = (int) ($bytes ?? 0);

        if ($bytes <= 0) {
            return '0 B';
        }

        $units = [
            'B',
            'KB',
            'MB',
            'GB',
            'TB',
        ];

        $power = min(
            floor(log($bytes, 1024)),
            count($units) - 1
        );

        return number_format(
            $bytes / (1024 ** $power),
            2,
            ',',
            '.'
        ).' '.$units[$power];
    }
}
