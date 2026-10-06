<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // I file temporanei di Livewire passano dal server (nginx + PHP),
        // non direttamente dal browser a MinIO.
        config(['livewire.temporary_file_upload.disk' => 'local']);
    }
}