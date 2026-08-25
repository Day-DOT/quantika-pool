<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();

            // Alumno para quien se realiza la reservación
            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            // Sucursal donde será la reservación
            $table->foreignId('branch_id')
                ->constrained('branches')
                ->cascadeOnDelete();

            // Clase relacionada, si la reservación corresponde
            // a una clase existente
            $table->foreignId('class_id')
                ->nullable()
                ->constrained('classes')
                ->nullOnDelete();

            // Carril reservado, si aplica
            $table->foreignId('lane_id')
                ->nullable()
                ->constrained('lanes')
                ->nullOnDelete();

            // Usuario que realizó la reservación
            // Puede ser el alumno, tutor o administrador
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Fecha de la reservación
            $table->date('reservation_date');

            // Horario reservado
            $table->time('start_time');

            $table->time('end_time');

            /*
            Estados:
            pending = Pendiente
            confirmed = Confirmada
            cancelled = Cancelada
            completed = Realizada
            */
            $table->enum('status', [
                'pending',
                'confirmed',
                'cancelled',
                'completed'
            ])->default('pending');

            // Observaciones
            $table->text('notes')->nullable();

            $table->timestamps();

            // Índices para consultas frecuentes
            $table->index(
                ['student_id', 'reservation_date'],
                'reservation_student_date_idx'
            );

            $table->index(
                ['branch_id', 'reservation_date'],
                'reservation_branch_date_idx'
            );

            $table->index(
                ['lane_id', 'reservation_date'],
                'reservation_lane_date_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};