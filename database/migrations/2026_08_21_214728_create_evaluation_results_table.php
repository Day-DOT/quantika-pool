<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluation_results', function (Blueprint $table) {
            $table->id();

            // Evaluación general
            $table->foreignId('evaluation_id')
                ->constrained('evaluations')
                ->cascadeOnDelete();

            // Criterio evaluado
            $table->foreignId('evaluation_criterion_id')
                ->constrained('evaluation_criteria')
                ->cascadeOnDelete();

            /*
            Estados:
            not_started = No iniciado
            in_progress = En proceso
            achieved = Logrado
            */
            $table->enum('status', [
                'not_started',
                'in_progress',
                'achieved'
            ]);

            // Valor calculado para este criterio
            $table->decimal('score', 5, 2)
                ->default(0);

            $table->text('comments')
                ->nullable();

            $table->timestamps();

            // Un criterio solo puede evaluarse una vez
            // dentro de la misma evaluación
            $table->unique(
                ['evaluation_id', 'evaluation_criterion_id'],
                'eval_result_criterion_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluation_results');
    }
};