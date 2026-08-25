<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_level_history', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            $table->foreignId('level_id')
                ->constrained('levels')
                ->restrictOnDelete();

            // Usuario que asignó o cambió el nivel
            $table->foreignId('assigned_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->date('assigned_at');

            // Fecha en que dejó ese nivel
            $table->date('completed_at')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['student_id', 'assigned_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_level_history');
    }
};