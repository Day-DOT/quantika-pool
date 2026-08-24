<?php

namespace Database\Factories;

use App\Models\Carril;
use App\Models\Sucursal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Carril>
 */
class CarrilFactory extends Factory
{
    protected $model = Carril::class;

    public function definition(): array
    {
        return [
            'sucursal_id' => Sucursal::factory(),
            'nombre' => 'Carril ' . fake()->unique()->numberBetween(1, 8),
            'capacidad_maxima' => fake()->numberBetween(6, 10),
            'activo' => true,
        ];
    }
}
