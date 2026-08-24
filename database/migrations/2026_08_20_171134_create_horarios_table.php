<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('horarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sucursal_id')->constrained('sucursales')->cascadeOnDelete();
            $table->foreignId('nivel_id')->constrained('niveles')->restrictOnDelete();
            $table->foreignId('instructor_id')->constrained('instructores')->restrictOnDelete();
            $table->foreignId('carril_id')->constrained('carriles')->restrictOnDelete();
            $table->string('nombre_grupo');
            $table->unsignedTinyInteger('dia_semana');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->unsignedInteger('capacidad_maxima')->default(10);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('horarios');
    }
};
