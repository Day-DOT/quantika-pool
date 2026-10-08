<?php

namespace Tests\Feature;

use App\Enums\EstadoCita;
use App\Enums\EstadoInscripcion;
use App\Models\Alumno;
use App\Models\Cita;
use App\Models\Horario;
use App\Models\Inscripcion;
use App\Models\Instructor;
use App\Models\Nivel;
use App\Models\Plan;
use App\Models\SolicitudReposicion;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class PortalReposicionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Día 10 del mes: deja margen para tener una falta pasada y fechas
        // futuras para reponer dentro del mismo mes, se corra cuando se corra.
        $this->travelTo(now()->startOfMonth()->addDays(9)->setTime(10, 0));
    }

    /**
     * Un alumno de Niños con una falta este mes y un grupo destino SIN
     * nivel asignado (así están casi todos los grupos reales) que da clase
     * dentro de dos días.
     *
     * @return array{sucursal: Sucursal, tutor: User, admin: User, alumno: Alumno, falta: Cita, destino: Horario, fecha: Carbon}
     */
    private function crearEscenario(int $capacidadDestino = 4): array
    {
        $sucursal = Sucursal::factory()->create();
        $tutor = User::factory()->tutor()->create();
        $admin = User::factory()->admin($sucursal->id)->create();
        $nivel = Nivel::factory()->create(['categoria_edad' => 'Niños']);

        $alumno = Alumno::factory()->create([
            'tutor_user_id' => $tutor->id,
            'sucursal_id' => $sucursal->id,
            'nivel_id' => $nivel->id,
        ]);

        $grupoHabitual = $this->crearGrupo($sucursal, today()->subDays(3), 'Grupo Habitual');
        Inscripcion::factory()->create(['horario_id' => $grupoHabitual->id, 'alumno_id' => $alumno->id, 'activa' => true]);

        $falta = $this->crearFalta($alumno, $grupoHabitual, today()->subDays(3));

        $fecha = today()->addDays(2);
        $destino = $this->crearGrupo($sucursal, $fecha, 'Grupo Destino', $capacidadDestino);

        return compact('sucursal', 'tutor', 'admin', 'alumno', 'falta', 'destino', 'fecha');
    }

    private function crearGrupo(Sucursal $sucursal, Carbon $fecha, string $nombre, int $capacidad = 4, ?Nivel $nivel = null): Horario
    {
        return Horario::factory()->create([
            'sucursal_id' => $sucursal->id,
            'instructor_id' => Instructor::factory()->create(['sucursal_id' => $sucursal->id])->id,
            'nivel_id' => $nivel?->id,
            'dia_semana' => $fecha->isoWeekday(),
            'capacidad_maxima' => $capacidad,
            'nombre_grupo' => $nombre,
            'activo' => true,
        ]);
    }

    private function crearFalta(Alumno $alumno, Horario $horario, Carbon $fecha): Cita
    {
        return Cita::factory()->create([
            'alumno_id' => $alumno->id,
            'horario_id' => $horario->id,
            'sucursal_id' => $alumno->sucursal_id,
            'fecha' => $fecha,
            'asistio' => false,
            'estado' => EstadoCita::Completada->value,
        ]);
    }

    private function solicitar(array $e, array $cambios = [])
    {
        return $this->actingAs($e['tutor'])->post(route('portal.reservar.store'), [
            'alumno_id' => $e['alumno']->id,
            'cita_id' => $e['falta']->id,
            'horario_id' => $e['destino']->id,
            'fecha' => $e['fecha']->toDateString(),
            ...$cambios,
        ]);
    }

    // ------------------------------------------------------------------
    // QUÉ GRUPOS VE EL TUTOR
    // ------------------------------------------------------------------

    public function test_el_tutor_ve_grupos_sin_nivel_aunque_el_plan_del_alumno_este_lleno(): void
    {
        $e = $this->crearEscenario();

        // Plan de 1 clase por semana ya ocupado por su grupo habitual.
        $e['alumno']->update(['plan_id' => Plan::factory()->create(['clases_por_semana' => 1])->id]);

        $this->actingAs($e['tutor'])
            ->get(route('portal.reservar.index', ['alumno' => $e['alumno']->id, 'fecha' => $e['fecha']->toDateString()]))
            ->assertOk()
            ->assertSee('Grupo Destino')
            ->assertSee('Reponer en este grupo')
            ->assertSee('Solicitar reposición');
    }

    public function test_solo_se_muestran_grupos_de_su_sucursal_y_de_su_categoria_de_edad(): void
    {
        $e = $this->crearEscenario();

        $adultos = Nivel::factory()->create(['categoria_edad' => 'Adultos']);
        $this->crearGrupo($e['sucursal'], $e['fecha'], 'Grupo Con Nivel Adultos', nivel: $adultos);

        // Grupo sin nivel pero con un adulto inscrito: se deduce que es de adultos.
        $grupoDeAdultos = $this->crearGrupo($e['sucursal'], $e['fecha'], 'Grupo Sin Nivel De Adultos');
        Inscripcion::factory()->create([
            'horario_id' => $grupoDeAdultos->id,
            'alumno_id' => Alumno::factory()->create(['sucursal_id' => $e['sucursal']->id, 'nivel_id' => $adultos->id])->id,
            'activa' => true,
        ]);

        $this->crearGrupo(Sucursal::factory()->create(), $e['fecha'], 'Grupo Otra Sucursal');
        $this->crearGrupo($e['sucursal'], $e['fecha']->copy()->addDay(), 'Grupo Otro Dia');

        $this->actingAs($e['tutor'])
            ->get(route('portal.reservar.index', ['alumno' => $e['alumno']->id, 'fecha' => $e['fecha']->toDateString()]))
            ->assertOk()
            ->assertSee('Grupo Destino')
            ->assertDontSee('Grupo Con Nivel Adultos')
            ->assertDontSee('Grupo Sin Nivel De Adultos')
            ->assertDontSee('Grupo Otra Sucursal')
            ->assertDontSee('Grupo Otro Dia');
    }

    public function test_sin_faltas_en_el_mes_no_se_ofrece_reponer(): void
    {
        $e = $this->crearEscenario();
        $e['falta']->update(['asistio' => true]);

        $this->actingAs($e['tutor'])
            ->get(route('portal.reservar.index', ['alumno' => $e['alumno']->id]))
            ->assertOk()
            ->assertSee('No hay faltas por reponer')
            ->assertDontSee('Solicitar reposición');
    }

    public function test_las_clases_de_bebes_no_ofrecen_reposicion(): void
    {
        $e = $this->crearEscenario();
        $e['alumno']->update(['nivel_id' => Nivel::factory()->create(['categoria_edad' => 'Bebés'])->id]);

        $this->actingAs($e['tutor'])
            ->get(route('portal.reservar.index', ['alumno' => $e['alumno']->id]))
            ->assertOk()
            ->assertSee('Las clases de bebés no tienen reposición');

        $this->solicitar($e)->assertSessionHasErrors('fecha');
        $this->assertSame(0, SolicitudReposicion::count());
    }

    // ------------------------------------------------------------------
    // SOLICITUD
    // ------------------------------------------------------------------

    public function test_la_solicitud_queda_pendiente_sin_inscribir_al_alumno_ni_ocupar_lugar(): void
    {
        $e = $this->crearEscenario();

        $this->solicitar($e)
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('portal.reservar.index', ['alumno' => $e['alumno']->id]));

        $solicitud = SolicitudReposicion::sole();

        $this->assertSame(EstadoInscripcion::Pendiente, $solicitud->estado);
        $this->assertSame($e['falta']->id, $solicitud->cita_id);
        $this->assertTrue($solicitud->fecha->isSameDay($e['fecha']));

        $this->assertSame(0, Inscripcion::where('alumno_id', $e['alumno']->id)->where('horario_id', $e['destino']->id)->count());
        $this->assertSame(0, Cita::whereNotNull('reposicion_de_id')->count());
    }

    public function test_no_se_puede_pedir_dos_veces_la_misma_falta(): void
    {
        $e = $this->crearEscenario();

        $this->solicitar($e)->assertSessionHasNoErrors();
        $this->solicitar($e)->assertSessionHasErrors('cita_id');

        $this->assertSame(1, SolicitudReposicion::count());
    }

    public function test_el_maximo_mensual_cuenta_reposiciones_y_solicitudes_en_espera(): void
    {
        $e = $this->crearEscenario();
        $grupoHabitual = Horario::where('nombre_grupo', 'Grupo Habitual')->sole();

        // Ya repuso una falta este mes y tiene otra solicitud en espera.
        $faltaRepuesta = $this->crearFalta($e['alumno'], $grupoHabitual, today()->subDays(4));
        Cita::factory()->create([
            'alumno_id' => $e['alumno']->id,
            'horario_id' => $e['destino']->id,
            'sucursal_id' => $e['sucursal']->id,
            'fecha' => today()->addDays(9),
            'asistio' => null,
            'estado' => EstadoCita::Programada->value,
            'reposicion_de_id' => $faltaRepuesta->id,
        ]);

        $otraFalta = $this->crearFalta($e['alumno'], $grupoHabitual, today()->subDays(5));
        $this->solicitar($e, ['cita_id' => $otraFalta->id])->assertSessionHasNoErrors();

        $this->solicitar($e)->assertSessionHasErrors('cita_id');
        $this->assertSame(1, SolicitudReposicion::count());
    }

    public function test_no_se_puede_reponer_en_otro_mes_ni_en_un_dia_que_no_da_clase_el_grupo(): void
    {
        $e = $this->crearEscenario();

        $this->solicitar($e, ['fecha' => $e['fecha']->copy()->addDay()->toDateString()])
            ->assertSessionHasErrors('fecha');

        $mesSiguiente = $e['fecha']->copy()->addWeeks(5);
        $this->solicitar($e, ['fecha' => $mesSiguiente->toDateString()])
            ->assertSessionHasErrors(['fecha' => 'La reposición debe realizarse dentro del mismo mes calendario en que se produjo la falta.']);

        $this->assertSame(0, SolicitudReposicion::count());
    }

    public function test_no_se_puede_solicitar_en_un_grupo_sin_cupo_ese_dia(): void
    {
        $e = $this->crearEscenario(capacidadDestino: 1);

        Inscripcion::factory()->create([
            'horario_id' => $e['destino']->id,
            'alumno_id' => Alumno::factory()->create(['sucursal_id' => $e['sucursal']->id])->id,
            'activa' => true,
        ]);

        $this->solicitar($e)
            ->assertSessionHasErrors(['horario_id' => 'Ese horario ya no tiene cupo disponible para esa fecha.']);
    }

    public function test_un_tutor_no_puede_solicitar_reposicion_para_un_alumno_ajeno(): void
    {
        $e = $this->crearEscenario();
        $otroTutor = User::factory()->tutor()->create();

        $this->actingAs($otroTutor)->post(route('portal.reservar.store'), [
            'alumno_id' => $e['alumno']->id,
            'cita_id' => $e['falta']->id,
            'horario_id' => $e['destino']->id,
            'fecha' => $e['fecha']->toDateString(),
        ])->assertForbidden();

        $this->assertSame(0, SolicitudReposicion::count());
    }

    // ------------------------------------------------------------------
    // RESPUESTA DE LA ADMINISTRACIÓN
    // ------------------------------------------------------------------

    public function test_al_aprobar_se_crea_una_reposicion_de_un_solo_dia_ligada_a_la_falta(): void
    {
        $e = $this->crearEscenario();
        $this->solicitar($e);
        $solicitud = SolicitudReposicion::sole();

        $this->actingAs($e['admin'])
            ->get(route('reservas.index'))
            ->assertOk()
            ->assertSee('Solicitudes de reposición de clase')
            ->assertSee($e['alumno']->nombreCompleto())
            ->assertSee('Grupo Destino');

        $this->actingAs($e['admin'])
            ->patch(route('reposiciones.solicitudes.aprobar', $solicitud))
            ->assertSessionHasNoErrors();

        $reposicion = Cita::whereNotNull('reposicion_de_id')->sole();

        $this->assertSame($e['falta']->id, $reposicion->reposicion_de_id);
        $this->assertSame($e['destino']->id, $reposicion->horario_id);
        $this->assertTrue($reposicion->fecha->isSameDay($e['fecha']));
        $this->assertSame(EstadoInscripcion::Aprobada, $solicitud->fresh()->estado);

        // No queda inscrito en el grupo y la falta deja de estar pendiente.
        $this->assertSame(0, Inscripcion::where('alumno_id', $e['alumno']->id)->where('horario_id', $e['destino']->id)->count());
        $this->assertTrue($e['falta']->reposicion()->exists());

        $this->actingAs($e['tutor'])
            ->get(route('portal.reservar.index', ['alumno' => $e['alumno']->id]))
            ->assertOk()
            ->assertSee('Aprobada')
            ->assertSee('No hay faltas por reponer');
    }

    public function test_al_rechazar_no_se_agenda_nada_y_la_falta_se_puede_volver_a_pedir(): void
    {
        $e = $this->crearEscenario();
        $this->solicitar($e);
        $solicitud = SolicitudReposicion::sole();

        $this->actingAs($e['admin'])
            ->patch(route('reposiciones.solicitudes.rechazar', $solicitud))
            ->assertSessionHasNoErrors();

        $this->assertSame(EstadoInscripcion::Rechazada, $solicitud->fresh()->estado);
        $this->assertSame(0, Cita::whereNotNull('reposicion_de_id')->count());

        $this->solicitar($e)->assertSessionHasNoErrors();
        $this->assertSame(2, SolicitudReposicion::count());
    }

    public function test_no_se_aprueba_si_el_grupo_se_lleno_mientras_esperaba(): void
    {
        $e = $this->crearEscenario(capacidadDestino: 1);
        $this->solicitar($e);
        $solicitud = SolicitudReposicion::sole();

        Inscripcion::factory()->create([
            'horario_id' => $e['destino']->id,
            'alumno_id' => Alumno::factory()->create(['sucursal_id' => $e['sucursal']->id])->id,
            'activa' => true,
        ]);

        $this->actingAs($e['admin'])
            ->patch(route('reposiciones.solicitudes.aprobar', $solicitud))
            ->assertSessionHasErrors('solicitud');

        $this->assertSame(EstadoInscripcion::Pendiente, $solicitud->fresh()->estado);
        $this->assertSame(0, Cita::whereNotNull('reposicion_de_id')->count());
    }

    public function test_un_admin_de_otra_sucursal_no_puede_aprobar_la_solicitud(): void
    {
        $e = $this->crearEscenario();
        $this->solicitar($e);
        $adminAjeno = User::factory()->admin(Sucursal::factory()->create()->id)->create();

        $this->actingAs($adminAjeno)
            ->patch(route('reposiciones.solicitudes.aprobar', SolicitudReposicion::sole()))
            ->assertForbidden();
    }

    public function test_la_administracion_puede_programar_una_reposicion_en_un_grupo_sin_nivel(): void
    {
        $e = $this->crearEscenario();

        $this->actingAs($e['admin'])
            ->get(route('reposiciones.index'))
            ->assertOk()
            ->assertSee('Grupo Destino');

        $this->actingAs($e['admin'])
            ->post(route('citas.reponer', $e['falta']), [
                'horario_id' => $e['destino']->id,
                'fecha' => $e['fecha']->toDateString(),
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('reposiciones.index'));

        $this->assertSame($e['destino']->id, Cita::whereNotNull('reposicion_de_id')->sole()->horario_id);
    }
}
