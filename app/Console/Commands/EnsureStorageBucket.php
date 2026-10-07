<?php

namespace App\Console\Commands;

use Aws\S3\S3Client;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use RuntimeException;
use Throwable;

#[Signature('app:ensure-storage-bucket')]
#[Description('Ensure the S3/MinIO bucket and fixture files exist')]
class EnsureStorageBucket extends Command
{
    private const FILES = [
        'carte_d_identita.webp',
        'codice-fiscale.webp',
        'passport.webp',
    ];

    private const LOCAL_PATH = 'images/minio';

    private const MINIO_PATH = 'documents';

    public function handle(): int
    {
        $bucket = config('filesystems.disks.s3.bucket');

        if (! $bucket) {
            $this->error('S3 bucket non configurato.');

            return self::FAILURE;
        }

        try {
            $client = $this->createClient();

            $this->ensureBucket($client, $bucket);
            $this->ensureFiles($client, $bucket);

            $this->newLine();
            $this->info('MinIO inizializzato correttamente.');

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->newLine();
            $this->error('Impossibile inizializzare MinIO.');
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }

    private function createClient(): S3Client
    {
        return new S3Client([
            'version' => 'latest',
            'region' => config('filesystems.disks.s3.region', 'us-east-1'),
            'endpoint' => config('filesystems.disks.s3.endpoint'),
            'use_path_style_endpoint' => config(
                'filesystems.disks.s3.use_path_style_endpoint',
                false
            ),
            'credentials' => [
                'key' => config('filesystems.disks.s3.key'),
                'secret' => config('filesystems.disks.s3.secret'),
            ],
        ]);
    }

    private function ensureBucket(
        S3Client $client,
        string $bucket
    ): void {
        try {
            $client->headBucket([
                'Bucket' => $bucket,
            ]);

            $this->info("Bucket [{$bucket}] già esistente.");

            return;
        } catch (Throwable $e) {
            $this->line("Bucket [{$bucket}] non trovato. Lo creo...");
        }

        try {
            $client->createBucket([
                'Bucket' => $bucket,
            ]);

            $this->info("Bucket [{$bucket}] creato.");
        } catch (Throwable $e) {
            /*
             * Può succedere che il bucket sia stato creato
             * da un altro processo tra headBucket() e createBucket().
             */
            try {
                $client->headBucket([
                    'Bucket' => $bucket,
                ]);

                $this->info("Bucket [{$bucket}] già disponibile.");

                return;
            } catch (Throwable) {
                throw new RuntimeException(
                    "Impossibile creare il bucket [{$bucket}]: ".$e->getMessage(),
                    previous: $e
                );
            }
        }
    }

    private function ensureFiles(
        S3Client $client,
        string $bucket
    ): void {
        foreach (self::FILES as $filename) {
            $localPath = public_path(
                self::LOCAL_PATH.'/'.$filename
            );

            $remotePath = self::MINIO_PATH.'/'.$filename;

            if (! is_file($localPath)) {
                throw new RuntimeException(
                    "Fixture locale non trovata: {$localPath}"
                );
            }

            if ($this->fileExists($client, $bucket, $remotePath)) {
                $this->line(
                    "File già presente: {$remotePath}"
                );

                continue;
            }

            $this->line(
                "Upload MinIO: {$remotePath}"
            );

            $client->putObject([
                'Bucket' => $bucket,
                'Key' => $remotePath,
                'Body' => fopen($localPath, 'rb'),
                'ContentType' => 'image/webp',
            ]);

            $this->info(
                "  ✓ {$remotePath}"
            );
        }
    }

    private function fileExists(
        S3Client $client,
        string $bucket,
        string $path
    ): bool {
        try {
            $client->headObject([
                'Bucket' => $bucket,
                'Key' => $path,
            ]);

            return true;
        } catch (Throwable) {
            return false;
        }
    }
}
