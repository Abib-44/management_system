<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use RuntimeException;

class StorageBucketSeeder extends Seeder
{
    public function run(): void
    {
        $exitCode = $this->command->call('app:ensure-storage-bucket');

        if ($exitCode !== 0) {
            throw new RuntimeException(
                'Inizializzazione del bucket MinIO fallita.'
            );
        }
    }
}
