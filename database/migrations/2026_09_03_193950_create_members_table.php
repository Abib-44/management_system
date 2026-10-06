<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();

            // Dati personali
            $table->string('last_name');
            $table->string('first_name');
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();

            // Iscrizione
            $table->date('registration_date');
            $table->date('renewal_date')->nullable();

            // Stato
            $table->enum('status', [
                'active',
                'inactive',
                'suspended',
            ])->default('active');

            $table->enum('assembly_status', [
                'active',
                'inactive',
            ])->default('active');

            // Quota annuale
            $table->decimal('annual_fee', 10, 2)->default(0);

            // Note
            $table->text('activity_notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
