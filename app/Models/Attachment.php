<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'attachable_type',
        'attachable_id',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
        'label',
        'version',
        'uploaded_by',
    ];

    protected static function booted(): void
    {
        static::creating(function (Attachment $attachment) {
            $attachment->uploaded_by ??= auth()->id();
            $attachment->version ??= 1;
        });
    }

    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
