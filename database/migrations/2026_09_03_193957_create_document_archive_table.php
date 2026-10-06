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
        Schema::create('document_archives', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('document_category_id')
                ->constrained('document_categories')
                ->restrictOnDelete();
            $table->date('document_date')->nullable();
            $table->date('expires_at')->nullable();
            $table->string('status')->default('active');
            $table->foreignId('created_by')
                ->constrained('users');
            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_archives');
    }
};
