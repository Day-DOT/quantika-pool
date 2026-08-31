<?php

namespace Database\Factories;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Plan>
 */
class PlanFactory extends Factory
{
    protected $model = Plan::class;

    public function definition(): array
    {
        $clasesPorSemana = fake()->randomElement([2, 3]);

        return [
            'nombre' => "Plan {$clasesPorSemana} clases/semana",
            'clases_por_semana' => $clasesPorSemana,
            'precio' => fake()->randomFloat(2, 700, 1200),
            'activo' => true,
        ];
    }
}
