<?php

namespace Tests\Feature;

use App\Enums\EstadoCita;
use App\Models\Alumno;
use App\Models\Carril;
use App\Models\Cita;
use App\Models\Horario;
use App\Models\Inscripcion;
use App\Models\Instructor;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

/**
 * Falta automática cuando no se registra la asistencia y registro manual
 * de asistencias o faltas de días anteriores.
 */
class AsistenciaAutomaticaTest extends TestCase
{
    use RefreshDatabase;

    private const MIERCOLES = 3;

    protected function setUp(): void
    {
        parent::setUp();

        // Miércoles 14 de octubre de 2026, 18:00.
        $this->travelTo(Carbon::create(2026, 10, 14, 18));
    }

    /**
     * @return array{sucursal: Sucursal, admin: User, grupo: Horario}
     */
    private function crearEscenario(string $inicio = '16:00', string $fin = '17:00'): array
    {
        $sucursal = Sucursal::factory()->create();

        $grupo = Horario::factory()->create([
            'sucursal_id' => $sucursal->id,
            'instructor_id' => Instructor::factory()->create(['sucursal_id' => $sucursal->id])->id,
            'carril_id' => Carril::factory()->create(['sucursal_id' => $sucursal->id])->id,
            'dia_semana' => self::MIERCOLES,
            'hora_inicio' => $inicio.':00',
            'hora_fin' => $fin.':00',
            'nombre_grupo' => 'Grupo Miércoles',
            'activo' => true,
        ]);

        return [
            'sucursal' => $sucursal,
            'admin' => User::factory()->admin($sucursal->id)->create(),
            'grupo' => $grupo,
        ];
    }

    private function inscribir(Horario $grupo, string $nombre, array $alumno = [], string $desde = '2026-09-01'): Alumno
    {
        $alumno = Alumno::factory()->create([
            'sucursal_id' => $grupo->sucursal_id,
            'nombre' => $nombre,
            'apellidos' => 'Prueba',
            'estado' => 'activo',
            ...$alumno,
        ]);

        Inscripcion::factory()->create([
            'alumno_id' => $alumno->id,
            'horario_id' => $grupo->id,
            'activa' => true,
            'fecha_inicio' => $desde,
        ]);

        return $alumno;
    }

    // ------------------------------------------------------------------
    // FALTA AUTOMÁTICA
    // ------------------------------------------------------------------

    public function test_al_terminar_la_clase_quien_no_registro_asistencia_queda_con_falta(): void
    {
        $e = $this->crearEscenario();
        $falto = $this->inscribir($e['grupo'], 'Falto');
        $asistio = $this->inscribir($e['grupo'], 'Asistio');
        $inactivo = $this->inscribir($e['grupo'], 'Inactivo', ['estado' => 'baja_temporal']);
        $nuevo = $this->inscribir($e['grupo'], 'Nuevo', desde: '2026-10-15');

        Cita::factory()->create([
            'alumno_id' => $asistio->id,
            'horario_id' => $e['grupo']->id,
            'sucursal_id' => $e['sucursal']->id,
            'fecha' => '2026-10-14',
            'asistio' => true,
            'estado' => EstadoCita::Completada->value,
        ]);

        $this->artisan('asistencia:marcar-faltas')->assertExitCode(0);

        $falta = Cita::where('alumno_id', $falto->id)->sole();
        $this->assertFalse($falta->asistio);
        $this->assertSame('2026-10-14', $falta->fecha->toDateString());
        $this->assertSame(EstadoCita::Completada, $falta->estado);

        $this->assertTrue(Cita::where('alumno_id', $asistio->id)->sole()->asistio);
        $this->assertSame(0, Cita::where('alumno_id', $inactivo->id)->count());
        $this->assertSame(0, Cita::where('alumno_id', $nuevo->id)->count());

        // Correrlo otra vez no duplica nada.
        $this->artisan('asistencia:marcar-faltas')->assertExitCode(0);
        $this->assertSame(2, Cita::count());

        // La falta ya aparece para reponerse.
        $this->actingAs($e['admin'])
            ->get(route('reposiciones.index'))
            ->assertOk()
            ->assertSee('Falto Prueba');
    }

