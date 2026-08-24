<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluacion_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluacion_id')->constrained('evaluaciones')->cascadeOnDelete();
            $table->foreignId('criterio_evaluacion_id')->constrained('criterios_evaluacion')->cascadeOnDelete();
            $table->string('estado')->default('no_iniciado');
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->unique(['evaluacion_id', 'criterio_evaluacion_id'], 'evaluacion_criterio_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluacion_detalles');
    }
};
