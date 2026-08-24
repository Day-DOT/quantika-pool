<?php

namespace Tests\Feature;

use App\Enums\EstadoEvaluacionDetalle;
use App\Models\Alumno;
use App\Models\CriterioEvaluacion;
use App\Models\Evaluacion;
use App\Models\Horario;
use App\Models\Inscripcion;
use App\Models\Instructor;
use App\Models\Nivel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstructorEvaluacionTest extends TestCase
{
    use RefreshDatabase;

    private function crearAlumnoDelInstructor(Instructor $instructor, Nivel $nivel): Alumno
    {
        $horario = Horario::factory()->create([
            'instructor_id' => $instructor->id,
            'nivel_id' => $nivel->id,
        ]);

        $alumno = Alumno::factory()->create(['nivel_id' => $nivel->id]);

        Inscripcion::factory()->create([
            'horario_id' => $horario->id,
            'alumno_id' => $alumno->id,
            'activa' => true,
        ]);

        return $alumno;
    }

    public function test_instructor_crea_una_evaluacion_nueva_para_su_alumno_con_el_estado_de_cada_criterio(): void
    {
        $nivel = Nivel::factory()->create();
        $criterio1 = CriterioEvaluacion::factory()->create(['nivel_id' => $nivel->id, 'orden' => 1]);
        $criterio2 = CriterioEvaluacion::factory()->create(['nivel_id' => $nivel->id, 'orden' => 2]);

        $instructor = Instructor::factory()->create();
        $alumno = $this->crearAlumnoDelInstructor($instructor, $nivel);

        $response = $this->actingAs($instructor->user)->post(route('instructor.evaluaciones.store', $alumno), [
            'observaciones' => 'Buen avance general',
            'detalles' => [
                ['criterio_evaluacion_id' => $criterio1->id, 'estado' => 'logrado', 'observaciones' => 'Excelente'],
                ['criterio_evaluacion_id' => $criterio2->id, 'estado' => 'en_proceso'],
            ],
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('evaluaciones', [
            'alumno_id' => $alumno->id,
            'instructor_id' => $instructor->id,
            'nivel_id' => $nivel->id,
            'observaciones' => 'Buen avance general',
        ]);

        $evaluacion = Evaluacion::where('alumno_id', $alumno->id)->first();

        $this->assertDatabaseHas('evaluacion_detalles', [
            'evaluacion_id' => $evaluacion->id,
            'criterio_evaluacion_id' => $criterio1->id,
            'estado' => EstadoEvaluacionDetalle::Logrado->value,
        ]);

        $this->assertEquals(50.0, $evaluacion->porcentajeAvance());
    }

    public function test_al_evaluar_de_nuevo_se_continua_la_evaluacion_existente_del_mismo_nivel(): void
    {
        $nivel = Nivel::factory()->create();
        CriterioEvaluacion::factory()->create(['nivel_id' => $nivel->id]);

        $instructor = Instructor::factory()->create();
        $alumno = $this->crearAlumnoDelInstructor($instructor, $nivel);

        $evaluacionExistente = Evaluacion::factory()->create([
            'alumno_id' => $alumno->id,
            'instructor_id' => $instructor->id,
            'nivel_id' => $nivel->id,
        ]);

        $response = $this->actingAs($instructor->user)->get(route('instructor.evaluaciones.create', $alumno));

        $response->assertRedirect(route('instructor.evaluaciones.edit', $evaluacionExistente));
    }

    public function test_instructor_actualiza_los_criterios_de_una_evaluacion_existente(): void
    {
        $nivel = Nivel::factory()->create();
        $criterio = CriterioEvaluacion::factory()->create(['nivel_id' => $nivel->id]);

        $instructor = Instructor::factory()->create();
        $alumno = $this->crearAlumnoDelInstructor($instructor, $nivel);

        $evaluacion = Evaluacion::factory()->create([
            'alumno_id' => $alumno->id,
            'instructor_id' => $instructor->id,
            'nivel_id' => $nivel->id,
        ]);

        $response = $this->actingAs($instructor->user)->put(route('instructor.evaluaciones.update', $evaluacion), [
            'observaciones' => 'Actualizado',
            'detalles' => [
                ['criterio_evaluacion_id' => $criterio->id, 'estado' => 'logrado'],
            ],
        ]);

        $response->assertRedirect(route('instructor.evaluaciones.edit', $evaluacion));

        $this->assertDatabaseHas('evaluacion_detalles', [
            'evaluacion_id' => $evaluacion->id,
            'criterio_evaluacion_id' => $criterio->id,
            'estado' => 'logrado',
        ]);
    }

    public function test_instructor_no_puede_evaluar_a_un_alumno_que_no_es_suyo(): void
    {
        $instructor = Instructor::factory()->create();
        $alumnoAjeno = Alumno::factory()->create();

        $this->actingAs($instructor->user)->get(route('instructor.evaluaciones.create', $alumnoAjeno))->assertForbidden();
    }

    public function test_instructor_no_puede_editar_la_evaluacion_de_otro_instructor(): void
    {
        $nivel = Nivel::factory()->create();
        $otroInstructor = Instructor::factory()->create();
        $alumno = Alumno::factory()->create(['nivel_id' => $nivel->id]);

        $evaluacion = Evaluacion::factory()->create([
            'alumno_id' => $alumno->id,
            'instructor_id' => $otroInstructor->id,
            'nivel_id' => $nivel->id,
        ]);

        $miInstructor = Instructor::factory()->create();

        $this->actingAs($miInstructor->user)->get(route('instructor.evaluaciones.edit', $evaluacion))->assertForbidden();
    }
}