    public function test_no_marca_falta_antes_de_que_termine_la_clase_y_su_tiempo_de_gracia(): void
    {
        // Clase de 17:00 a 17:50: a las 18:00 aún no pasan los 15 minutos.
        $e = $this->crearEscenario('17:00', '17:50');
        $alumno = $this->inscribir($e['grupo'], 'Puntual');

        $this->artisan('asistencia:marcar-faltas')->assertExitCode(0);
        $this->assertSame(0, Cita::count());

        $this->travelTo(Carbon::create(2026, 10, 14, 18, 6));
        $this->artisan('asistencia:marcar-faltas')->assertExitCode(0);

        $this->assertFalse(Cita::where('alumno_id', $alumno->id)->sole()->asistio);
    }

    public function test_si_escanea_su_qr_tarde_la_falta_automatica_se_cambia_por_asistencia(): void
    {
        $e = $this->crearEscenario();
        $alumno = $this->inscribir($e['grupo'], 'Tarde');

        $this->artisan('asistencia:marcar-faltas');
        $this->assertFalse(Cita::sole()->asistio);

        $this->actingAs($e['admin'])
            ->post(route('asistencia.confirmar', $alumno->qr_token))
            ->assertOk();

        $this->assertTrue(Cita::sole()->asistio);
    }

    public function test_las_clases_de_una_sola_fecha_sin_asistencia_tambien_quedan_como_falta_pero_no_las_movidas(): void
    {
        $e = $this->crearEscenario();
        $deVisita = Alumno::factory()->create(['sucursal_id' => $e['sucursal']->id, 'estado' => 'activo']);
        $movido = $this->inscribir($e['grupo'], 'Movido');

        $extra = Cita::factory()->create([
            'alumno_id' => $deVisita->id,
            'horario_id' => $e['grupo']->id,
            'sucursal_id' => $e['sucursal']->id,
            'fecha' => '2026-10-14',
            'es_extra' => true,
            'estado' => EstadoCita::Programada->value,
        ]);

        // Su clase de hoy se reagendó a otro día: no es falta.
        $marca = Cita::factory()->create([
            'alumno_id' => $movido->id,
            'horario_id' => $e['grupo']->id,
            'sucursal_id' => $e['sucursal']->id,
            'fecha' => '2026-10-14',
            'estado' => EstadoCita::Reagendada->value,
        ]);

        $this->artisan('asistencia:marcar-faltas');

        $this->assertFalse($extra->fresh()->asistio);
        $this->assertNull($marca->fresh()->asistio);
        $this->assertSame(EstadoCita::Reagendada, $marca->fresh()->estado);
    }

    public function test_no_marca_faltas_de_dias_anteriores_al_inicio_de_la_falta_automatica(): void
    {
        $e = $this->crearEscenario();
        $this->inscribir($e['grupo'], 'Anterior');

        // Miércoles 7 de octubre: antes del 14, se registra a mano.
        $this->travelTo(Carbon::create(2026, 10, 8, 9));
        $this->artisan('asistencia:marcar-faltas', ['--dias' => 7])->assertExitCode(0);

        $this->assertSame(0, Cita::count());
    }

    public function test_la_tarea_esta_programada_cada_hora(): void
    {
        Artisan::call('schedule:list');

        $this->assertStringContainsString('asistencia:marcar-faltas', Artisan::output());
    }

    // ------------------------------------------------------------------
    // REGISTRO MANUAL
    // ------------------------------------------------------------------

