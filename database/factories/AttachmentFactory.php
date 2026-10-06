<?php

namespace Database\Factories;

use App\Models\Attachment;
use App\Models\DocumentArchive;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttachmentFactory extends Factory
{
    protected $model = Attachment::class;

    public function definition(): array
    {
        return [
            'attachable_type' => DocumentArchive::class,
            'attachable_id' => DocumentArchive::factory(),
            'file_path' => 'documents/01M2BGNVFD019RWKDZH72M7B11.png',
            'file_name' => 'logo.png',
            'mime_type' => 'image/png',
            'file_size' => 1_330_000,
            'label' => 'Logo',
            'version' => 1,
            'uploaded_by' => User::inRandomOrder()->value('id') ?? User::factory(),
        ];
    }

    /**
     * File già presente su MinIO/S3 (nessun upload).
     */
    public function existingFile(
        string $path,
        int $size,
        string $mime = 'image/webp',
        ?string $label = null
    ): static {
        return $this->state(fn () => [
            'file_path' => $path,
            'file_name' => basename($path),
            'mime_type' => $mime,
            'file_size' => $size,
            'label' => $label,
        ]);
    }
}
