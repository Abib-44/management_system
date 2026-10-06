<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_archive_links', function (Blueprint $table) {
            $table->id();

            $table->foreignId('document_archive_id')
                ->constrained('document_archives')
                ->cascadeOnDelete();

            $table->enum('entity_type', ['member', 'activity', 'transaction', 'student'])
                ->index();

            $table->foreignId('member_id')->nullable()
                ->constrained('members')
                ->cascadeOnDelete();

            $table->foreignId('activity_id')->nullable()
                ->constrained('activities')
                ->cascadeOnDelete();

            $table->foreignId('transaction_id')->nullable()
                ->constrained('financial_transactions')
                ->cascadeOnDelete();

            $table->foreignId('student_id')->nullable()
                ->constrained('students')
                ->cascadeOnDelete();

            $table->foreignId('created_by')->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->unique(['document_archive_id', 'member_id']);
            $table->unique(['document_archive_id', 'activity_id']);
            $table->unique(['document_archive_id', 'transaction_id']);
            $table->unique(['document_archive_id', 'student_id']);
        });

        DB::statement('
            ALTER TABLE document_archive_links
            ADD CONSTRAINT chk_one_entity_only CHECK (
                (member_id IS NOT NULL) +
                (activity_id IS NOT NULL) +
                (transaction_id IS NOT NULL) +
                (student_id IS NOT NULL) = 1
            )
        ');
    }

    public function down(): void
    {
        Schema::dropIfExists('document_archive_links');
    }
};