    public function test_la_administracion_marca_la_falta_de_un_dia_anterior_desde_el_grupo(): void
    {
        $e = $this->crearEscenario();
        $alumno = $this->inscribir($e['grupo'], 'Pasado');

        $this->actingAs($e['admin'])
            ->get(route('horarios.index'))
            ->assertOk()
            ->assertSee('Registrar asistencia o falta')
            ->assertSee('"alumno_id":'.$alumno->id, false);

        $this->actingAs($e['admin'])->post(route('alumnos.clases.asistencia', $alumno), [
            'horario_id' => $e['grupo']->id,
            'fecha' => '2026-10-07',
            'asistio' => 0,
        ])->assertSessionHasNoErrors();

        $falta = Cita::sole();
        $this->assertFalse($falta->asistio);
        $this->assertSame('2026-10-07', $falta->fecha->toDateString());

        $this->actingAs($e['admin'])
            ->get(route('alumnos.show', $alumno))
            ->assertOk()
            ->assertSee('● Falta')
            ->assertSee('Sí asistió');
    }

    public function test_la_administracion_corrige_una_falta_automatica_a_asistencia(): void
    {
        $e = $this->crearEscenario();
        $alumno = $this->inscribir($e['grupo'], 'Corregido');

        $this->artisan('asistencia:marcar-faltas');

        $this->actingAs($e['admin'])->post(route('alumnos.clases.asistencia', $alumno), [
            'horario_id' => $e['grupo']->id,
            'fecha' => '2026-10-14',
            'asistio' => 1,
        ])->assertSessionHasNoErrors();

        $cita = Cita::sole();
        $this->assertTrue($cita->asistio);
        $this->assertNull($cita->notas);
    }

    public function test_no_se_registra_en_un_dia_sin_clase_ni_en_fechas_futuras_ni_sin_inscripcion(): void
    {
        $e = $this->crearEscenario();
        $alumno = $this->inscribir($e['grupo'], 'Validado', desde: '2026-10-01');

        $registrar = fn (string $fecha) => $this->actingAs($e['admin'])->post(route('alumnos.clases.asistencia', $alumno), [
            'horario_id' => $e['grupo']->id,
            'fecha' => $fecha,
            'asistio' => 0,
        ]);

        $registrar('2026-10-13')->assertSessionHasErrors('fecha'); // martes: el grupo es de miércoles
        $registrar('2026-10-21')->assertSessionHasErrors('fecha'); // futura
        $registrar('2026-09-23')->assertSessionHasErrors('fecha'); // antes de inscribirse

        $this->assertSame(0, Cita::count());
    }

    public function test_no_se_cambia_a_asistencia_una_falta_que_ya_tiene_reposicion(): void
    {
        $e = $this->crearEscenario();
        $alumno = $this->inscribir($e['grupo'], 'Repuesto');

        $falta = Cita::factory()->create([
            'alumno_id' => $alumno->id,
            'horario_id' => $e['grupo']->id,
            'sucursal_id' => $e['sucursal']->id,
            'fecha' => '2026-10-07',
            'asistio' => false,
            'estado' => EstadoCita::Completada->value,
        ]);

        Cita::factory()->create([
            'alumno_id' => $alumno->id,
            'horario_id' => Horario::factory()->create(['sucursal_id' => $e['sucursal']->id])->id,
            'sucursal_id' => $e['sucursal']->id,
            'fecha' => '2026-10-20',
            'reposicion_de_id' => $falta->id,
        ]);

        $this->actingAs($e['admin'])->post(route('alumnos.clases.asistencia', $alumno), [
            'horario_id' => $e['grupo']->id,
            'fecha' => '2026-10-07',
            'asistio' => 1,
        ])->assertSessionHasErrors('fecha');

        $this->assertFalse($falta->fresh()->asistio);
    }

    public function test_un_admin_de_otra_sucursal_no_puede_registrar_asistencia(): void
    {
        $e = $this->crearEscenario();
        $alumno = $this->inscribir($e['grupo'], 'Ajeno');
        $adminAjeno = User::factory()->admin(Sucursal::factory()->create()->id)->create();

        $this->actingAs($adminAjeno)->post(route('alumnos.clases.asistencia', $alumno), [
            'horario_id' => $e['grupo']->id,
            'fecha' => '2026-10-07',
            'asistio' => 0,
        ])->assertForbidden();
    }
}
