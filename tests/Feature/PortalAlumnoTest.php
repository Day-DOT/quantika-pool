<?php

namespace Tests\Feature;

use App\Enums\EstadoEvaluacionDetalle;
use App\Enums\EstadoPago;
use App\Models\Alumno;
use App\Models\AlumnoNivelHistorial;
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
use Illuminate\Http\UploadedFile;
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

    public function test_dashboard_muestra_alumno_con_correo_del_alumno_si_no_tiene_tutor_vinculado(): void
    {
        $tutor = User::factory()->tutor()->create(['email' => 'dayanna@example.com']);
        $alumno = Alumno::factory()->create([
            'tutor_user_id' => null,
            'email' => $tutor->email,
        ]);

        $this->actingAs($tutor)
            ->get(route('portal.dashboard'))
            ->assertOk()
            ->assertSee($alumno->nombreCompleto());
    }

    public function test_tutor_no_puede_modificar_la_foto_del_alumno(): void
    {
        [$tutor, $alumno] = $this->crearTutorConAlumno();

        $this->actingAs($tutor)
            ->post('/portal/foto', [
                'foto' => UploadedFile::fake()->image('alumno.jpg'),
            ])
            ->assertNotFound();

        $this->assertNull($alumno->fresh()->foto_path);
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
    // HORARIOS (apoyo para las pruebas de clases agendadas)
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

    public function test_progreso_muestra_la_descripcion_del_nivel_actual_y_de_la_trayectoria(): void
    {
        $nivel = Nivel::factory()->create(['orden' => 1, 'descripcion' => 'Se familiariza con el agua y flota con apoyo.']);
        $siguiente = Nivel::factory()->create([
            'orden' => 2,
            'categoria_edad' => $nivel->categoria_edad,
            'descripcion' => 'Nada de crol sin apoyo durante 10 metros.',
        ]);

        [$tutor, $alumno] = $this->crearTutorConAlumno(nivel: $nivel);

        $this->actingAs($tutor)
            ->get(route('portal.progreso', ['alumno' => $alumno->id]))
            ->assertOk()
            ->assertSee('Se familiariza con el agua y flota con apoyo.')
            ->assertSee('Nada de crol sin apoyo durante 10 metros.');
    }

    public function test_la_trayectoria_muestra_los_niveles_de_todas_las_categorias(): void
    {
        $nivelNinos = Nivel::factory()->create(['nombre' => 'Estrella Trayectoria', 'orden' => 1, 'categoria_edad' => 'Niños']);
        Nivel::factory()->create(['nombre' => 'Pececito Trayectoria', 'orden' => 1, 'categoria_edad' => 'Bebés']);
        Nivel::factory()->create(['nombre' => 'Perla Trayectoria', 'orden' => 1, 'categoria_edad' => 'Adultos']);
        Nivel::factory()->create(['nombre' => 'Nivel Desactivado', 'orden' => 2, 'categoria_edad' => 'Adultos', 'activo' => false]);

        [$tutor, $alumno] = $this->crearTutorConAlumno(nivel: $nivelNinos);

        $this->actingAs($tutor)
            ->get(route('portal.progreso', ['alumno' => $alumno->id]))
            ->assertOk()
            ->assertSeeInOrder(['Trayectoria de niveles', 'Pececito Trayectoria', 'Estrella Trayectoria', 'Perla Trayectoria'])
            ->assertDontSee('Nivel Desactivado');
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

        AlumnoNivelHistorial::create([
            'alumno_id' => $alumno->id,
            'nivel_id' => $nivelAnterior->id,
            'fecha_inicio' => now()->subMonths(2)->toDateString(),
            'fecha_fin' => now()->subMonth()->toDateString(),
        ]);

        AlumnoNivelHistorial::create([
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

    public function test_historial_de_evaluaciones_permite_navegar_por_todas_las_paginas(): void
    {
        [$tutor, $alumno] = $this->crearTutorConAlumno();
        $instructor = Instructor::factory()->create();

        // Niveles desactivados: no salen en la trayectoria (que lista todos
        // los niveles activos), solo en el historial paginado que se prueba.
        for ($i = 1; $i <= 8; $i++) {
            $nivel = Nivel::factory()->create([
                'nombre' => "Nivel histórico {$i}",
                'categoria_edad' => 'Adultos',
                'activo' => false,
            ]);
            Evaluacion::factory()->create([
                'alumno_id' => $alumno->id,
                'instructor_id' => $instructor->id,
                'nivel_id' => $nivel->id,
                'fecha' => now()->subDays($i)->toDateString(),
            ]);
        }

        $primeraPagina = $this->actingAs($tutor)
            ->get(route('portal.progreso', ['alumno' => $alumno->id]))
            ->assertOk()
            ->assertSeeInOrder(['Nivel histórico 1', 'Nivel histórico 6'])
            ->assertDontSee('Nivel histórico 7')
            ->assertSee('Ver evaluaciones anteriores')
            ->assertSee('alumno='.$alumno->id.'&amp;page=2', false)
            ->assertDontSee('Ver evaluaciones siguientes');

        $this->get(route('portal.progreso', ['alumno' => $alumno->id, 'page' => 2]))
            ->assertOk()
            ->assertSeeInOrder(['Nivel histórico 7', 'Nivel histórico 8'])
            ->assertSee('Ver evaluaciones siguientes')
            ->assertDontSee('Ver evaluaciones anteriores')
            ->assertSee('alumno='.$alumno->id.'&amp;page=1', false);
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

    public function test_cuenta_muestra_clases_futuras_de_una_inscripcion_activa_aunque_aun_no_tenga_citas_generadas(): void
    {
        [$tutor, $alumno, $sucursal, $nivel] = $this->crearTutorConAlumno();
        $diaSiguiente = now()->isoWeekday() === 7 ? 1 : now()->isoWeekday() + 1;
        $horario = $this->crearHorario($sucursal, $nivel, diaSemana: $diaSiguiente);
        $horario->update(['nombre_grupo' => 'Grupo Recurrente']);

        Inscripcion::factory()->create([
            'horario_id' => $horario->id,
            'alumno_id' => $alumno->id,
            'fecha_inicio' => today(),
            'activa' => true,
            'estado' => 'aprobada',
        ]);

        $this->actingAs($tutor)
            ->get(route('portal.cuenta', ['alumno' => $alumno->id]))
            ->assertOk()
            ->assertSee('Grupo Recurrente');
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
