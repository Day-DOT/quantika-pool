<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();

            // Clase a la que corresponde la asistencia
            $table->foreignId('class_id')
                ->constrained('classes')
                ->cascadeOnDelete();

            // Alumno
            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            // Sucursal donde se registró
            $table->foreignId('branch_id')
                ->constrained('branches')
                ->cascadeOnDelete();

            // Instructor que registró la asistencia
            $table->foreignId('recorded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
            present = Asistencia
            absent = Falta
            justified = Falta justificada
            */
            $table->enum('status', [
                'present',
                'absent',
                'justified'
            ])->default('present');

            $table->text('notes')->nullable();

            $table->timestamps();

            // Un alumno solo puede tener una asistencia por clase
            $table->unique(
                ['class_id', 'student_id'],
                'attendance_class_student_unique'
            );

            $table->index(
                ['student_id', 'branch_id'],
                'attendance_student_branch_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};