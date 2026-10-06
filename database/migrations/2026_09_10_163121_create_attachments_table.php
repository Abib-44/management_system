<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();

            // Relazione polimorfica: attachable_id + attachable_type
            // Permette di collegare gli attachment non solo a document_archives
            // ma anche ad altri modelli futuri, senza modificare lo schema.
            $table->morphs('attachable');

            $table->string('file_path');
            $table->string('file_name');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();

            // Utile per distinguere il tipo di pagina/lato del documento
            // (es. 'front', 'back', 'page_1', ecc.) senza colonne fisse.
            $table->string('label')->nullable();

            // Utile per gestire versioni multiple dello stesso documento.
            $table->unsignedInteger('version')->default(1);

            $table->foreignId('uploaded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
