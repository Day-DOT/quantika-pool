<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluation_criteria', function (Blueprint $table) {
            $table->id();

            // Nivel al que pertenece este criterio
            $table->foreignId('level_id')
                ->constrained('levels')
                ->cascadeOnDelete();

            // Ejemplo: Respiración, Flotación, Patada, etc.
            $table->string('name');

            $table->text('description')
                ->nullable();

            // Peso del criterio para calcular el progreso
            $table->decimal('weight', 5, 2)
                ->default(0);

            // Orden en que aparecerá
            $table->unsignedInteger('sort_order')
                ->default(0);

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();

            $table->index(
                ['level_id', 'is_active'],
                'eval_criteria_level_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluation_criteria');
    }
};