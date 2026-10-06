<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class DocumentArchive extends Model
{
    use HasFactory;

    protected $table = 'document_archives';

    protected $fillable = [
        'title',
        'document_category_id',
        'document_date',
        'expires_at',
        'status',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'document_date' => 'date',
            'expires_at' => 'date',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(
            DocumentCategory::class,
            'document_category_id'
        );
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'updated_by'
        );
    }

    public function documentLinks(): HasMany
    {
        return $this->hasMany(
            DocumentArchiveLink::class,
            'document_archive_id'
        );
    }
}
