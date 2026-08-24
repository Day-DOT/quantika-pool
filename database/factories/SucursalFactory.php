<?php

namespace Database\Factories;

use App\Models\Sucursal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sucursal>
 */
class SucursalFactory extends Factory
{
    protected $model = Sucursal::class;

    public function definition(): array
    {
        $numero = fake()->unique()->numberBetween(1, 999);

        return [
            'nombre' => "Sucursal {$numero}",
            'codigo' => "SUC{$numero}",
            'direccion' => fake()->address(),
            'telefono' => fake()->phoneNumber(),
            'activa' => true,
        ];
    }
}
