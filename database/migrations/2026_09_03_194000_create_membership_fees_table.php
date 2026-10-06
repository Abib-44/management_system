<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membership_fees', function (Blueprint $table) {
            $table->id();

            $table->foreignId('member_id')
                ->constrained('members')
                ->cascadeOnDelete();

            // Importo effettivamente pagato
            $table->decimal('amount', 10, 2);

            // Data del pagamento
            $table->date('payment_date');

            // Metodo di pagamento
            $table->enum('payment_method', [
                'cash',
                'bank_transfer',
                'card',
                'other',
            ])->default('cash');

            // Ricevuta
            $table->string('receipt_number', 50)->nullable();

            // Note
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index([
                'member_id',
                'payment_date',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_fees');
    }
};
