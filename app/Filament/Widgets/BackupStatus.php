<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class BackupStatus extends Widget
{
    protected string $view = 'filament.widgets.backup-status';

    protected int|string|array $columnSpan = 2;

    protected static ?int $sort = 2;

    public function runBackup(): void
    {
        set_time_limit(0);

        try {
            Artisan::call('backup:run');

            // 2. Cerca l'ultimo backup creato
            $disk = Storage::disk('local');

            $latestBackup = collect($disk->allFiles())
                ->filter(
                    fn ($file) => str_starts_with($file, 'backups/')
                        && str_ends_with(strtolower($file), '.zip')
                )
                ->sortByDesc(
                    fn ($file) => $disk->lastModified($file)
                )
                ->first();

            if (! $latestBackup) {
                Notification::make()
                    ->title('Backup completato')
                    ->body('Il backup è terminato ma non è stato trovato il file ZIP.')
                    ->warning()
                    ->send();

                return;
            }

            // 3. Notifica
            Notification::make()
                ->title('Backup completato')
                ->body('Download del backup in corso...')
                ->success()
                ->send();

            // 4. Secondo evento: avvia il download
            $this->dispatch(
                'backup-download',
                url: route('backup.download', [
                    'path' => $latestBackup,
                ])
            );

        } catch (\Throwable $e) {
            Notification::make()
                ->title('Errore durante il backup')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function getBackupData(): array
    {
        $disk = Storage::disk('local');

        $backups = collect($disk->allFiles())
            ->filter(
                fn ($file) => str_starts_with($file, 'backups/')
                    && str_ends_with(strtolower($file), '.zip')
            )
            ->map(function ($file) use ($disk) {
                $sizeBytes = $disk->size($file);

                return [
                    'name' => basename($file),
                    'path' => $file,
                    'date' => Carbon::createFromTimestamp(
                        $disk->lastModified($file)
                    ),
                    'size_bytes' => $sizeBytes,
                    'size' => $this->formatSize($sizeBytes),
                ];
            })
            ->sortByDesc('date')
            ->values();

        $latestBackup = $backups->first();

        $todayBackups = $backups->filter(
            fn ($backup) => $backup['date']->isToday()
        );

        $totalSizeBytes = $backups->sum(
            fn ($backup) => $backup['size_bytes'] ?? 0
        );

        return [
            'backups' => $backups,
            'latestBackup' => $latestBackup,
            'todayCount' => $todayBackups->count(),
            'backupToday' => $todayBackups->isNotEmpty(),
            'totalSize' => $this->formatSize($totalSizeBytes),
        ];
    }

    protected function formatSize(int|float $bytes): string
    {
        if ($bytes >= 1024 ** 3) {
            return number_format(
                $bytes / (1024 ** 3),
                2,
                ',',
                '.'
            ).' GB';
        }

        if ($bytes >= 1024 ** 2) {
            return number_format(
                $bytes / (1024 ** 2),
                2,
                ',',
                '.'
            ).' MB';
        }

        if ($bytes >= 1024) {
            return number_format(
                $bytes / 1024,
                2,
                ',',
                '.'
            ).' KB';
        }

        return $bytes.' B';
    }
}
