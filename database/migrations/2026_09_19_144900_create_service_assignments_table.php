<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_assignments', function (Blueprint $table): void {
            $table->id();
            $table->string('type');
            $table->string('name');
            $table->string('assignee_name');
            $table->string('document_number')->nullable();
            $table->date('delivered_at');
            $table->date('returned_at')->nullable();
            $table->enum('status', ['delivered', 'returned', 'lost', 'active', 'completed']);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['type', 'status']);
            $table->index('returned_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_assignments');
    }
};
