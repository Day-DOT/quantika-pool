<?php

namespace Tests\Feature;

use App\Models\Alumno;
use App\Models\Carril;
use App\Models\Cita;
use App\Models\Horario;
use App\Models\Inscripcion;
use App\Models\Instructor;
use App\Models\Nivel;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminReservaTest extends TestCase
{
    use RefreshDatabase;

    private function crearEscenario(int $capacidad = 4): array
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();
        $instructor = Instructor::factory()->create(['sucursal_id' => $sucursal->id]);
        $carril = Carril::factory()->create(['sucursal_id' => $sucursal->id]);
        $nivel = Nivel::factory()->create();

        $horario = Horario::factory()->create([
            'sucursal_id' => $sucursal->id,
            'instructor_id' => $instructor->id,
            'carril_id' => $carril->id,
            'nivel_id' => $nivel->id,
            'capacidad_maxima' => $capacidad,
            'dia_semana' => 2,
            'activo' => true,
        ]);

        $alumno = Alumno::factory()->create(['sucursal_id' => $sucursal->id]);

        $inscripcion = Inscripcion::factory()->create([
            'horario_id' => $horario->id,
            'alumno_id' => $alumno->id,
            'activa' => false,
            'estado' => 'pendiente',
        ]);

        return compact('sucursal', 'admin', 'horario', 'alumno', 'inscripcion');
    }

    public function test_admin_ve_las_reservas_pendientes(): void
    {
        $e = $this->crearEscenario();

        $this->actingAs($e['admin'])->get(route('reservas.index'))
            ->assertOk()
            ->assertSee($e['alumno']->nombreCompleto());
    }

    public function test_admin_aprueba_una_reserva_pendiente(): void
    {
        $e = $this->crearEscenario();

        $response = $this->actingAs($e['admin'])->patch(route('reservas.aprobar', $e['inscripcion']));

        $response->assertRedirect(route('reservas.index'));

        $this->assertDatabaseHas('inscripciones', [
            'id' => $e['inscripcion']->id,
            'activa' => 1,
            'estado' => 'aprobada',
        ]);

        $this->assertGreaterThan(
            0,
            Cita::where('horario_id', $e['horario']->id)->where('alumno_id', $e['alumno']->id)->count()
        );
    }

    public function test_no_se_puede_aprobar_una_reserva_si_ya_no_hay_cupo(): void
    {
        $e = $this->crearEscenario(capacidad: 1);

        // Otro alumno ya ocupa el único lugar disponible.
        $otroAlumno = Alumno::factory()->create(['sucursal_id' => $e['sucursal']->id]);
        Inscripcion::factory()->create([
            'horario_id' => $e['horario']->id,
            'alumno_id' => $otroAlumno->id,
            'activa' => true,
        ]);

        $response = $this->actingAs($e['admin'])->patch(route('reservas.aprobar', $e['inscripcion']));

        $response->assertSessionHasErrors();

        $this->assertDatabaseHas('inscripciones', [
            'id' => $e['inscripcion']->id,
            'activa' => 0,
            'estado' => 'pendiente',
        ]);
    }

    public function test_admin_rechaza_una_reserva_pendiente(): void
    {
        $e = $this->crearEscenario();

        $response = $this->actingAs($e['admin'])->patch(route('reservas.rechazar', $e['inscripcion']));

        $response->assertRedirect(route('reservas.index'));

        $this->assertDatabaseHas('inscripciones', [
            'id' => $e['inscripcion']->id,
            'activa' => 0,
            'estado' => 'rechazada',
        ]);

        $this->assertSame(
            0,
            Cita::where('horario_id', $e['horario']->id)->where('alumno_id', $e['alumno']->id)->count()
        );
    }

    public function test_un_admin_de_otra_sucursal_no_puede_aprobar_la_reserva(): void
    {
        $e = $this->crearEscenario();
        $otraSucursal = Sucursal::factory()->create();
        $adminAjeno = User::factory()->admin($otraSucursal->id)->create();

        $this->actingAs($adminAjeno)
            ->patch(route('reservas.aprobar', $e['inscripcion']))
            ->assertForbidden();

        $this->assertDatabaseHas('inscripciones', [
            'id' => $e['inscripcion']->id,
            'estado' => 'pendiente',
        ]);
    }
}
