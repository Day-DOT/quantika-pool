<?php

namespace Database\Factories;

use App\Models\Nivel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Nivel>
 */
class NivelFactory extends Factory
{
    protected $model = Nivel::class;

    public function definition(): array
    {
        return [
            'orden' => fake()->unique()->numberBetween(1, 999),
            'nombre' => fake()->word(),
            'categoria' => fake()->randomElement(['Principiante', 'Intermedio', 'Avanzado']),
            'categoria_edad' => 'Niños',
            'descripcion' => fake()->sentence(),
            'activo' => true,
        ];
    }
}
