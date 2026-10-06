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
        Schema::create('students', function (Blueprint $table) {
            $table->id();

            // Dati anagrafici
            $table->string('first_name');
            $table->string('last_name');
            $table->date('birth_date')->nullable();

            // Genitori
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mother_phone', 20)->nullable();
            $table->string('father_phone', 20)->nullable();

            // Contatti
            $table->string('address')->nullable();
            $table->string('email')->nullable();

            // Classe
            $table->foreignId('class_room_id')
                ->nullable()
                ->constrained('class_rooms')
                ->nullOnDelete();

            // Quota
            $table->decimal('total_fee', 10, 2)->default(0);

            // Piano di pagamento
            $table->enum('installment_plan', [
                'no_interest',
                'installment_1',
                'installment_2',
            ])->nullable();

            // Note
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
