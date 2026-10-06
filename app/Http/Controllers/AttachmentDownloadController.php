<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttachmentDownloadController extends Controller
{
    public function __invoke(Request $request, Attachment $attachment): StreamedResponse
    {
        abort_unless(auth()->user()->can('view', $attachment), 403);

        $disk = Storage::disk('s3');
        $disposition = $request->boolean('download') ? 'attachment' : 'inline';

        return new StreamedResponse(
            function () use ($disk, $attachment) {
                fpassthru($disk->readStream($attachment->file_path));
            },
            200,
            [
                'Content-Type' => $attachment->mime_type,
                'Content-Disposition' => $disposition.'; filename="'.$attachment->file_name.'"',
                'X-Content-Type-Options' => 'nosniff',
            ]
        );
    }
}
