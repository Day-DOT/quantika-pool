<?php

namespace Tests\Feature;

use App\Enums\ConceptoPago;
use App\Enums\EstadoAlumno;
use App\Enums\EstadoCita;
use App\Enums\EstadoPago;
use App\Models\Alumno;
use App\Models\Carril;
use App\Models\Cita;
use App\Models\Horario;
use App\Models\Inscripcion;
use App\Models\Instructor;
use App\Models\Nivel;
use App\Models\Pago;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClaseExtraTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array{sucursal: Sucursal, admin: User, instructor: Instructor, alumno: Alumno, horario: Horario}
     */
    private function crearEscenario(int $capacidad = 4): array
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();
        $instructor = Instructor::factory()->create(['sucursal_id' => $sucursal->id]);

        // El grupo da clase hoy, así la clase extra se puede agendar para hoy
        // y comprobar que aparece en el lector y en la lista del instructor.
        $horario = Horario::factory()->create([
            'sucursal_id' => $sucursal->id,
            'instructor_id' => $instructor->id,
            'carril_id' => Carril::factory()->create(['sucursal_id' => $sucursal->id])->id,
            'nivel_id' => Nivel::factory()->create()->id,
            'dia_semana' => today()->dayOfWeekIso,
            'capacidad_maxima' => $capacidad,
            'nombre_grupo' => 'Grupo Extra',
            'activo' => true,
        ]);

        $alumno = Alumno::factory()->create([
            'sucursal_id' => $sucursal->id,
            'estado' => EstadoAlumno::Activo->value,
        ]);

        return compact('sucursal', 'admin', 'instructor', 'alumno', 'horario');
    }

    public function test_admin_agenda_una_clase_extra_sin_inscribir_al_alumno_y_registra_el_pago(): void
    {
        $e = $this->crearEscenario();

        $this->actingAs($e['admin'])
            ->post(route('alumnos.clase-extra.store', $e['alumno']), [
                'horario_id' => $e['horario']->id,
                'fecha' => today()->toDateString(),
                'monto' => 250,
                'metodo_pago' => 'efectivo',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $cita = Cita::where('alumno_id', $e['alumno']->id)->sole();

        $this->assertTrue($cita->es_extra);
        $this->assertTrue($cita->fecha->isToday());
        $this->assertSame(EstadoCita::Programada, $cita->estado);
        $this->assertNull($cita->asistio);
        $this->assertSame(0, Inscripcion::where('alumno_id', $e['alumno']->id)->count());

        $pago = Pago::where('alumno_id', $e['alumno']->id)->sole();

        $this->assertSame(ConceptoPago::Otro, $pago->concepto);
        $this->assertSame(EstadoPago::Pagado, $pago->estado);
        $this->assertEquals(250, $pago->monto);
        $this->assertStringContainsString('Clase extra', $pago->observaciones);
    }

    public function test_sin_monto_no_se_registra_ningun_pago(): void
    {
        $e = $this->crearEscenario();

        $this->actingAs($e['admin'])
            ->post(route('alumnos.clase-extra.store', $e['alumno']), [
                'horario_id' => $e['horario']->id,
                'fecha' => today()->toDateString(),
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame(1, Cita::where('alumno_id', $e['alumno']->id)->where('es_extra', true)->count());
        $this->assertSame(0, Pago::where('alumno_id', $e['alumno']->id)->count());
    }

    public function test_la_clase_extra_solo_aparece_en_el_grupo_el_dia_agendado(): void
    {
        $e = $this->crearEscenario();

        $this->actingAs($e['admin'])->post(route('alumnos.clase-extra.store', $e['alumno']), [
            'horario_id' => $e['horario']->id,
            'fecha' => today()->toDateString(),
        ]);

        $this->actingAs($e['instructor']->user)
            ->get(route('instructor.grupos.show', $e['horario']))
            ->assertOk()
            ->assertSee($e['alumno']->nombreCompleto())
            ->assertSee('Clase extra · solo hoy')
            ->assertSee('Sin registrar');

        // La semana siguiente el alumno ya no forma parte del grupo.
        $this->travel(7)->days();

        $this->actingAs($e['instructor']->user)
            ->get(route('instructor.grupos.show', $e['horario']))
            ->assertOk()
            ->assertDontSee($e['alumno']->nombreCompleto());
    }

    public function test_el_qr_registra_la_asistencia_de_la_clase_extra_de_hoy(): void
    {
        $e = $this->crearEscenario();

        $this->actingAs($e['admin'])->post(route('alumnos.clase-extra.store', $e['alumno']), [
            'horario_id' => $e['horario']->id,
            'fecha' => today()->toDateString(),
        ]);

        $this->actingAs($e['admin'])
            ->get(route('asistencia.qr', $e['alumno']->qr_token))
            ->assertOk()
            ->assertSee('Grupo Extra');

        $this->actingAs($e['admin'])
            ->post(route('asistencia.confirmar', $e['alumno']->qr_token))
            ->assertOk()
            ->assertSee('Asistencia registrada en: Grupo Extra.');

        $cita = Cita::where('alumno_id', $e['alumno']->id)->sole();

        $this->assertTrue($cita->asistio);
        $this->assertSame(EstadoCita::Completada, $cita->estado);
        $this->assertTrue($cita->es_extra);
    }

    public function test_la_fecha_debe_coincidir_con_el_dia_del_grupo(): void
    {
        $e = $this->crearEscenario();

        $this->actingAs($e['admin'])
            ->post(route('alumnos.clase-extra.store', $e['alumno']), [
                'horario_id' => $e['horario']->id,
                'fecha' => today()->addDay()->toDateString(),
            ])
            ->assertSessionHasErrors('clase_extra');

        $this->assertSame(0, Cita::count());
    }

    public function test_no_se_agenda_clase_extra_en_un_grupo_donde_ya_esta_inscrito(): void
    {
        $e = $this->crearEscenario();

        Inscripcion::factory()->create([
            'horario_id' => $e['horario']->id,
            'alumno_id' => $e['alumno']->id,
            'activa' => true,
        ]);

        $this->actingAs($e['admin'])
            ->post(route('alumnos.clase-extra.store', $e['alumno']), [
                'horario_id' => $e['horario']->id,
                'fecha' => today()->toDateString(),
            ])
            ->assertSessionHasErrors('clase_extra');

        $this->assertSame(0, Cita::count());
    }

    public function test_no_se_agenda_clase_extra_si_el_grupo_esta_lleno_ese_dia(): void
    {
        $e = $this->crearEscenario(capacidad: 1);

        Inscripcion::factory()->create([
            'horario_id' => $e['horario']->id,
            'alumno_id' => Alumno::factory()->create(['sucursal_id' => $e['sucursal']->id])->id,
            'activa' => true,
        ]);

        $this->actingAs($e['admin'])
            ->post(route('alumnos.clase-extra.store', $e['alumno']), [
                'horario_id' => $e['horario']->id,
                'fecha' => today()->toDateString(),
            ])
            ->assertSessionHasErrors(['clase_extra' => 'Ese grupo ya no tiene cupo disponible para esa fecha.']);
    }

    public function test_solo_se_agenda_clase_extra_a_alumnos_activos(): void
    {
        $e = $this->crearEscenario();
        $e['alumno']->update(['estado' => EstadoAlumno::BajaTemporal->value]);

        $this->actingAs($e['admin'])
            ->post(route('alumnos.clase-extra.store', $e['alumno']), [
                'horario_id' => $e['horario']->id,
                'fecha' => today()->toDateString(),
            ])
            ->assertSessionHasErrors('clase_extra');
    }

    public function test_admin_de_otra_sucursal_no_puede_agendar_clase_extra(): void
    {
        $e = $this->crearEscenario();
        $adminAjeno = User::factory()->admin(Sucursal::factory()->create()->id)->create();

        $this->actingAs($adminAjeno)
            ->post(route('alumnos.clase-extra.store', $e['alumno']), [
                'horario_id' => $e['horario']->id,
                'fecha' => today()->toDateString(),
            ])
            ->assertForbidden();
    }

    public function test_admin_cancela_una_clase_extra_que_aun_no_se_toma(): void
    {
        $e = $this->crearEscenario();

        $this->actingAs($e['admin'])->post(route('alumnos.clase-extra.store', $e['alumno']), [
            'horario_id' => $e['horario']->id,
            'fecha' => today()->toDateString(),
        ]);

        $cita = Cita::where('alumno_id', $e['alumno']->id)->sole();

        $this->actingAs($e['admin'])
            ->delete(route('citas.clase-extra.destroy', $cita))
            ->assertSessionHasNoErrors();

        $this->assertModelMissing($cita);
    }

    public function test_la_ficha_muestra_el_formulario_de_clase_extra_y_el_historial_de_asistencia(): void
    {
        $e = $this->crearEscenario();

        Cita::factory()->create([
            'alumno_id' => $e['alumno']->id,
            'horario_id' => $e['horario']->id,
            'sucursal_id' => $e['sucursal']->id,
            'fecha' => today()->subDays(7),
            'asistio' => false,
            'estado' => EstadoCita::Completada->value,
        ]);
        Cita::factory()->create([
            'alumno_id' => $e['alumno']->id,
            'horario_id' => $e['horario']->id,
            'sucursal_id' => $e['sucursal']->id,
            'fecha' => today()->subDays(14),
            'asistio' => true,
            'estado' => EstadoCita::Completada->value,
        ]);

        $this->actingAs($e['admin'])
            ->get(route('alumnos.show', $e['alumno']))
            ->assertOk()
            ->assertSee('Agendar clase extra')
            ->assertSee('Historial de asistencia')
            ->assertSee('1 asistencia(s) · 1 falta(s) en total')
            ->assertSeeInOrder(['● Falta', '● Asistió']);
    }

    public function test_el_codigo_qr_del_alumno_usa_la_direccion_corta_y_la_larga_sigue_funcionando(): void
    {
        $e = $this->crearEscenario();
        $alumno = $e['alumno'];

        $this->assertStringEndsWith('/q/'.$alumno->qr_token, $alumno->qrUrl());

        $this->actingAs($e['admin'])->get($alumno->qrUrl())->assertOk()->assertSee($alumno->nombreCompleto());
        $this->actingAs($e['admin'])
            ->get(route('asistencia.registrar', $alumno->qr_token))
            ->assertOk()
            ->assertSee($alumno->nombreCompleto());
    }
}
