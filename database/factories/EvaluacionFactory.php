<?php

namespace Database\Factories;

use App\Models\Alumno;
use App\Models\Evaluacion;
use App\Models\Instructor;
use App\Models\Nivel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Evaluacion>
 */
class EvaluacionFactory extends Factory
{
    protected $model = Evaluacion::class;

    public function definition(): array
    {
        return [
            'alumno_id' => Alumno::factory(),
            'instructor_id' => Instructor::factory(),
            'nivel_id' => Nivel::factory(),
            'fecha' => fake()->dateTimeBetween('-2 months', 'now'),
            'observaciones' => fake()->optional()->sentence(),
        ];
    }
}
