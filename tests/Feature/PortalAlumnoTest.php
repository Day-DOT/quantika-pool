<?php

namespace Tests\Feature;

use App\Enums\EstadoEvaluacionDetalle;
use App\Enums\EstadoPago;
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
use App\Models\Plan;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalAlumnoTest extends TestCase
{
    use RefreshDatabase;

    private function crearTutorConAlumno(?Sucursal $sucursal = null, ?Nivel $nivel = null): array
    {
        $sucursal ??= Sucursal::factory()->create();
        $nivel ??= Nivel::factory()->create();

        $tutor = User::factory()->tutor()->create();

        $alumno = Alumno::factory()->create([
            'tutor_user_id' => $tutor->id,
            'sucursal_id' => $sucursal->id,
            'nivel_id' => $nivel->id,
        ]);

        return [$tutor, $alumno, $sucursal, $nivel];
    }

    // ------------------------------------------------------------------
    // DASHBOARD
    // ------------------------------------------------------------------

    public function test_dashboard_muestra_directo_el_resumen_cuando_hay_un_solo_alumno(): void
    {
        [$tutor, $alumno] = $this->crearTutorConAlumno();

        $this->actingAs($tutor)
            ->get(route('portal.dashboard'))
            ->assertOk()
            ->assertSee($alumno->nombreCompleto())
            ->assertSee($alumno->nivel->nombre)
            ->assertDontSee('Mis alumnos');
    }

    public function test_dashboard_muestra_selector_cuando_el_tutor_tiene_varios_alumnos(): void
    {
        $sucursal = Sucursal::factory()->create();
        $tutor = User::factory()->tutor()->create();

        $primero = Alumno::factory()->create(['tutor_user_id' => $tutor->id, 'sucursal_id' => $sucursal->id]);
        $segundo = Alumno::factory()->create(['tutor_user_id' => $tutor->id, 'sucursal_id' => $sucursal->id]);

        $this->actingAs($tutor)
            ->get(route('portal.dashboard'))
            ->assertOk()
            ->assertSee('Mis alumnos')
            ->assertSee($primero->nombreCompleto())
            ->assertSee($segundo->nombreCompleto());
    }

    public function test_un_tutor_no_puede_ver_el_resumen_de_un_alumno_ajeno(): void
    {
        [$tutorA, $alumnoA] = $this->crearTutorConAlumno();
        [$tutorB, $alumnoB] = $this->crearTutorConAlumno();

        // Intenta forzar el id del alumno de otro tutor por query string:
        // el resolver debe ignorarlo y quedarse con el alumno propio.
        $this->actingAs($tutorA)
            ->get(route('portal.dashboard', ['alumno' => $alumnoB->id]))
            ->assertOk()
            ->assertSee($alumnoA->nombreCompleto())
            ->assertDontSee($alumnoB->nombreCompleto());
    }

    public function test_guest_es_redirigido_a_login(): void
    {
        $this->get('/portal')->assertRedirect('/login');
    }

    public function test_un_instructor_no_puede_entrar_al_portal_de_alumnos(): void
    {
        $instructor = User::factory()->instructor()->create();

        $this->actingAs($instructor)->get('/portal')->assertForbidden();
    }

    // ------------------------------------------------------------------
    // RESERVA DE CLASES
    // ------------------------------------------------------------------

    private function crearHorario(Sucursal $sucursal, Nivel $nivel, int $capacidad = 4, int $diaSemana = 2): Horario
    {
        $instructor = Instructor::factory()->create(['sucursal_id' => $sucursal->id]);
        $carril = Carril::factory()->create(['sucursal_id' => $sucursal->id]);

        return Horario::factory()->create([
            'sucursal_id' => $sucursal->id,
            'nivel_id' => $nivel->id,
            'instructor_id' => $instructor->id,
            'carril_id' => $carril->id,
            'capacidad_maxima' => $capacidad,
            'dia_semana' => $diaSemana,
            'activo' => true,
        ]);
    }

    public function test_reservar_index_solo_muestra_horarios_de_la_sucursal_y_niveles_cercanos(): void
    {
        [$tutor, $alumno, $sucursal, $nivel] = $this->crearTutorConAlumno();
        $alumno->update(['plan_id' => Plan::factory()->create(['clases_por_semana' => 2])->id]);

        $horarioCercano = $this->crearHorario($sucursal, $nivel);
        $horarioCercano->update(['nombre_grupo' => 'Grupo Cercano']);

        $nivelLejano = Nivel::factory()->create(['orden' => $nivel->orden + 5]);
        $horarioLejano = $this->crearHorario($sucursal, $nivelLejano);
        $horarioLejano->update(['nombre_grupo' => 'Grupo Lejano']);

        $otraSucursal = Sucursal::factory()->create();
        $horarioOtraSucursal = $this->crearHorario($otraSucursal, $nivel);
        $horarioOtraSucursal->update(['nombre_grupo' => 'Grupo Otra Sucursal']);

        $this->actingAs($tutor)
            ->get(route('portal.reservar.index', ['alumno' => $alumno->id, 'sucursal' => $sucursal->id]))
            ->assertOk()
            ->assertSee('Grupo Cercano')
            ->assertDontSee('Grupo Lejano')
            ->assertDontSee('Grupo Otra Sucursal');
    }

    public function test_reservar_clase_crea_inscripcion_pendiente_sin_ocupar_cupo_ni_citas(): void
    {
        [$tutor, $alumno, $sucursal, $nivel] = $this->crearTutorConAlumno();
        $alumno->update(['plan_id' => Plan::factory()->create(['clases_por_semana' => 2])->id]);
        $horario = $this->crearHorario($sucursal, $nivel, capacidad: 4);

        $this->actingAs($tutor)
            ->post(route('portal.reservar.store'), [
                'alumno_id' => $alumno->id,
                'horario_ids' => [$horario->id],
            ])
            ->assertRedirect(route('portal.reservar.index', ['alumno' => $alumno->id, 'sucursal' => $sucursal->id]));

        $this->assertDatabaseHas('inscripciones', [
            'horario_id' => $horario->id,
            'alumno_id' => $alumno->id,
            'activa' => 0,
            'estado' => 'pendiente',
        ]);

        $this->assertSame(
            0,
            Cita::where('horario_id', $horario->id)->where('alumno_id', $alumno->id)->count()
        );
    }

    public function test_reservar_varias_clases_a_la_vez_hasta_el_limite_del_plan(): void
    {
        [$tutor, $alumno, $sucursal, $nivel] = $this->crearTutorConAlumno();
        $alumno->update(['plan_id' => Plan::factory()->create(['clases_por_semana' => 3])->id]);

        $horarioA = $this->crearHorario($sucursal, $nivel, capacidad: 4, diaSemana: 1);
        $horarioB = $this->crearHorario($sucursal, $nivel, capacidad: 4, diaSemana: 3);
        $horarioC = $this->crearHorario($sucursal, $nivel, capacidad: 4, diaSemana: 5);

        $this->actingAs($tutor)
            ->post(route('portal.reservar.store'), [
                'alumno_id' => $alumno->id,
                'horario_ids' => [$horarioA->id, $horarioB->id, $horarioC->id],
            ])
            ->assertSessionDoesntHaveErrors();

        $this->assertSame(
            3,
            Inscripcion::where('alumno_id', $alumno->id)->where('estado', 'pendiente')->count()
        );
    }

    public function test_no_se_puede_reservar_mas_clases_que_las_del_plan(): void
    {
        [$tutor, $alumno, $sucursal, $nivel] = $this->crearTutorConAlumno();
        $alumno->update(['plan_id' => Plan::factory()->create(['clases_por_semana' => 2])->id]);

        $horarioA = $this->crearHorario($sucursal, $nivel, capacidad: 4, diaSemana: 1);
        $horarioB = $this->crearHorario($sucursal, $nivel, capacidad: 4, diaSemana: 3);
        $horarioC = $this->crearHorario($sucursal, $nivel, capacidad: 4, diaSemana: 5);

        $this->actingAs($tutor)
            ->post(route('portal.reservar.store'), [
                'alumno_id' => $alumno->id,
                'horario_ids' => [$horarioA->id, $horarioB->id, $horarioC->id],
            ])
            ->assertSessionHasErrors('horario_ids');

        $this->assertSame(0, Inscripcion::where('alumno_id', $alumno->id)->count());
    }

    public function test_no_se_puede_reservar_sin_un_plan_asignado(): void
    {
        [$tutor, $alumno, $sucursal, $nivel] = $this->crearTutorConAlumno();
        $horario = $this->crearHorario($sucursal, $nivel, capacidad: 4);

        $this->actingAs($tutor)
            ->post(route('portal.reservar.store'), [
                'alumno_id' => $alumno->id,
                'horario_ids' => [$horario->id],
            ])
            ->assertSessionHasErrors('plan');

        $this->assertSame(0, Inscripcion::where('alumno_id', $alumno->id)->count());
    }

    public function test_no_se_puede_reservar_cuando_el_horario_ya_no_tiene_cupo(): void
    {
        [$tutor, $alumno, $sucursal, $nivel] = $this->crearTutorConAlumno();
        $alumno->update(['plan_id' => Plan::factory()->create(['clases_por_semana' => 2])->id]);
        $horario = $this->crearHorario($sucursal, $nivel, capacidad: 1);

        // Alguien más ya ocupa el único lugar disponible.
        $otroAlumno = Alumno::factory()->create(['sucursal_id' => $sucursal->id]);
        Inscripcion::factory()->create([
            'horario_id' => $horario->id,
            'alumno_id' => $otroAlumno->id,
            'activa' => true,
        ]);

        $this->actingAs($tutor)
            ->post(route('portal.reservar.store'), [
                'alumno_id' => $alumno->id,
                'horario_ids' => [$horario->id],
            ])
            ->assertSessionHasErrors('horario_ids');

        $this->assertDatabaseMissing('inscripciones', [
            'horario_id' => $horario->id,
            'alumno_id' => $alumno->id,
        ]);
    }

    public function test_no_se_puede_reservar_dos_veces_el_mismo_grupo_para_el_mismo_alumno(): void
    {
        [$tutor, $alumno, $sucursal, $nivel] = $this->crearTutorConAlumno();
        $alumno->update(['plan_id' => Plan::factory()->create(['clases_por_semana' => 2])->id]);
        $horario = $this->crearHorario($sucursal, $nivel, capacidad: 4);

        Inscripcion::factory()->create([
            'horario_id' => $horario->id,
            'alumno_id' => $alumno->id,
            'activa' => true,
        ]);

        $this->actingAs($tutor)
            ->post(route('portal.reservar.store'), [
                'alumno_id' => $alumno->id,
                'horario_ids' => [$horario->id],
            ])
            ->assertSessionHasErrors('horario_ids');

        $this->assertSame(
            1,
            Inscripcion::where('horario_id', $horario->id)->where('alumno_id', $alumno->id)->count()
        );
    }

    public function test_un_tutor_no_puede_reservar_una_clase_para_un_alumno_ajeno(): void
    {
        [$tutorA] = $this->crearTutorConAlumno();
        [$tutorB, $alumnoB, $sucursal, $nivel] = $this->crearTutorConAlumno();
        $horario = $this->crearHorario($sucursal, $nivel, capacidad: 4);

        $this->actingAs($tutorA)
            ->post(route('portal.reservar.store'), [
                'alumno_id' => $alumnoB->id,
                'horario_ids' => [$horario->id],
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('inscripciones', [
            'horario_id' => $horario->id,
            'alumno_id' => $alumnoB->id,
        ]);
    }

    // ------------------------------------------------------------------
    // PROGRESO / EVALUACIÓN
    // ------------------------------------------------------------------

    public function test_progreso_muestra_la_boleta_con_criterios_y_porcentaje_de_avance(): void
    {
        [$tutor, $alumno, , $nivel] = $this->crearTutorConAlumno();

        $criterioLogrado = CriterioEvaluacion::factory()->create(['nivel_id' => $nivel->id, 'nombre' => 'Flotación', 'orden' => 1]);
        $criterioPendiente = CriterioEvaluacion::factory()->create(['nivel_id' => $nivel->id, 'nombre' => 'Respiración', 'orden' => 2]);

        $instructor = Instructor::factory()->create();
        $evaluacion = Evaluacion::factory()->create([
            'alumno_id' => $alumno->id,
            'instructor_id' => $instructor->id,
            'nivel_id' => $nivel->id,
            'observaciones' => 'Muy buen avance general.',
        ]);

        EvaluacionDetalle::factory()->create([
            'evaluacion_id' => $evaluacion->id,
            'criterio_evaluacion_id' => $criterioLogrado->id,
            'estado' => EstadoEvaluacionDetalle::Logrado->value,
            'observaciones' => 'Excelente flotación dorsal.',
        ]);

        EvaluacionDetalle::factory()->create([
            'evaluacion_id' => $evaluacion->id,
            'criterio_evaluacion_id' => $criterioPendiente->id,
            'estado' => EstadoEvaluacionDetalle::NoIniciado->value,
        ]);

        $this->actingAs($tutor)
            ->get(route('portal.progreso', ['alumno' => $alumno->id]))
            ->assertOk()
            ->assertSee('Flotación')
            ->assertSee('Respiración')
            ->assertSee('Excelente flotación dorsal.')
            ->assertSee('Muy buen avance general.')
            ->assertSee('50%');
    }

    public function test_progreso_sin_evaluaciones_muestra_todos_los_criterios_como_no_iniciado(): void
    {
        [$tutor, $alumno, , $nivel] = $this->crearTutorConAlumno();

        CriterioEvaluacion::factory()->create(['nivel_id' => $nivel->id, 'nombre' => 'Coordinación']);

        $this->actingAs($tutor)
            ->get(route('portal.progreso', ['alumno' => $alumno->id]))
            ->assertOk()
            ->assertSee('Coordinación')
            ->assertSee('No iniciado')
            ->assertSee('0%');
    }

    public function test_progreso_muestra_el_historial_por_niveles_y_la_comparativa(): void
    {
        $nivelAnterior = Nivel::factory()->create(['orden' => 10, 'nombre' => 'Nivel Anterior']);
        $nivelActual = Nivel::factory()->create(['orden' => 11, 'nombre' => 'Nivel Actual']);

        [$tutor, $alumno] = $this->crearTutorConAlumno(nivel: $nivelActual);

        $criterioAnterior = CriterioEvaluacion::factory()->create(['nivel_id' => $nivelAnterior->id]);
        $criterioActual = CriterioEvaluacion::factory()->create(['nivel_id' => $nivelActual->id]);

        $instructor = Instructor::factory()->create();

        \App\Models\AlumnoNivelHistorial::create([
            'alumno_id' => $alumno->id,
            'nivel_id' => $nivelAnterior->id,
            'fecha_inicio' => now()->subMonths(2)->toDateString(),
            'fecha_fin' => now()->subMonth()->toDateString(),
        ]);

        \App\Models\AlumnoNivelHistorial::create([
            'alumno_id' => $alumno->id,
            'nivel_id' => $nivelActual->id,
            'fecha_inicio' => now()->subMonth()->toDateString(),
            'fecha_fin' => null,
        ]);

        $evaluacionAnterior = Evaluacion::factory()->create([
            'alumno_id' => $alumno->id,
            'instructor_id' => $instructor->id,
            'nivel_id' => $nivelAnterior->id,
            'fecha' => now()->subMonth()->toDateString(),
        ]);
        EvaluacionDetalle::factory()->create([
            'evaluacion_id' => $evaluacionAnterior->id,
            'criterio_evaluacion_id' => $criterioAnterior->id,
            'estado' => EstadoEvaluacionDetalle::Logrado->value,
        ]);

        $evaluacionActual = Evaluacion::factory()->create([
            'alumno_id' => $alumno->id,
            'instructor_id' => $instructor->id,
            'nivel_id' => $nivelActual->id,
            'fecha' => now()->toDateString(),
        ]);
        EvaluacionDetalle::factory()->create([
            'evaluacion_id' => $evaluacionActual->id,
            'criterio_evaluacion_id' => $criterioActual->id,
            'estado' => EstadoEvaluacionDetalle::NoIniciado->value,
        ]);

        $this->actingAs($tutor)
            ->get(route('portal.progreso', ['alumno' => $alumno->id]))
            ->assertOk()
            ->assertSeeInOrder(['Historial de progreso por niveles', 'Nivel Anterior'])
            ->assertSee('Comparativa de progreso')
            ->assertSee('Progreso anterior')
            ->assertSee('Progreso actual');
    }

    // ------------------------------------------------------------------
    // ESTADO DE CUENTA Y CLASES
    // ------------------------------------------------------------------

    public function test_cuenta_muestra_las_proximas_clases_y_el_historial_de_pagos(): void
    {
        [$tutor, $alumno, $sucursal, $nivel] = $this->crearTutorConAlumno();
        $horario = $this->crearHorario($sucursal, $nivel);
        $horario->update(['nombre_grupo' => 'Delfines Matutino']);

        Cita::factory()->create([
            'horario_id' => $horario->id,
            'alumno_id' => $alumno->id,
            'sucursal_id' => $sucursal->id,
            'fecha' => now()->addDays(3)->toDateString(),
            'estado' => 'programada',
        ]);

        Pago::factory()->create([
            'alumno_id' => $alumno->id,
            'sucursal_id' => $sucursal->id,
            'estado' => EstadoPago::Vencido->value,
            'monto' => 550,
        ]);

        $this->actingAs($tutor)
            ->get(route('portal.cuenta', ['alumno' => $alumno->id]))
            ->assertOk()
            ->assertSee('Delfines Matutino')
            ->assertSee('Vencido')
            ->assertSee('550.00');
    }

    public function test_cuenta_no_muestra_boton_de_pago_en_linea(): void
    {
        [$tutor, $alumno] = $this->crearTutorConAlumno();

        $this->actingAs($tutor)
            ->get(route('portal.cuenta', ['alumno' => $alumno->id]))
            ->assertOk()
            ->assertSee('Este portal es solo de consulta');
    }
}
