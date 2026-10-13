<?php

namespace Tests\Feature;

use App\Enums\EstadoCita;
use App\Models\Alumno;
use App\Models\Carril;
use App\Models\Cita;
use App\Models\Horario;
use App\Models\Inscripcion;
use App\Models\Instructor;
use App\Models\Nivel;
use App\Models\Plan;
use App\Models\Sucursal;
use App\Models\User;
use App\Support\ClasesDelAlumno;
use App\Support\Reposiciones;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Las clases de un alumno se deducen de sus inscripciones; estas pruebas
 * cubren lo que antes fallaba cuando dependían de registros guardados solo
 * para las primeras cuatro semanas.
 */
class ClasesDelAlumnoTest extends TestCase
{
    use RefreshDatabase;

    private const LUNES = 1;

    private const MIERCOLES = 3;

    private const VIERNES = 5;

    protected function setUp(): void
    {
        parent::setUp();

        // Lunes 12 de octubre de 2026, 9:00.
        $this->travelTo(Carbon::create(2026, 10, 12, 9));
    }

    /**
     * Alumno inscrito desde hace tres meses en "Grupo Lunes", sin ninguna
     * clase guardada: así están la mayoría de los alumnos reales.
     *
     * @return array{sucursal: Sucursal, admin: User, tutor: User, alumno: Alumno, lunes: Horario, inscripcion: Inscripcion}
     */
    private function crearEscenario(): array
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();
        $tutor = User::factory()->tutor()->create();

        $alumno = Alumno::factory()->create([
            'sucursal_id' => $sucursal->id,
            'tutor_user_id' => $tutor->id,
            'nivel_id' => Nivel::factory()->create(['categoria_edad' => 'Niños'])->id,
            'nombre' => 'Leonardo',
            'apellidos' => 'Padilla',
        ]);

        $lunes = $this->crearGrupo($sucursal, self::LUNES, 'Grupo Lunes');
        $inscripcion = $this->inscribir($alumno, $lunes);

