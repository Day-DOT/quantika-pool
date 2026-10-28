<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clases_muestra', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sucursal_id')->constrained('sucursales')->cascadeOnDelete();
            // Si el grupo se elimina, el registro se conserva en el historial.
            $table->foreignId('horario_id')->nullable()->constrained('horarios')->nullOnDelete();
            $table->date('fecha');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            // Datos del interesado: todavía no es alumno de la escuela.
            $table->string('nombre_alumno');
            $table->unsignedTinyInteger('edad');
            $table->string('nombre_tutor');
            $table->string('telefono', 30);
            $table->string('email')->nullable();
            // Versiones normalizadas para reconocer a quien ya tomó una clase muestra.
            $table->string('telefono_normalizado', 20)->index();
            $table->string('nombre_normalizado')->index();
            $table->string('estado')->default('programada');
            $table->text('notas')->nullable();
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['horario_id', 'fecha']);
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clases_muestra');
    }
};
