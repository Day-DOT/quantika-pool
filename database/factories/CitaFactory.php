<?php

namespace Database\Factories;

use App\Enums\EstadoCita;
use App\Models\Alumno;
use App\Models\Cita;
use App\Models\Horario;
use App\Models\Sucursal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cita>
 */
class CitaFactory extends Factory
{
    protected $model = Cita::class;

    public function definition(): array
    {
        return [
            'horario_id' => Horario::factory(),
            'alumno_id' => Alumno::factory(),
            'sucursal_id' => Sucursal::factory(),
            'fecha' => fake()->dateTimeBetween('-1 month', '+1 week'),
            'hora_inicio' => '10:00:00',
            'hora_fin' => '11:00:00',
            'estado' => EstadoCita::Programada->value,
            'asistio' => null,
            'notas' => null,
            'registrado_por' => null,
        ];
    }
}