        return compact('sucursal', 'admin', 'tutor', 'alumno', 'lunes', 'inscripcion');
    }

    private function crearGrupo(Sucursal $sucursal, int $dia, string $nombre, ?string $carril = null): Horario
    {
        return Horario::factory()->create([
            'sucursal_id' => $sucursal->id,
            'instructor_id' => Instructor::factory()->create(['sucursal_id' => $sucursal->id])->id,
            'carril_id' => Carril::factory()->create(['sucursal_id' => $sucursal->id, ...($carril ? ['nombre' => $carril] : [])])->id,
            'nivel_id' => null,
            'dia_semana' => $dia,
            'hora_inicio' => '16:00:00',
            'hora_fin' => '17:00:00',
            'capacidad_maxima' => 6,
            'nombre_grupo' => $nombre,
            'activo' => true,
        ]);
    }

    private function inscribir(Alumno $alumno, Horario $horario): Inscripcion
    {
        return Inscripcion::factory()->create([
            'alumno_id' => $alumno->id,
            'horario_id' => $horario->id,
            'fecha_inicio' => today()->subMonths(3),
            'activa' => true,
        ]);
    }

    /**
     * @return list<string>
     */
    private function fechasProximas(Alumno $alumno, ?Horario $horario = null): array
    {
        return ClasesDelAlumno::proximas($alumno)
            ->filter(fn (array $clase) => $horario === null || $clase['cita']->horario_id === $horario->id)
            ->map(fn (array $clase) => $clase['cita']->fecha->toDateString())
            ->values()
            ->all();
    }

    // ------------------------------------------------------------------
    // PRÓXIMAS CLASES
    // ------------------------------------------------------------------

    public function test_un_alumno_inscrito_hace_meses_sigue_viendo_sus_proximas_clases(): void
    {
        $e = $this->crearEscenario();

        $this->assertSame(0, Cita::count());
        $this->assertSame(
            ['2026-10-12', '2026-10-19', '2026-10-26', '2026-11-02', '2026-11-09'],
            $this->fechasProximas($e['alumno'])
        );

        $this->actingAs($e['admin'])
            ->get(route('alumnos.show', $e['alumno']))
            ->assertOk()
            ->assertSee('Grupo Lunes')
            ->assertSee('Avisó que faltará')
            ->assertDontSee('no tiene clases próximas');

        $this->actingAs($e['tutor'])
            ->get(route('portal.dashboard'))
            ->assertOk()
            ->assertSee('Grupo Lunes');
    }

    public function test_se_muestran_las_clases_de_todos_sus_grupos_y_no_solo_las_del_primero(): void
    {
        $e = $this->crearEscenario();
        $miercoles = $this->crearGrupo($e['sucursal'], self::MIERCOLES, 'Grupo Miércoles');
        $this->inscribir($e['alumno'], $miercoles);

        $proximas = ClasesDelAlumno::proximas($e['alumno'], limite: 3)
            ->map(fn (array $clase) => $clase['cita']->fecha->toDateString().' '.$clase['cita']->horario->nombre_grupo)
            ->all();

        $this->assertSame(['2026-10-12 Grupo Lunes', '2026-10-14 Grupo Miércoles', '2026-10-19 Grupo Lunes'], $proximas);
        $this->assertSame(2, $e['alumno']->clasesEstaSemana());
    }

    public function test_al_cambiar_de_grupo_ya_no_quedan_clases_del_grupo_anterior(): void
    {
        $e = $this->crearEscenario();
        $viernes = $this->crearGrupo($e['sucursal'], self::VIERNES, 'Grupo Viernes');

        // Clase guardada del grupo anterior, como las que se apartaban antes.
        $apartada = Cita::factory()->create([
            'alumno_id' => $e['alumno']->id,
            'horario_id' => $e['lunes']->id,
            'sucursal_id' => $e['sucursal']->id,
            'fecha' => '2026-10-19',
        ]);

        $this->actingAs($e['admin'])->patch(route('inscripciones.cambiar-grupo'), [
            'alumno_id' => $e['alumno']->id,
            'inscripcion_id' => $e['inscripcion']->id,
            'horario_id' => $viernes->id,
        ])->assertSessionHasNoErrors();

        $this->assertModelMissing($apartada);
        $this->assertSame([], $this->fechasProximas($e['alumno'], $e['lunes']));
        $this->assertSame('2026-10-16', $this->fechasProximas($e['alumno'], $viernes)[0]);
    }

    public function test_al_retirar_y_volver_a_asignar_el_mismo_grupo_sus_clases_aparecen_completas(): void
    {
        $e = $this->crearEscenario();

        $this->actingAs($e['admin'])->delete(route('inscripciones.destroy', $e['inscripcion']))->assertSessionHasNoErrors();
        $this->assertSame([], $this->fechasProximas($e['alumno']));

        $this->actingAs($e['admin'])->post(route('inscripciones.store'), [
            'alumno_id' => $e['alumno']->id,
            'horario_id' => $e['lunes']->id,
        ])->assertSessionHasNoErrors();

        $this->assertSame(
            ['2026-10-12', '2026-10-19', '2026-10-26', '2026-11-02', '2026-11-09'],
            $this->fechasProximas($e['alumno'])
        );
    }

    public function test_si_el_grupo_cambia_de_dia_las_clases_del_alumno_siguen_al_grupo(): void
    {
        $e = $this->crearEscenario();

        Cita::factory()->create([
            'alumno_id' => $e['alumno']->id,
            'horario_id' => $e['lunes']->id,
            'sucursal_id' => $e['sucursal']->id,
            'fecha' => '2026-10-19',
        ]);

        $this->actingAs($e['admin'])->patch(route('horarios.reagendar', $e['lunes']), [
            'nombre_grupo' => 'Grupo Lunes',
            'dia_semana' => self::MIERCOLES,
            'hora_inicio' => '17:00',
            'hora_fin' => '18:00',
            'carril_id' => $e['lunes']->carril_id,
        ])->assertSessionHasNoErrors();

        $this->assertSame(
            ['2026-10-14', '2026-10-21', '2026-10-28', '2026-11-04'],
            $this->fechasProximas($e['alumno'])
        );
    }

    // ------------------------------------------------------------------
    // REAGENDAR
    // ------------------------------------------------------------------

    public function test_reagendar_una_clase_de_su_grupo_la_mueve_solo_esa_vez(): void
    {
        $e = $this->crearEscenario();
        $miercoles = $this->crearGrupo($e['sucursal'], self::MIERCOLES, 'Grupo Miércoles');

        $this->actingAs($e['admin'])->patch(route('alumnos.clases.reagendar', $e['alumno']), [
            'origen_horario_id' => $e['lunes']->id,
            'origen_fecha' => '2026-10-12',
            'horario_id' => $miercoles->id,
            'fecha' => '2026-10-14',
        ])->assertSessionHasNoErrors();

        // El lunes 12 ya no tiene clase; el miércoles 14 sí; sigue en su grupo.
        $this->assertSame(['2026-10-19', '2026-10-26', '2026-11-02', '2026-11-09'], $this->fechasProximas($e['alumno'], $e['lunes']));
        $this->assertSame(['2026-10-14'], $this->fechasProximas($e['alumno'], $miercoles));
        $this->assertTrue(ClasesDelAlumno::estaInscrito($e['alumno'], $e['lunes']->id));
        $this->assertFalse(ClasesDelAlumno::estaInscrito($e['alumno'], $miercoles->id));

        $this->actingAs($e['admin'])
            ->get(route('alumnos.show', $e['alumno']))
            ->assertOk()
            ->assertSee('CLASE REAGENDADA');

        // El instructor del grupo original ve que hoy no viene.
        $this->actingAs($e['lunes']->instructor->user)
            ->get(route('instructor.grupos.show', $e['lunes']))
            ->assertOk()
            ->assertSee('Leonardo Padilla')
            ->assertSee('Reagendó esta clase');

        // El miércoles aparece en la lista del grupo que lo recibe y ocupa lugar.
        $this->assertSame(5, Reposiciones::cupoDisponible($miercoles, Carbon::parse('2026-10-14')));

        $this->travelTo(Carbon::create(2026, 10, 14, 9));

        $this->actingAs($miercoles->instructor->user)
            ->get(route('instructor.grupos.show', $miercoles))
            ->assertOk()
            ->assertSee('Leonardo Padilla')
            ->assertSee('Clase reagendada · solo hoy');
    }

    public function test_la_administracion_puede_reagendar_sin_limite_aunque_el_plan_este_completo(): void
    {
        $e = $this->crearEscenario();
        $e['alumno']->update(['plan_id' => Plan::factory()->create(['clases_por_semana' => 1])->id]);

        $miercoles = $this->crearGrupo($e['sucursal'], self::MIERCOLES, 'Grupo Miércoles');
        $viernes = $this->crearGrupo($e['sucursal'], self::VIERNES, 'Grupo Viernes');

        // Primera vez: del lunes al miércoles.
        $this->actingAs($e['admin'])->patch(route('alumnos.clases.reagendar', $e['alumno']), [
            'origen_horario_id' => $e['lunes']->id,
            'origen_fecha' => '2026-10-12',
            'horario_id' => $miercoles->id,
            'fecha' => '2026-10-14',
        ])->assertSessionHasNoErrors();

        // Segunda y tercera vez sobre la misma clase: al viernes y de regreso.
        $this->actingAs($e['admin'])->patch(route('alumnos.clases.reagendar', $e['alumno']), [
            'origen_horario_id' => $miercoles->id,
            'origen_fecha' => '2026-10-14',
            'horario_id' => $viernes->id,
            'fecha' => '2026-10-16',
        ])->assertSessionHasNoErrors();

        $this->assertSame(['2026-10-16'], $this->fechasProximas($e['alumno'], $viernes));
        $this->assertSame([], $this->fechasProximas($e['alumno'], $miercoles));

        // Y la de la semana siguiente también se puede mover.
        $this->actingAs($e['admin'])->patch(route('alumnos.clases.reagendar', $e['alumno']), [
            'origen_horario_id' => $e['lunes']->id,
            'origen_fecha' => '2026-10-19',
            'horario_id' => $miercoles->id,
            'fecha' => '2026-10-21',
        ])->assertSessionHasNoErrors();

        $this->assertSame(['2026-10-26', '2026-11-02', '2026-11-09'], $this->fechasProximas($e['alumno'], $e['lunes']));
        $this->assertSame(1, $e['alumno']->clasesEstaSemana());
    }

    public function test_una_clase_reagendada_puede_regresar_a_su_fecha_original(): void
    {
        $e = $this->crearEscenario();
        $miercoles = $this->crearGrupo($e['sucursal'], self::MIERCOLES, 'Grupo Miércoles');

        $this->actingAs($e['admin'])->patch(route('alumnos.clases.reagendar', $e['alumno']), [
            'origen_horario_id' => $e['lunes']->id,
            'origen_fecha' => '2026-10-19',
            'horario_id' => $miercoles->id,
            'fecha' => '2026-10-21',
        ])->assertSessionHasNoErrors();

        $this->actingAs($e['admin'])->patch(route('alumnos.clases.reagendar', $e['alumno']), [
            'origen_horario_id' => $miercoles->id,
            'origen_fecha' => '2026-10-21',
            'horario_id' => $e['lunes']->id,
            'fecha' => '2026-10-19',
        ])->assertSessionHasNoErrors();

        $this->assertSame(0, Cita::count());
        $this->assertSame(
            ['2026-10-12', '2026-10-19', '2026-10-26', '2026-11-02', '2026-11-09'],
            $this->fechasProximas($e['alumno'])
        );
    }

    public function test_no_se_reagenda_a_un_dia_en_que_ya_tiene_clase_por_su_grupo(): void
    {
        $e = $this->crearEscenario();
        $miercoles = $this->crearGrupo($e['sucursal'], self::MIERCOLES, 'Grupo Miércoles');
        $this->inscribir($e['alumno'], $miercoles);

        $this->actingAs($e['admin'])->patch(route('alumnos.clases.reagendar', $e['alumno']), [
            'origen_horario_id' => $e['lunes']->id,
            'origen_fecha' => '2026-10-12',
            'horario_id' => $miercoles->id,
            'fecha' => '2026-10-14',
        ])->assertSessionHasErrors('horario_id');

        $this->assertSame(0, Cita::count());
    }

    public function test_el_tablero_lista_para_reagendar_las_clases_de_alumnos_sin_registros_guardados(): void
    {
        $e = $this->crearEscenario();

        $this->actingAs($e['admin'])
            ->get(route('horarios.index'))
            ->assertOk()
            ->assertSee('Leonardo Padilla')
            ->assertSee('data-alumno="'.$e['alumno']->id.'"', false)
            ->assertSee('data-fecha="2026-10-12"', false)
            ->assertSee('data-fecha="2026-10-19"', false);
    }

    // ------------------------------------------------------------------
    // FALTA AVISADA
    // ------------------------------------------------------------------

    public function test_la_administracion_registra_una_falta_avisada_y_el_instructor_la_ve(): void
    {
        $e = $this->crearEscenario();

        $this->actingAs($e['admin'])->post(route('alumnos.clases.falta-avisada', $e['alumno']), [
            'horario_id' => $e['lunes']->id,
            'fecha' => '2026-10-12',
        ])->assertSessionHasNoErrors();

        $cita = Cita::sole();

        $this->assertFalse($cita->asistio);
        $this->assertTrue($cita->falta_avisada);
        $this->assertSame(EstadoCita::Completada, $cita->estado);

        $instructor = $e['lunes']->instructor->user;

        $this->actingAs($instructor)
            ->get(route('instructor.grupos.show', $e['lunes']))
            ->assertOk()
            ->assertSee('Leonardo Padilla')
            ->assertSee('Avisó que no asistirá')
            ->assertSee('No asistirá (avisó)');

        $this->actingAs($instructor)
            ->get(route('instructor.dashboard'))
            ->assertOk()
            ->assertSee('1 avisó/avisaron que no asistirá(n)');

        // La falta queda disponible para reponerse.
        $this->assertSame(1, Reposiciones::faltasPorReponer($e['alumno'])->count());

        $this->actingAs($e['admin'])
            ->get(route('alumnos.show', $e['alumno']))
            ->assertOk()
            ->assertSee('Quitar aviso de falta');
    }

    public function test_se_puede_avisar_una_falta_de_un_dia_futuro_y_deshacerla(): void
    {
        $e = $this->crearEscenario();

        $this->actingAs($e['admin'])->post(route('alumnos.clases.falta-avisada', $e['alumno']), [
            'horario_id' => $e['lunes']->id,
            'fecha' => '2026-10-19',
        ])->assertSessionHasNoErrors();

        // Hoy (día 12) el instructor todavía no ve ningún aviso.
        $this->actingAs($e['lunes']->instructor->user)
            ->get(route('instructor.grupos.show', $e['lunes']))
            ->assertOk()
            ->assertDontSee('Avisó que no asistirá');

        $this->actingAs($e['admin'])
            ->delete(route('citas.falta-avisada.destroy', Cita::sole()))
            ->assertSessionHasNoErrors();

        $this->assertSame(0, Cita::count());
        $this->assertContains('2026-10-19', $this->fechasProximas($e['alumno']));
    }

    public function test_no_se_avisa_falta_de_un_dia_sin_clase_ni_de_fechas_pasadas(): void
    {
        $e = $this->crearEscenario();

        $this->actingAs($e['admin'])->post(route('alumnos.clases.falta-avisada', $e['alumno']), [
            'horario_id' => $e['lunes']->id,
            'fecha' => '2026-10-13',
        ])->assertSessionHasErrors('falta');

        $this->actingAs($e['admin'])->post(route('alumnos.clases.falta-avisada', $e['alumno']), [
            'horario_id' => $e['lunes']->id,
            'fecha' => '2026-10-05',
        ])->assertSessionHasErrors('fecha');

        $this->assertSame(0, Cita::count());
    }

    public function test_si_el_alumno_llega_y_escanea_su_qr_el_aviso_de_falta_se_reemplaza_por_asistencia(): void
    {
        $e = $this->crearEscenario();

        $this->actingAs($e['admin'])->post(route('alumnos.clases.falta-avisada', $e['alumno']), [
            'horario_id' => $e['lunes']->id,
            'fecha' => '2026-10-12',
        ]);

        $this->actingAs($e['admin'])
            ->post(route('asistencia.confirmar', $e['alumno']->qr_token))
            ->assertOk()
            ->assertSee('Asistencia registrada en: Grupo Lunes.');

        $cita = Cita::sole();

        $this->assertTrue($cita->asistio);
        $this->assertFalse($cita->falta_avisada);
    }

    // ------------------------------------------------------------------
    // LISTAS DE GRUPOS
    // ------------------------------------------------------------------

    public function test_las_listas_de_grupos_muestran_carril_e_instructor_para_distinguirlos(): void
    {
        $e = $this->crearEscenario();

        // Dos grupos con el mismo nombre, día y hora: solo cambia el carril.
        $carril1 = $this->crearGrupo($e['sucursal'], self::VIERNES, 'Mixto', 'Carril 1');
        $carril2 = $this->crearGrupo($e['sucursal'], self::VIERNES, 'Mixto', 'Carril 2');

        $this->assertSame(
            'Mixto · Viernes 16:00 · Carril 1 · '.$carril1->instructor->user->name,
            $carril1->etiqueta()
        );

        $this->actingAs($e['admin'])
            ->get(route('alumnos.show', $e['alumno']))
            ->assertOk()
            ->assertSee($carril1->etiqueta())
            ->assertSee($carril2->etiqueta());

        $this->actingAs($e['admin'])
            ->get(route('horarios.index'))
            ->assertOk()
            ->assertSee($carril1->etiqueta())
            ->assertSee($carril2->etiqueta());
    }
}
