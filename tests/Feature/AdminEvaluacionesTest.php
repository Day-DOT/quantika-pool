<?php

namespace Tests\Feature;

use App\Models\Alumno;
use App\Models\Carril;
use App\Models\CriterioEvaluacion;
use App\Models\Evaluacion;
use App\Models\EvaluacionDetalle;
use App\Models\Horario;
use App\Models\Inscripcion;
use App\Models\Instructor;
use App\Models\Nivel;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminEvaluacionesTest extends TestCase
{
    use RefreshDatabase;

    private function crearEscenario(): array
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();
        $instructor = Instructor::factory()->create(['sucursal_id' => $sucursal->id]);
        $nivel = Nivel::factory()->create();
        $carril = Carril::factory()->create(['sucursal_id' => $sucursal->id]);

        $horario = Horario::factory()->create([
            'sucursal_id' => $sucursal->id,
            'instructor_id' => $instructor->id,
            'nivel_id' => $nivel->id,
            'carril_id' => $carril->id,
        ]);

        $alumno = Alumno::factory()->create(['sucursal_id' => $sucursal->id, 'nivel_id' => $nivel->id]);

        Inscripcion::factory()->create([
            'horario_id' => $horario->id,
            'alumno_id' => $alumno->id,
            'activa' => true,
        ]);

        $criterio = CriterioEvaluacion::factory()->create(['nivel_id' => $nivel->id]);

        $evaluacion = Evaluacion::factory()->create([
            'alumno_id' => $alumno->id,
            'instructor_id' => $instructor->id,
            'nivel_id' => $nivel->id,
        ]);

        EvaluacionDetalle::factory()->create([
            'evaluacion_id' => $evaluacion->id,
            'criterio_evaluacion_id' => $criterio->id,
            'estado' => 'logrado',
        ]);

        return compact('admin', 'instructor', 'alumno', 'evaluacion');
    }

    public function test_admin_ve_el_listado_de_instructores_con_conteos(): void
    {
        $escenario = $this->crearEscenario();

        $response = $this->actingAs($escenario['admin'])->get(route('evaluaciones.index'));

        $response->assertOk();
        $response->assertSee($escenario['instructor']->user->name);
    }

    public function test_admin_ve_los_alumnos_de_un_instructor(): void
    {
        $escenario = $this->crearEscenario();

        $response = $this->actingAs($escenario['admin'])->get(route('evaluaciones.instructor', $escenario['instructor']));

        $response->assertOk();
        $response->assertSee($escenario['alumno']->nombreCompleto());
    }

    public function test_admin_ve_el_detalle_de_evaluaciones_de_un_alumno(): void
    {
        $escenario = $this->crearEscenario();

        $response = $this->actingAs($escenario['admin'])->get(route('evaluaciones.alumno', $escenario['alumno']));

        $response->assertOk();
    }
}
