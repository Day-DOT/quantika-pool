<?php

namespace Database\Factories;

use App\Models\Alumno;
use App\Models\Horario;
use App\Models\Inscripcion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Inscripcion>
 */
class InscripcionFactory extends Factory
{
    protected $model = Inscripcion::class;

    public function definition(): array
    {
        return [
            'horario_id' => Horario::factory(),
            'alumno_id' => Alumno::factory(),
            'fecha_inicio' => fake()->dateTimeBetween('-6 months', 'now'),
            'fecha_fin' => null,
            'activa' => true,
        ];
    }
}
