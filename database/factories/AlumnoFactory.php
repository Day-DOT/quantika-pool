<?php

namespace Database\Factories;

use App\Enums\EstadoAlumno;
use App\Models\Alumno;
use App\Models\Nivel;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Alumno>
 */
class AlumnoFactory extends Factory
{
    protected $model = Alumno::class;

    public function definition(): array
    {
        return [
            'tutor_user_id' => User::factory()->tutor(),
            'sucursal_id' => Sucursal::factory(),
            'nivel_id' => Nivel::inRandomOrder()->value('id'),
            'sub_nivel' => 1,
            'nombre' => fake()->firstName(),
            'apellidos' => fake()->lastName() . ' ' . fake()->lastName(),
            'fecha_nacimiento' => fake()->dateTimeBetween('-14 years', '-4 years'),
            'telefono' => fake()->phoneNumber(),
            'email' => fake()->optional()->safeEmail(),
            'observaciones' => fake()->optional()->sentence(),
            'estado' => EstadoAlumno::Activo->value,
            'fecha_inscripcion' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
