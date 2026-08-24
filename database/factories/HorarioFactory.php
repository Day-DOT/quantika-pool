<?php

namespace Database\Factories;

use App\Models\Carril;
use App\Models\Horario;
use App\Models\Instructor;
use App\Models\Nivel;
use App\Models\Sucursal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Horario>
 */
class HorarioFactory extends Factory
{
    protected $model = Horario::class;

    public function definition(): array
    {
        $horaInicio = fake()->numberBetween(8, 17);

        return [
            'sucursal_id' => Sucursal::factory(),
            'nivel_id' => Nivel::factory(),
            'instructor_id' => Instructor::factory(),
            'carril_id' => Carril::factory(),
            'nombre_grupo' => fake()->randomElement([
                'Delfines', 'Tiburones', 'Orcas', 'Caballitos', 'Mantarrayas', 'Estrellitas',
            ]),
            'dia_semana' => fake()->numberBetween(1, 7),
            'hora_inicio' => sprintf('%02d:00:00', $horaInicio),
            'hora_fin' => sprintf('%02d:00:00', $horaInicio + 1),
            'capacidad_maxima' => fake()->numberBetween(6, 12),
            'activo' => true,
        ];
    }
}
