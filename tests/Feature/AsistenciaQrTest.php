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
use App\Models\Pago;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AsistenciaQrTest extends TestCase
{
    use RefreshDatabase;

    private function crearAlumnoConClaseHoy(Sucursal $sucursal, Instructor $instructor): Alumno
    {
        $nivel = Nivel::factory()->create();
        $carril = Carril::factory()->create(['sucursal_id' => $sucursal->id]);

        $horario = Horario::factory()->create([
            'sucursal_id' => $sucursal->id,
            'instructor_id' => $instructor->id,
            'carril_id' => $carril->id,
            'nivel_id' => $nivel->id,
            'dia_semana' => now()->dayOfWeekIso,
            'activo' => true,
        ]);

        $alumno = Alumno::factory()->create(['sucursal_id' => $sucursal->id]);

        Inscripcion::factory()->create([
            'horario_id' => $horario->id,
            'alumno_id' => $alumno->id,
            'activa' => true,
        ]);

        return $alumno;
    }

    public function test_instructor_no_puede_acceder_a_la_pagina_de_escanear(): void
    {
        $sucursal = Sucursal::factory()->create();
        $instructor = Instructor::factory()->create(['sucursal_id' => $sucursal->id]);

        $this->actingAs($instructor->user)->get(route('asistencia.escanear'))->assertForbidden();
    }

    public function test_instructor_no_puede_registrar_asistencia_via_qr(): void
    {
        $sucursal = Sucursal::factory()->create();
        $instructor = Instructor::factory()->create(['sucursal_id' => $sucursal->id]);
        $alumno = $this->crearAlumnoConClaseHoy($sucursal, $instructor);

        $this->actingAs($instructor->user)->get(route('asistencia.registrar', $alumno->qr_token))->assertForbidden();

        $this->assertDatabaseMissing('citas', ['alumno_id' => $alumno->id]);
    }

    public function test_admin_registra_asistencia_de_un_alumno_de_su_sucursal(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();
        $instructor = Instructor::factory()->create(['sucursal_id' => $sucursal->id]);
        $alumno = $this->crearAlumnoConClaseHoy($sucursal, $instructor);

        $response = $this->actingAs($admin)->get(route('asistencia.registrar', $alumno->qr_token));

        $response->assertOk();
        $response->assertSee('Registrar asistencia');
        $this->assertDatabaseMissing('citas', ['alumno_id' => $alumno->id]);

        $response = $this->actingAs($admin)->post(route('asistencia.confirmar', $alumno->qr_token));
        $response->assertOk();
        $response->assertSee('Asistencia registrada');

        $this->assertDatabaseHas('citas', [
            'alumno_id' => $alumno->id,
            'asistio' => 1,
        ]);
    }

    public function test_al_escanear_se_muestra_el_carril_y_el_instructor_de_la_clase_de_hoy(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();
        $instructor = Instructor::factory()->create(['sucursal_id' => $sucursal->id]);
        $alumno = $this->crearAlumnoConClaseHoy($sucursal, $instructor);

        $horario = Horario::where('instructor_id', $instructor->id)->sole();
        $horario->carril->update(['nombre' => 'Carril 3']);

        foreach ([
            $this->actingAs($admin)->get($alumno->qrUrl()),
            $this->actingAs($admin)->post(route('asistencia.confirmar', $alumno->qr_token)),
        ] as $respuesta) {
            $respuesta->assertOk()
                ->assertSee('Carril 3')
                ->assertSee($instructor->user->name);
        }
    }

    public function test_la_foto_del_alumno_se_puede_tomar_o_elegir_de_la_galeria(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();
        $alumno = Alumno::factory()->create(['sucursal_id' => $sucursal->id]);

        // Con el atributo "capture" el celular solo deja usar la cámara.
        foreach ([route('alumnos.index'), route('alumnos.edit', $alumno)] as $pagina) {
            $this->actingAs($admin)
                ->get($pagina)
                ->assertOk()
                ->assertSee('name="foto"', false)
                ->assertDontSee('capture=', false);
        }
    }

    public function test_al_escanear_se_avisa_si_la_mensualidad_esta_vencida_o_al_corriente(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();
        $instructor = Instructor::factory()->create(['sucursal_id' => $sucursal->id]);
        $alumno = $this->crearAlumnoConClaseHoy($sucursal, $instructor);

        $this->actingAs($admin)->get($alumno->qrUrl())
            ->assertOk()
            ->assertSee('Pagos al corriente');

        Pago::factory()->create([
            'alumno_id' => $alumno->id,
            'sucursal_id' => $sucursal->id,
            'concepto' => 'mensualidad',
            'estado' => 'pendiente',
            'monto' => 990,
            'fecha_vencimiento' => now()->subDays(6)->toDateString(),
        ]);

        $aviso = 'Mensualidad vencida desde el '.now()->subDays(6)->format('d/m/Y').' · debe $990.00';

        $this->actingAs($admin)->get($alumno->qrUrl())->assertOk()->assertSee($aviso);

        // También después de registrar la asistencia.
        $this->actingAs($admin)
            ->post(route('asistencia.confirmar', $alumno->qr_token))
            ->assertOk()
            ->assertSee($aviso);
    }

    public function test_al_escanear_se_avisa_si_la_mensualidad_vence_en_los_proximos_dias(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();
        $instructor = Instructor::factory()->create(['sucursal_id' => $sucursal->id]);
        $alumno = $this->crearAlumnoConClaseHoy($sucursal, $instructor);

        Pago::factory()->create([
            'alumno_id' => $alumno->id,
            'sucursal_id' => $sucursal->id,
            'concepto' => 'mensualidad',
            'estado' => 'pendiente',
            'monto' => 1100,
            'fecha_vencimiento' => now()->addDays(3)->toDateString(),
        ]);

        $this->actingAs($admin)->get($alumno->qrUrl())
            ->assertOk()
            ->assertSee('Su mensualidad vence el '.now()->addDays(3)->format('d/m/Y'));
    }

    public function test_alumno_sin_clase_programada_hoy_muestra_mensaje(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();
        $alumno = Alumno::factory()->create(['sucursal_id' => $sucursal->id]);

        $response = $this->actingAs($admin)->get(route('asistencia.registrar', $alumno->qr_token));

        $response->assertOk();
        $response->assertSee('no tiene clase programada para hoy');
    }

    public function test_token_invalido_muestra_mensaje_de_error(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();

        $response = $this->actingAs($admin)->get(route('asistencia.registrar', 'token-que-no-existe'));

        $response->assertOk();
        $response->assertSee('no es válido');
    }

    public function test_un_alumno_tutor_no_puede_acceder_a_la_pagina_de_escanear(): void
    {
        $tutor = User::factory()->tutor()->create();

        $this->actingAs($tutor)->get(route('asistencia.escanear'))->assertForbidden();
    }

    public function test_tutor_ve_el_codigo_qr_de_su_alumno(): void
    {
        $tutor = User::factory()->tutor()->create();
        $alumno = Alumno::factory()->create(['tutor_user_id' => $tutor->id]);

        $response = $this->actingAs($tutor)->get(route('portal.qr'));

        $response->assertOk();
        $response->assertSee($alumno->nombreCompleto());
        $response->assertSee($alumno->qr_token);
    }
}
