<?php

namespace Database\Seeders;

use App\Models\Alumno;
use App\Models\Carril;
use App\Models\Cita;
use App\Models\CriterioEvaluacion;
use App\Models\Evaluacion;
use App\Models\EvaluacionDetalle;
use App\Models\Horario;
use App\Models\Inscripcion;
use App\Models\Instructor;
use App\Models\Nivel;
use App\Models\Pago;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // Esto crea decenas de registros interrelacionados (alumnos, citas,
        // pagos...) que no se pueden volver a insertar sin duplicar datos o
        // tronar por un correo único repetido. Si ya corrió una vez (por
        // ejemplo, en un despliegue anterior), no lo vuelve a intentar.
        if (User::where('email', 'superadmin@aqualix.test')->exists()) {
            $this->command?->info('DemoDataSeeder: ya existen datos de demostración, se omite.');

            return;
        }

        $sucursales = Sucursal::all();
        $niveles = Nivel::ordenados()->get();

        User::factory()->superAdmin()->create([
            'name' => 'Super Administrador',
            'email' => 'superadmin@aqualix.test',
        ]);

        $carriles = new Collection();
        $instructores = new Collection();

        foreach ($sucursales as $sucursal) {
            User::factory()->admin($sucursal->id)->create([
                'name' => "Administrador {$sucursal->nombre}",
                'email' => 'admin.' . strtolower($sucursal->codigo) . '@aqualix.test',
            ]);

            foreach (range(1, 4) as $i) {
                $carriles->push(Carril::factory()->create([
                    'sucursal_id' => $sucursal->id,
                    'nombre' => "Carril {$i}",
                ]));
            }

            foreach (range(1, 2) as $i) {
                $instructores->push(Instructor::factory()->create([
                    'sucursal_id' => $sucursal->id,
                ]));
            }
        }

        $horarios = new Collection();

        foreach ($sucursales as $sucursal) {
            $carrilesSucursal = $carriles->where('sucursal_id', $sucursal->id);
            $instructoresSucursal = $instructores->where('sucursal_id', $sucursal->id);

            foreach (range(1, 6) as $i) {
                $horarios->push(Horario::factory()->create([
                    'sucursal_id' => $sucursal->id,
                    'nivel_id' => $niveles->random()->id,
                    'instructor_id' => $instructoresSucursal->random()->id,
                    'carril_id' => $carrilesSucursal->random()->id,
                ]));
            }
        }

        foreach ($sucursales as $sucursal) {
            $horariosSucursal = $horarios->where('sucursal_id', $sucursal->id);

            Alumno::factory()
                ->count(15)
                ->create(['sucursal_id' => $sucursal->id])
                ->each(function (Alumno $alumno) use ($horariosSucursal, $sucursal) {
                    $horario = $horariosSucursal->random();

                    Inscripcion::factory()->create([
                        'horario_id' => $horario->id,
                        'alumno_id' => $alumno->id,
                    ]);

                    foreach ([14, 7, 0] as $offsetDias) {
                        Cita::factory()->create([
                            'horario_id' => $horario->id,
                            'alumno_id' => $alumno->id,
                            'sucursal_id' => $sucursal->id,
                            'fecha' => now()->subDays($offsetDias)->toDateString(),
                            'estado' => $offsetDias === 0 ? 'programada' : 'completada',
                            'asistio' => $offsetDias === 0 ? null : fake()->boolean(85),
                        ]);
                    }

                    Pago::factory()->create([
                        'alumno_id' => $alumno->id,
                        'sucursal_id' => $sucursal->id,
                    ]);

                    if ($alumno->nivel_id) {
                        $evaluacion = Evaluacion::factory()->create([
                            'alumno_id' => $alumno->id,
                            'instructor_id' => $horario->instructor_id,
                            'nivel_id' => $alumno->nivel_id,
                        ]);

                        CriterioEvaluacion::where('nivel_id', $alumno->nivel_id)->get()
                            ->each(function (CriterioEvaluacion $criterio) use ($evaluacion) {
                                EvaluacionDetalle::factory()->create([
                                    'evaluacion_id' => $evaluacion->id,
                                    'criterio_evaluacion_id' => $criterio->id,
                                ]);
                            });
                    }
                });
        }
    }
}
