<?php

namespace Database\Factories;

use App\Enums\EstadoEvaluacionDetalle;
use App\Models\CriterioEvaluacion;
use App\Models\Evaluacion;
use App\Models\EvaluacionDetalle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EvaluacionDetalle>
 */
class EvaluacionDetalleFactory extends Factory
{
    protected $model = EvaluacionDetalle::class;

    public function definition(): array
    {
        return [
            'evaluacion_id' => Evaluacion::factory(),
            'criterio_evaluacion_id' => CriterioEvaluacion::factory(),
            'estado' => fake()->randomElement(EstadoEvaluacionDetalle::cases())->value,
            'observaciones' => null,
        ];
    }
}
