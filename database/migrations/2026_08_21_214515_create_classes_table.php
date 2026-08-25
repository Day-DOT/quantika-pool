<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();

            // Sucursal donde se realizará la clase
            $table->foreignId('branch_id')
                ->constrained('branches')
                ->cascadeOnDelete();

            // Grupo al que pertenece la clase
            $table->foreignId('group_id')
                ->nullable()
                ->constrained('groups')
                ->nullOnDelete();

            // Instructor asignado a la clase
            $table->foreignId('instructor_id')
                ->nullable()
                ->constrained('instructors')
                ->nullOnDelete();

            // Carril donde se realizará
            $table->foreignId('lane_id')
                ->nullable()
                ->constrained('lanes')
                ->nullOnDelete();

            // Fecha de la clase
            $table->date('class_date');

            // Horario real de la clase
            $table->time('start_time');

            $table->time('end_time');

            /*
            Estados posibles:
            scheduled = Programada
            completed = Realizada
            cancelled = Cancelada
            rescheduled = Reagendada
            */
            $table->enum('status', [
                'scheduled',
                'completed',
                'cancelled',
                'rescheduled'
            ])->default('scheduled');

            // Observaciones generales
            $table->text('notes')->nullable();

            $table->timestamps();

            // Índice corto para evitar problemas con MySQL
            $table->index(
                ['branch_id', 'class_date'],
                'class_branch_date_idx'
            );

            $table->index(
                ['instructor_id', 'class_date'],
                'class_instructor_date_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};