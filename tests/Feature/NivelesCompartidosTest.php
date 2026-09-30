<?php

namespace Tests\Feature;

use App\Enums\EstadoEvaluacionDetalle;
use App\Models\Alumno;
use App\Models\AlumnoNivelHistorial;
use App\Models\CriterioEvaluacion;
use App\Models\Evaluacion;
use App\Models\EvaluacionDetalle;
use App\Models\Horario;
use App\Models\Nivel;
use Database\Seeders\NivelSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class NivelesCompartidosTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_seeder_crea_una_sola_lista_compartida_para_ninos_y_adultos(): void
    {
        $nivelPersonalizado = Nivel::factory()->create([
            'nombre' => 'Adulto Iniciación personalizado',
            'orden' => 1,
            'categoria_edad' => 'Adultos',
        ]);
        $seeder = new NivelSeeder;

        $seeder->run();
        $seeder->run();

        $this->assertSame('Adulto Iniciación personalizado', $nivelPersonalizado->fresh()->nombre);
        $this->assertSame(17, Nivel::count());
        $this->assertSame(['Adultos', 'Niños'], Nivel::query()->distinct()->orderBy('categoria_edad')->pluck('categoria_edad')->all());
        $this->assertSame(14, Nivel::where('categoria_edad', 'Niños')->count());
        $this->assertSame(3, Nivel::where('categoria_edad', 'Adultos')->count());
    }

    public function test_migracion_unifica_niveles_y_conserva_referencias_y_evaluaciones(): void
    {
        $nivelNinos = Nivel::factory()->create([
            'nombre' => 'Estrella compartida',
            'orden' => 1,
            'categoria_edad' => 'Niños',
        ]);
        $nivelNinas = Nivel::factory()->create([
            'nombre' => 'Estrella compartida',
            'orden' => 1,
            'categoria_edad' => 'Niñas',
        ]);
        $nivelAdultas = Nivel::factory()->create([
            'nombre' => 'Adulto compartido',
            'orden' => 1,
            'categoria_edad' => 'Adultos mujeres',
        ]);
        $nivelAdultos = Nivel::factory()->create([
            'nombre' => 'Adulto compartido',
            'orden' => 1,
            'categoria_edad' => 'Adultos hombres',
        ]);

        $alumno = Alumno::factory()->create(['nivel_id' => $nivelNinas->id]);
        $horario = Horario::factory()->create(['nivel_id' => $nivelNinas->id]);
        $historial = AlumnoNivelHistorial::create([
            'alumno_id' => $alumno->id,
            'nivel_id' => $nivelNinas->id,
            'fecha_inicio' => now()->subMonth()->toDateString(),
            'fecha_fin' => now()->toDateString(),
        ]);

        $criterioNinos = CriterioEvaluacion::factory()->create([
            'nivel_id' => $nivelNinos->id,
            'nombre' => 'Flotación',
            'orden' => 1,
        ]);
        $criterioNinas = CriterioEvaluacion::factory()->create([
            'nivel_id' => $nivelNinas->id,
            'nombre' => 'Flotación',
            'orden' => 1,
        ]);
        $evaluacion = Evaluacion::factory()->create([
            'alumno_id' => $alumno->id,
            'nivel_id' => $nivelNinas->id,
        ]);
        EvaluacionDetalle::factory()->create([
            'evaluacion_id' => $evaluacion->id,
            'criterio_evaluacion_id' => $criterioNinos->id,
            'estado' => EstadoEvaluacionDetalle::NoIniciado->value,
            'observaciones' => 'Observación original',
        ]);
        EvaluacionDetalle::factory()->create([
            'evaluacion_id' => $evaluacion->id,
            'criterio_evaluacion_id' => $criterioNinas->id,
            'estado' => EstadoEvaluacionDetalle::Logrado->value,
            'observaciones' => 'Observación complementaria',
        ]);

        (require database_path('migrations/2026_09_30_090000_unify_gendered_levels.php'))->up();

        $this->assertDatabaseMissing('niveles', ['id' => $nivelNinas->id]);
        $this->assertDatabaseMissing('niveles', ['id' => $nivelAdultos->id]);
        $this->assertDatabaseHas('niveles', ['id' => $nivelNinos->id, 'categoria_edad' => 'Niños']);
        $this->assertDatabaseHas('niveles', ['id' => $nivelAdultas->id, 'categoria_edad' => 'Adultos']);
        $this->assertSame($nivelNinos->id, $alumno->fresh()->nivel_id);
        $this->assertSame($nivelNinos->id, $horario->fresh()->nivel_id);
        $this->assertSame($nivelNinos->id, $evaluacion->fresh()->nivel_id);
        $this->assertSame($nivelNinos->id, $historial->fresh()->nivel_id);

        $detalle = EvaluacionDetalle::where('evaluacion_id', $evaluacion->id)->firstOrFail();
        $this->assertSame($criterioNinos->id, $detalle->criterio_evaluacion_id);
        $this->assertSame(EstadoEvaluacionDetalle::Logrado, $detalle->estado);
        $this->assertSame("Observación original\nObservación complementaria", $detalle->observaciones);
        $this->assertSame(1, DB::table('criterios_evaluacion')->where('nivel_id', $nivelNinos->id)->count());
    }
}
