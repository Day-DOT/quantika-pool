<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_status_history', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            $table->enum('previous_status', [
                'active',
                'temporary_leave',
                'inactive'
            ])->nullable();

            $table->enum('new_status', [
                'active',
                'temporary_leave',
                'inactive'
            ]);

            // Motivo del cambio
            $table->text('reason')->nullable();

            // Usuario que realizó el cambio
            $table->foreignId('changed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('changed_at')->useCurrent();

            $table->timestamps();

            $table->index(['student_id', 'changed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_status_history');
    }
};