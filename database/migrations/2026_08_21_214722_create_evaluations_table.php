<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();

            // Alumno evaluado
            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            // Nivel que se está evaluando
            $table->foreignId('level_id')
                ->constrained('levels')
                ->cascadeOnDelete();

            // Instructor que realizó la evaluación
            $table->foreignId('instructor_id')
                ->nullable()
                ->constrained('instructors')
                ->nullOnDelete();

            // Sucursal donde se realizó la evaluación
            $table->foreignId('branch_id')
                ->constrained('branches')
                ->cascadeOnDelete();

            // Clase relacionada, si la evaluación se hizo durante una clase
            $table->foreignId('class_id')
                ->nullable()
                ->constrained('classes')
                ->nullOnDelete();

            // Fecha de evaluación
            $table->date('evaluation_date');

            // Porcentaje general calculado
            $table->decimal('progress_percentage', 5, 2)
                ->default(0);

            // Observaciones generales del instructor
            $table->text('observations')
                ->nullable();

            $table->timestamps();

            $table->index(
                ['student_id', 'evaluation_date'],
                'evaluation_student_date_idx'
            );

            $table->index(
                ['branch_id', 'evaluation_date'],
                'evaluation_branch_date_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};