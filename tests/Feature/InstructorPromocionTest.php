<?php

namespace Tests\Feature;

use App\Enums\EstadoEvaluacionDetalle;
use App\Models\Alumno;
use App\Models\AlumnoNivelHistorial;
use App\Models\CriterioEvaluacion;
use App\Models\Evaluacion;
use App\Models\EvaluacionDetalle;
use App\Models\Horario;
use App\Models\Inscripcion;
use App\Models\Instructor;
use App\Models\Nivel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstructorPromocionTest extends TestCase
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

    private function evaluacionCompleta(Alumno $alumno, Instructor $instructor, Nivel $nivel, CriterioEvaluacion $criterio): Evaluacion
    {
        $evaluacion = Evaluacion::factory()->create([
            'alumno_id' => $alumno->id,
            'instructor_id' => $instructor->id,
            'nivel_id' => $nivel->id,
        ]);

        EvaluacionDetalle::factory()->create([
            'evaluacion_id' => $evaluacion->id,
            'criterio_evaluacion_id' => $criterio->id,
            'estado' => EstadoEvaluacionDetalle::Logrado->value,
        ]);

        return $evaluacion;
    }

    public function test_instructor_promueve_a_un_alumno_con_evaluacion_completa(): void
    {
        $nivelActual = Nivel::factory()->create(['orden' => 5]);
        $nivelSiguiente = Nivel::factory()->create(['orden' => 6]);
        $criterio = CriterioEvaluacion::factory()->create(['nivel_id' => $nivelActual->id]);

        $instructor = Instructor::factory()->create();
        $alumno = $this->crearAlumnoDelInstructor($instructor, $nivelActual);
        $this->evaluacionCompleta($alumno, $instructor, $nivelActual, $criterio);

        $response = $this->actingAs($instructor->user)->patch(route('instructor.alumnos.promover', $alumno));

        $response->assertRedirect(route('instructor.alumnos.show', $alumno));
        $response->assertSessionHas('status');

        $alumno->refresh();
        $this->assertEquals($nivelSiguiente->id, $alumno->nivel_id);

        $this->assertDatabaseHas('alumno_nivel_historial', [
            'alumno_id' => $alumno->id,
            'nivel_id' => $nivelSiguiente->id,
            'fecha_fin' => null,
            'promovido_por' => $instructor->user_id,
        ]);
    }

    public function test_instructor_promueve_a_un_alumno_con_evaluacion_al_80_por_ciento(): void
    {
        $nivelActual = Nivel::factory()->create(['orden' => 5]);
        $nivelSiguiente = Nivel::factory()->create(['orden' => 6]);
        $criterios = CriterioEvaluacion::factory()->count(5)->create(['nivel_id' => $nivelActual->id]);

        $instructor = Instructor::factory()->create();
        $alumno = $this->crearAlumnoDelInstructor($instructor, $nivelActual);

        $evaluacion = Evaluacion::factory()->create([
            'alumno_id' => $alumno->id,
            'instructor_id' => $instructor->id,
            'nivel_id' => $nivelActual->id,
        ]);

        // 4 de 5 criterios logrados = 80%, justo el mínimo requerido.
        foreach ($criterios as $index => $criterio) {
            EvaluacionDetalle::factory()->create([
                'evaluacion_id' => $evaluacion->id,
                'criterio_evaluacion_id' => $criterio->id,
                'estado' => $index < 4 ? EstadoEvaluacionDetalle::Logrado->value : EstadoEvaluacionDetalle::EnProceso->value,
            ]);
        }

        $response = $this->actingAs($instructor->user)->patch(route('instructor.alumnos.promover', $alumno));

        $response->assertRedirect(route('instructor.alumnos.show', $alumno));
        $this->assertEquals($nivelSiguiente->id, $alumno->refresh()->nivel_id);
    }

    public function test_no_se_puede_promover_con_una_evaluacion_por_debajo_del_80_por_ciento(): void
    {
        $nivelActual = Nivel::factory()->create(['orden' => 5]);
        Nivel::factory()->create(['orden' => 6]);
        $criterios = CriterioEvaluacion::factory()->count(5)->create(['nivel_id' => $nivelActual->id]);

        $instructor = Instructor::factory()->create();
        $alumno = $this->crearAlumnoDelInstructor($instructor, $nivelActual);

        $evaluacion = Evaluacion::factory()->create([
            'alumno_id' => $alumno->id,
            'instructor_id' => $instructor->id,
            'nivel_id' => $nivelActual->id,
        ]);

        // 3 de 5 criterios logrados = 60%, por debajo del mínimo requerido.
        foreach ($criterios as $index => $criterio) {
            EvaluacionDetalle::factory()->create([
                'evaluacion_id' => $evaluacion->id,
                'criterio_evaluacion_id' => $criterio->id,
                'estado' => $index < 3 ? EstadoEvaluacionDetalle::Logrado->value : EstadoEvaluacionDetalle::EnProceso->value,
            ]);
        }

        $response = $this->actingAs($instructor->user)->patch(route('instructor.alumnos.promover', $alumno));

        $response->assertSessionHasErrors('nivel');
        $this->assertEquals($nivelActual->id, $alumno->refresh()->nivel_id);
    }

    public function test_no_se_puede_promover_sin_una_evaluacion_al_100(): void
    {
        $nivelActual = Nivel::factory()->create(['orden' => 5]);
        Nivel::factory()->create(['orden' => 6]);
        $criterio1 = CriterioEvaluacion::factory()->create(['nivel_id' => $nivelActual->id]);
        $criterio2 = CriterioEvaluacion::factory()->create(['nivel_id' => $nivelActual->id]);

        $instructor = Instructor::factory()->create();
        $alumno = $this->crearAlumnoDelInstructor($instructor, $nivelActual);

        $evaluacion = Evaluacion::factory()->create([
            'alumno_id' => $alumno->id,
            'instructor_id' => $instructor->id,
            'nivel_id' => $nivelActual->id,
        ]);

        EvaluacionDetalle::factory()->create([
            'evaluacion_id' => $evaluacion->id,
            'criterio_evaluacion_id' => $criterio1->id,
            'estado' => EstadoEvaluacionDetalle::Logrado->value,
        ]);

        EvaluacionDetalle::factory()->create([
            'evaluacion_id' => $evaluacion->id,
            'criterio_evaluacion_id' => $criterio2->id,
            'estado' => EstadoEvaluacionDetalle::EnProceso->value,
        ]);

        $response = $this->actingAs($instructor->user)->patch(route('instructor.alumnos.promover', $alumno));

        $response->assertSessionHasErrors('nivel');
        $this->assertEquals($nivelActual->id, $alumno->refresh()->nivel_id);
    }

    public function test_no_se_puede_promover_sin_ninguna_evaluacion(): void
    {
        $nivelActual = Nivel::factory()->create(['orden' => 5]);
        Nivel::factory()->create(['orden' => 6]);

        $instructor = Instructor::factory()->create();
        $alumno = $this->crearAlumnoDelInstructor($instructor, $nivelActual);

        $response = $this->actingAs($instructor->user)->patch(route('instructor.alumnos.promover', $alumno));

        $response->assertSessionHasErrors('nivel');
        $this->assertEquals($nivelActual->id, $alumno->refresh()->nivel_id);
    }

    public function test_no_se_puede_promover_en_el_nivel_maximo(): void
    {
        $nivelMaximo = Nivel::factory()->create(['orden' => 12]);
        $criterio = CriterioEvaluacion::factory()->create(['nivel_id' => $nivelMaximo->id]);

        $instructor = Instructor::factory()->create();
        $alumno = $this->crearAlumnoDelInstructor($instructor, $nivelMaximo);
        $this->evaluacionCompleta($alumno, $instructor, $nivelMaximo, $criterio);

        $response = $this->actingAs($instructor->user)->patch(route('instructor.alumnos.promover', $alumno));

        $response->assertSessionHasErrors('nivel');
        $this->assertEquals($nivelMaximo->id, $alumno->refresh()->nivel_id);
    }

    public function test_instructor_no_puede_promover_a_un_alumno_que_no_es_suyo(): void
    {
        $nivel = Nivel::factory()->create(['orden' => 1]);
        Nivel::factory()->create(['orden' => 2]);

        $instructor = Instructor::factory()->create();
        $alumnoAjeno = Alumno::factory()->create(['nivel_id' => $nivel->id]);

        $this->actingAs($instructor->user)
            ->patch(route('instructor.alumnos.promover', $alumnoAjeno))
            ->assertForbidden();
    }

    public function test_promover_cierra_el_registro_anterior_del_historial_de_niveles(): void
    {
        $nivelActual = Nivel::factory()->create(['orden' => 5]);
        $nivelSiguiente = Nivel::factory()->create(['orden' => 6]);
        $criterio = CriterioEvaluacion::factory()->create(['nivel_id' => $nivelActual->id]);

        $instructor = Instructor::factory()->create();
        $alumno = $this->crearAlumnoDelInstructor($instructor, $nivelActual);
        $this->evaluacionCompleta($alumno, $instructor, $nivelActual, $criterio);

        $historialPrevio = AlumnoNivelHistorial::create([
            'alumno_id' => $alumno->id,
            'nivel_id' => $nivelActual->id,
            'fecha_inicio' => now()->subMonths(2)->toDateString(),
            'fecha_fin' => null,
        ]);

        $this->actingAs($instructor->user)->patch(route('instructor.alumnos.promover', $alumno));

        $this->assertNotNull($historialPrevio->refresh()->fecha_fin);

        $this->assertDatabaseHas('alumno_nivel_historial', [
            'alumno_id' => $alumno->id,
            'nivel_id' => $nivelSiguiente->id,
            'fecha_fin' => null,
        ]);
    }

    public function test_promover_avanza_de_sub_nivel_antes_de_pasar_al_siguiente_nivel_principal(): void
    {
        $nivelActual = Nivel::factory()->create(['orden' => 5, 'total_sub_niveles' => 3]);
        $nivelSiguiente = Nivel::factory()->create(['orden' => 6]);
        $criterio = CriterioEvaluacion::factory()->create(['nivel_id' => $nivelActual->id]);

        $instructor = Instructor::factory()->create();
        $alumno = $this->crearAlumnoDelInstructor($instructor, $nivelActual);
        $this->evaluacionCompleta($alumno, $instructor, $nivelActual, $criterio);

        $this->assertEquals(1, $alumno->sub_nivel);

        // 1ª promoción: de sub-nivel A a B, sin salir del nivel principal.
        $this->actingAs($instructor->user)->patch(route('instructor.alumnos.promover', $alumno));
        $alumno->refresh();
        $this->assertEquals($nivelActual->id, $alumno->nivel_id);
        $this->assertEquals(2, $alumno->sub_nivel);

        // 2ª promoción: de sub-nivel B a C.
        $this->evaluacionCompleta($alumno, $instructor, $nivelActual, $criterio);
        $this->actingAs($instructor->user)->patch(route('instructor.alumnos.promover', $alumno));
        $alumno->refresh();
        $this->assertEquals($nivelActual->id, $alumno->nivel_id);
        $this->assertEquals(3, $alumno->sub_nivel);

        // 3ª promoción: completó el último sub-nivel, ahora sí avanza al
        // siguiente nivel principal y su sub-nivel se reinicia a 1.
        $this->evaluacionCompleta($alumno, $instructor, $nivelActual, $criterio);
        $response = $this->actingAs($instructor->user)->patch(route('instructor.alumnos.promover', $alumno));
        $response->assertSessionHas('status', function ($mensaje) use ($nivelSiguiente) {
            return str_contains($mensaje, $nivelSiguiente->nombre);
        });

        $alumno->refresh();
        $this->assertEquals($nivelSiguiente->id, $alumno->nivel_id);
        $this->assertEquals(1, $alumno->sub_nivel);
    }

    public function test_nombre_con_sub_nivel_se_muestra_con_letra(): void
    {
        $nivel = Nivel::factory()->create(['nombre' => 'Tortuga', 'total_sub_niveles' => 3]);

        $this->assertEquals('Tortuga A', $nivel->nombreConSubNivel(1));
        $this->assertEquals('Tortuga B', $nivel->nombreConSubNivel(2));
        $this->assertEquals('Tortuga C', $nivel->nombreConSubNivel(3));
    }

    public function test_nivel_sin_sub_niveles_no_agrega_letra(): void
    {
        $nivel = Nivel::factory()->create(['nombre' => 'Estrella', 'total_sub_niveles' => 1]);

        $this->assertEquals('Estrella', $nivel->nombreConSubNivel(1));
        $this->assertFalse($nivel->tieneSubNiveles());
    }
}
