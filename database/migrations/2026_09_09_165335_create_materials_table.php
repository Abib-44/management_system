<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();

            $table->foreignId('subject_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name');

            $table->text('description')->nullable();

            $table->string('type')->nullable();

            $table->unsignedInteger('quantity')->default(0);

            $table->unsignedInteger('available_quantity')->default(0);

            $table->string('location')->nullable();

            $table->boolean('active')->default(true);

            $table->timestamps();

            $table->index(['subject_id', 'active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
