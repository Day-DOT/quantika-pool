<?php

namespace Database\Factories;

use App\Models\CriterioEvaluacion;
use App\Models\Nivel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CriterioEvaluacion>
 */
class CriterioEvaluacionFactory extends Factory
{
    protected $model = CriterioEvaluacion::class;

    public function definition(): array
    {
        return [
            'nivel_id' => Nivel::factory(),
            'nombre' => fake()->randomElement([
                'Flotación', 'Respiración', 'Técnica de brazada', 'Coordinación', 'Resistencia',
            ]),
            'descripcion' => fake()->optional()->sentence(),
            'orden' => fake()->numberBetween(0, 10),
            'activo' => true,
        ];
    }
}
