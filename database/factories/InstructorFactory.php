<?php

namespace Database\Factories;

use App\Models\Instructor;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Instructor>
 */
class InstructorFactory extends Factory
{
    protected $model = Instructor::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->instructor(),
            'sucursal_id' => Sucursal::factory(),
            'especialidad' => fake()->randomElement([
                'Nivel principiante', 'Nivel intermedio', 'Nivel avanzado', 'Natación adaptada',
            ]),
            'estado' => 'activo',
        ];
    }
}
