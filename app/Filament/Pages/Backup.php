<?php

namespace App\Filament\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;
use ZipArchive;

class Backup extends Page
{
    use HasPageShield;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-server-stack';

    protected static ?string $navigationLabel = 'Backup';

    protected static ?string $title = 'Gestione Backup';

    /**
     * File temporanei da eliminare dopo la chiusura dello ZIP
     * (non si possono cancellare prima, altrimenti ZipArchive perde il riferimento).
     */
    protected array $tempFilesToCleanup = [];

    protected string $view = 'filament.pages.backup';

    /*
    |--------------------------------------------------------------------------
    | NAVIGATION
    |--------------------------------------------------------------------------
    */

    public static function getNavigationGroup(): ?string
    {
        return 'Sistema';
    }

    /*
    |--------------------------------------------------------------------------
    | HEADER ACTIONS
    |--------------------------------------------------------------------------
    */

    protected function getHeaderActions(): array
    {
        return [

            Action::make('createBackup')
                ->label('Esegui Backup Ora')
                ->icon('heroicon-o-arrow-path')
                ->color('primary')
                ->action('createBackup')
                ->requiresConfirmation()
                ->modalHeading('Eseguire il backup?')
                ->modalDescription(
                    'Verrà creato un nuovo backup. Il sistema conserverà automaticamente solo gli ultimi 3 backup.'
                )
                ->modalSubmitActionLabel('Esegui Backup'),

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | GET BACKUPS
    |--------------------------------------------------------------------------
    */

    public function getBackups()
    {
        $disk = Storage::disk('local');

        if (! $disk->exists('backups')) {
            return collect();
        }

        return collect($disk->files('backups'))
            ->filter(function ($path) {
                return Str::endsWith(strtolower($path), '.zip');
            })
            ->map(function ($path) use ($disk) {

                $fullPath = $disk->path($path);

                $timestamp = filemtime($fullPath);

                $date = now()->createFromTimestamp($timestamp);

                return [
                    'name' => basename($path),

                    'path' => $path,

                    'date' => $date,

                    'size' => $this->formatBytes(
                        $disk->size($path)
                    ),

                    'size_bytes' => $disk->size($path),
                ];
            })
            ->sortByDesc('date')
            ->values();
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE BACKUP
    |--------------------------------------------------------------------------
    */

    public function createBackup(): void
    {
        try {

            $disk = Storage::disk('local');

            /*
            |--------------------------------------------------------------------------
            | CREA DIRECTORY
            |--------------------------------------------------------------------------
            */

            if (! $disk->exists('backups')) {
                $disk->makeDirectory('backups');
            }

            /*
            |--------------------------------------------------------------------------
            | NOME FILE
            |--------------------------------------------------------------------------
            */

            $fileName = 'backup-'.now()->format('Y-m-d-H-i-s').'.zip';

            $relativePath = 'backups/'.$fileName;

            $zipPath = $disk->path($relativePath);

            /*
            |--------------------------------------------------------------------------
            | CREA ZIP
            |--------------------------------------------------------------------------
            */

            $zip = new ZipArchive;

            $result = $zip->open(
                $zipPath,
                ZipArchive::CREATE | ZipArchive::OVERWRITE
            );

            if ($result !== true) {
                throw new \RuntimeException(
                    'Impossibile creare il file ZIP.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | CARTELLE DA INCLUDERE
            |--------------------------------------------------------------------------
            |
            | Qui puoi modificare cosa vuoi salvare.
            |
            */

            $this->addDirectoryToZip(
                $zip,
                base_path('app'),
                'app'
            );

            $this->addDirectoryToZip(
                $zip,
                base_path('config'),
                'config'
            );

            $this->addDirectoryToZip(
                $zip,
                base_path('database'),
                'database'
            );

            $this->addDirectoryToZip(
                $zip,
                base_path('resources'),
                'resources'
            );

            $this->addDirectoryToZip(
                $zip,
                base_path('routes'),
                'routes'
            );

            /*
            |--------------------------------------------------------------------------
            | FILE IMPORTANTI
            |--------------------------------------------------------------------------
            */

            $files = [
                '.env',
                'composer.json',
                'composer.lock',
                'artisan',
            ];

            foreach ($files as $file) {

                $path = base_path($file);

                if (is_file($path)) {

                    $zip->addFile(
                        $path,
                        $file
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | STORAGE PUBLIC
            |--------------------------------------------------------------------------
            */

            $publicStorage = storage_path('app/public');

            if (is_dir($publicStorage)) {

                $this->addDirectoryToZip(
                    $zip,
                    $publicStorage,
                    'storage/app/public'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | DATABASE
            |--------------------------------------------------------------------------
            |
            | Se utilizzi SQLite, salva anche il database.
            |
            */

            $databasePath = database_path('database.sqlite');

            if (is_file($databasePath)) {

                $zip->addFile(
                    $databasePath,
                    'database/database.sqlite'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | DUMP DATABASE MYSQL
            |--------------------------------------------------------------------------
            */

            $this->addMysqlDumpToZip($zip);

            /*
            |--------------------------------------------------------------------------
            | FILE SU MINIO (S3)
            |--------------------------------------------------------------------------
            */

            $this->addMinioFilesToZip($zip);

            /*
            |--------------------------------------------------------------------------
            | CHIUDI ZIP
            |--------------------------------------------------------------------------
            */

            $zip->close();

            /*
            |--------------------------------------------------------------------------
            | PULIZIA FILE TEMPORANEI
            |--------------------------------------------------------------------------
            |
            | Va fatta DOPO la chiusura dello zip, altrimenti ZipArchive
            | perde il riferimento ai file aggiunti con addFile().
            |
            */

            foreach ($this->tempFilesToCleanup as $tmpFile) {
                @unlink($tmpFile);
            }

            $this->tempFilesToCleanup = [];

            /*
            |--------------------------------------------------------------------------
            | ELIMINA BACKUP VECCHI
            |--------------------------------------------------------------------------
            */

            $this->removeOldBackups();

            /*
            |--------------------------------------------------------------------------
            | NOTIFICA
            |--------------------------------------------------------------------------
            */

            Notification::make()
                ->title('Backup completato')
                ->body(
                    'Il backup è stato creato correttamente. Sono conservati solo gli ultimi 3 backup.'
                )
                ->success()
                ->send();

            /*
            |--------------------------------------------------------------------------
            | REFRESH
            |--------------------------------------------------------------------------
            */

            $this->dispatch('$refresh');

        } catch (\Throwable $e) {

            report($e);

            Notification::make()
                ->title('Errore durante il backup')
                ->body(
                    $e->getMessage()
                )
                ->danger()
                ->send();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | ADD DIRECTORY TO ZIP
    |--------------------------------------------------------------------------
    */

    protected function addDirectoryToZip(
        ZipArchive $zip,
        string $directory,
        string $zipDirectory
    ): void {

        if (! is_dir($directory)) {
            return;
        }

        $directory = realpath($directory);

        if (! $directory) {
            return;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $directory,
                \FilesystemIterator::SKIP_DOTS
            )
        );

        foreach ($iterator as $file) {

            if (! $file->isFile()) {
                continue;
            }

            $filePath = $file->getRealPath();

            if (! $filePath) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | ESCLUSIONI
            |--------------------------------------------------------------------------
            */

            if (
                str_contains($filePath, DIRECTORY_SEPARATOR.'.git'.DIRECTORY_SEPARATOR)
                ||
                str_contains($filePath, DIRECTORY_SEPARATOR.'node_modules'.DIRECTORY_SEPARATOR)
                ||
                str_contains($filePath, DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR)
            ) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | PERCORSO RELATIVO
            |--------------------------------------------------------------------------
            */

            $relativePath = ltrim(
                str_replace(
                    $directory,
                    '',
                    $filePath
                ),
                DIRECTORY_SEPARATOR
            );

            $zipPath = $zipDirectory.'/'.str_replace(
                DIRECTORY_SEPARATOR,
                '/',
                $relativePath
            );

            $zip->addFile(
                $filePath,
                $zipPath
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | ADD MYSQL DUMP TO ZIP
    |--------------------------------------------------------------------------
    */

    protected function addMysqlDumpToZip(ZipArchive $zip): void
    {
        $connectionName = config('database.default');

        $connection = config("database.connections.{$connectionName}");

        /*
        |----------------------------------------------------------------
        | Esegue il dump solo se la connessione di default è MySQL/MariaDB
        |----------------------------------------------------------------
        */
        if (! in_array($connection['driver'] ?? null, ['mysql', 'mariadb'], true)) {
            return;
        }

        $finder = new ExecutableFinder;

        $mysqldumpBinary = $finder->find('mysqldump');

        if (! $mysqldumpBinary) {
            throw new \RuntimeException(
                'mysqldump non è installato nel container. Il backup del database MySQL non può essere eseguito.'
            );
        }

        $tmpFile = tempnam(sys_get_temp_dir(), 'mysqldump_').'.sql';

        $process = new Process([
            $mysqldumpBinary,
            '-h', $connection['host'],
            '-P', (string) ($connection['port'] ?? 3306),
            '-u', $connection['username'],
            '--single-transaction',
            '--quick',
            '--routines',
            '--triggers',
            $connection['database'],
        ]);

        /*
        |----------------------------------------------------------------
        | La password passa come variabile d'ambiente, MAI come argomento
        | da riga di comando (altrimenti sarebbe visibile in `ps aux`).
        |----------------------------------------------------------------
        */
        $process->setEnv([
            'MYSQL_PWD' => $connection['password'] ?? '',
        ]);

        $process->setTimeout(600);

        $handle = fopen($tmpFile, 'w');

        $process->run(function ($type, $buffer) use ($handle) {
            if ($type === Process::OUT) {
                fwrite($handle, $buffer);
            }
        });

        fclose($handle);

        if (! $process->isSuccessful() || ! is_file($tmpFile) || filesize($tmpFile) === 0) {

            @unlink($tmpFile);

            throw new \RuntimeException(
                'Dump del database MySQL fallito: '.$process->getErrorOutput()
            );
        }

        $zip->addFile($tmpFile, 'database/mysql-dump.sql');

        $this->tempFilesToCleanup[] = $tmpFile;
    }

    /*
    |--------------------------------------------------------------------------
    | ADD MINIO FILES TO ZIP
    |--------------------------------------------------------------------------
    */

    protected function addMinioFilesToZip(ZipArchive $zip): void
    {
        /*
        |----------------------------------------------------------------
        | Se il disco s3/MinIO non è configurato, salta senza errori
        |----------------------------------------------------------------
        */
        if (! config('filesystems.disks.s3.key')) {
            return;
        }

        try {
            $disk = Storage::disk('s3');

            $files = $disk->allFiles();

        } catch (\Throwable $e) {

            report($e);

            throw new \RuntimeException(
                'Impossibile connettersi a MinIO: '.$e->getMessage()
            );
        }

        foreach ($files as $file) {

            try {

                $contents = $disk->get($file);

                $zip->addFromString('minio/'.$file, $contents);

            } catch (\Throwable $e) {

                report($e);

                continue;
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | REMOVE OLD BACKUPS
    |--------------------------------------------------------------------------
    */

    protected function removeOldBackups(): void
    {
        $backups = $this->getBackups();

        /*
        |--------------------------------------------------------------------------
        | Mantieni solamente i primi 3.
        |--------------------------------------------------------------------------
        */

        $oldBackups = $backups->slice(3);

        $disk = Storage::disk('local');

        foreach ($oldBackups as $backup) {

            $path = $backup['path'] ?? null;

            if (! $path) {
                continue;
            }

            if ($disk->exists($path)) {

                $disk->delete($path);
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE BACKUP
    |--------------------------------------------------------------------------
    */

    public function deleteBackup(string $path): void
    {
        try {

            /*
            |--------------------------------------------------------------------------
            | SICUREZZA PATH
            |--------------------------------------------------------------------------
            */

            if (! str_starts_with($path, 'backups/')) {

                Notification::make()
                    ->title('Backup non valido')
                    ->danger()
                    ->send();

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | EVITA PATH TRAVERSAL
            |--------------------------------------------------------------------------
            */

            if (
                str_contains($path, '..') ||
                str_contains($path, '\\')
            ) {

                Notification::make()
                    ->title('Percorso non valido')
                    ->danger()
                    ->send();

                return;
            }

            $disk = Storage::disk('local');

            /*
            |--------------------------------------------------------------------------
            | FILE ESISTENTE?
            |--------------------------------------------------------------------------
            */

            if (! $disk->exists($path)) {

                Notification::make()
                    ->title('Backup non trovato')
                    ->body(
                        'Il file potrebbe essere già stato eliminato.'
                    )
                    ->warning()
                    ->send();

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | DELETE
            |--------------------------------------------------------------------------
            */

            $disk->delete($path);

            /*
            |--------------------------------------------------------------------------
            | NOTIFICATION
            |--------------------------------------------------------------------------
            */

            Notification::make()
                ->title('Backup eliminato')
                ->body(
                    'Il backup è stato eliminato correttamente.'
                )
                ->success()
                ->send();

            /*
            |--------------------------------------------------------------------------
            | REFRESH
            |--------------------------------------------------------------------------
            */

            $this->dispatch('$refresh');

        } catch (\Throwable $e) {

            report($e);

            Notification::make()
                ->title('Errore')
                ->body(
                    'Non è stato possibile eliminare il backup.'
                )
                ->danger()
                ->send();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | FORMAT BYTES
    |--------------------------------------------------------------------------
    */

    protected function formatBytes(
        int|float $bytes,
        int $precision = 2
    ): string {

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

        $power = floor(
            log($bytes, 1024)
        );

        $power = min(
            $power,
            count($units) - 1
        );

        return number_format(
            $bytes / (1024 ** $power),
            $precision,
            ',',
            '.'
        ).' '.$units[$power];
    }
}
