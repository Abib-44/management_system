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
        Schema::create('student_payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            // Numero della rata
            $table->unsignedInteger('installment_number');

            // Descrizione del pagamento
            $table->string('description');

            // Importo effettivamente pagato
            $table->decimal('amount', 10, 2);

            // Data del pagamento
            $table->date('payment_date');

            // Metodo di pagamento
            $table->enum('payment_method', [
                'cash',
                'bank_transfer',
                'card',
                'check',
                'other',
            ])->default('cash');

            // Numero ricevuta
            $table->string('receipt_number', 100)->nullable();

            // Note
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index([
                'student_id',
                'installment_number',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_payments');
    }
};
