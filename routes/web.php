<?php

use App\Http\Controllers\AttachmentDownloadController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return Auth::check()
        ? redirect('/system')
        : redirect('/login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/attachments/{attachment}', AttachmentDownloadController::class)
        ->name('attachments.show');
});

// routes/web.php

Route::middleware(['auth'])->group(function () {
    Route::get('/attachments/{attachment}', AttachmentDownloadController::class)
        ->name('attachments.show');
});

Route::middleware(['auth'])->group(function () {

    Route::get('/attachments/{attachment}', AttachmentDownloadController::class)
        ->name('attachments.show');

    Route::get('/backup/download', function () {

        $path = request('path');

        abort_unless(is_string($path), 404);

        /*
        |----------------------------------------------------------------
        | SICUREZZA: deve stare dentro backups/, niente traversal
        |----------------------------------------------------------------
        */
        if (
            ! str_starts_with($path, 'backups/') ||
            str_contains($path, '..') ||
            str_contains($path, '\\')
        ) {
            abort(404);
        }

        $disk = Storage::disk('local');

        abort_unless($disk->exists($path), 404);

        return response()->download(
            $disk->path($path),
            basename($path)
        );

    })->name('backup.download');

});
