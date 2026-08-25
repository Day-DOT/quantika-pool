<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();

            // Sucursal a la que pertenece actualmente
            $table->foreignId('branch_id')
                ->constrained('branches')
                ->restrictOnDelete();

            // Nivel actual del alumno
            $table->foreignId('current_level_id')
                ->nullable()
                ->constrained('levels')
                ->nullOnDelete();

            // Datos personales
            $table->string('first_name');
            $table->string('last_name');

            $table->date('birth_date')->nullable();

            $table->enum('gender', [
                'female',
                'male',
                'other',
                'prefer_not_to_say'
            ])->nullable();

            // Datos de contacto propios, si aplica
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();

            $table->text('address')->nullable();

            // Fecha en que se registró el alumno
            $table->date('enrollment_date')->nullable();

            // Estado actual
            $table->enum('status', [
                'active',
                'temporary_leave',
                'inactive'
            ])->default('active');

            $table->timestamps();

            $table->index(['branch_id', 'status']);
            $table->index('current_level_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};