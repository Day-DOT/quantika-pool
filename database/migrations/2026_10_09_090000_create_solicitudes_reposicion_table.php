<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitudes_reposicion', function (Blueprint $table) {
            $table->id();
            // La falta que el tutor quiere reponer.
            $table->foreignId('cita_id')->constrained('citas')->cascadeOnDelete();
            $table->foreignId('alumno_id')->constrained('alumnos')->cascadeOnDelete();
            $table->foreignId('horario_id')->constrained('horarios')->cascadeOnDelete();
            $table->date('fecha');
            $table->string('estado')->default('pendiente');
            $table->foreignId('resuelta_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resuelta_en')->nullable();
            $table->timestamps();

            $table->index(['estado', 'alumno_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes_reposicion');
    }
};
