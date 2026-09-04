<?php

namespace Tests\Feature;

use App\Enums\EstadoCita;
use App\Models\Alumno;
use App\Models\Cita;
use App\Models\Horario;
use App\Models\Inscripcion;
use App\Models\Nivel;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReposicionesTest extends TestCase
{
    use RefreshDatabase;

    private function crearFalta(Sucursal $sucursal, ?Nivel $nivel = null, ?\Illuminate\Support\Carbon $fecha = null): array
    {
        $nivel ??= Nivel::factory()->create();
        $horarioOriginal = Horario::factory()->create(['sucursal_id' => $sucursal->id, 'nivel_id' => $nivel->id]);
        $alumno = Alumno::factory()->create(['sucursal_id' => $sucursal->id, 'nivel_id' => $nivel->id]);

        $cita = Cita::factory()->create([
            'horario_id' => $horarioOriginal->id,
            'alumno_id' => $alumno->id,
            'sucursal_id' => $sucursal->id,
            'fecha' => $fecha ?? now()->startOfMonth()->addDays(2),
            'asistio' => false,
            'estado' => EstadoCita::Completada->value,
        ]);

        return [$alumno, $cita];
    }

    public function test_admin_ve_las_faltas_pendientes_de_reposicion(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();
        [$alumno, $cita] = $this->crearFalta($sucursal);

        $response = $this->actingAs($admin)->get(route('reposiciones.index'));

        $response->assertOk();
        $response->assertSee($alumno->nombreCompleto());
    }

    public function test_admin_programa_una_reposicion_correctamente(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();
        $nivel = Nivel::factory()->create(['categoria_edad' => 'Niños']);
        [$alumno, $cita] = $this->crearFalta($sucursal, $nivel, now()->startOfMonth()->addDays(2));

        $horarioDestino = Horario::factory()->create([
            'sucursal_id' => $sucursal->id,
            'dia_semana' => now()->startOfMonth()->addDays(5)->isoWeekday(),
            'capacidad_maxima' => 6,
        ]);

        $response = $this->actingAs($admin)->post(route('citas.reponer', $cita), [
            'horario_id' => $horarioDestino->id,
            'fecha' => now()->startOfMonth()->addDays(5)->toDateString(),
        ]);

        $response->assertRedirect(route('reposiciones.index'));

        $this->assertDatabaseHas('citas', [
            'reposicion_de_id' => $cita->id,
            'alumno_id' => $alumno->id,
            'horario_id' => $horarioDestino->id,
        ]);
    }

    public function test_no_se_puede_reponer_una_clase_de_bebes(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();
        $nivelBebe = Nivel::factory()->create(['categoria_edad' => 'Bebés']);
        [$alumno, $cita] = $this->crearFalta($sucursal, $nivelBebe);

        $horarioDestino = Horario::factory()->create([
            'sucursal_id' => $sucursal->id,
            'dia_semana' => $cita->fecha->isoWeekday(),
        ]);

        $this->actingAs($admin)->post(route('citas.reponer', $cita), [
            'horario_id' => $horarioDestino->id,
            'fecha' => $cita->fecha->toDateString(),
        ])->assertSessionHasErrors('fecha');

        $this->assertDatabaseMissing('citas', ['reposicion_de_id' => $cita->id]);
    }

    public function test_no_se_puede_reponer_fuera_del_mismo_mes(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();
        $nivel = Nivel::factory()->create(['categoria_edad' => 'Niños']);
        [$alumno, $cita] = $this->crearFalta($sucursal, $nivel, now()->startOfMonth()->addDays(2));

        $fechaOtroMes = now()->startOfMonth()->addMonth()->addDays(2);
        $horarioDestino = Horario::factory()->create([
            'sucursal_id' => $sucursal->id,
            'dia_semana' => $fechaOtroMes->isoWeekday(),
        ]);

        $this->actingAs($admin)->post(route('citas.reponer', $cita), [
            'horario_id' => $horarioDestino->id,
            'fecha' => $fechaOtroMes->toDateString(),
        ])->assertSessionHasErrors('fecha');
    }

    public function test_no_se_pueden_exceder_dos_reposiciones_por_mes(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();
        $nivel = Nivel::factory()->create(['categoria_edad' => 'Niños']);

        $horarioOriginal = Horario::factory()->create(['sucursal_id' => $sucursal->id, 'nivel_id' => $nivel->id]);
        $alumno = Alumno::factory()->create(['sucursal_id' => $sucursal->id, 'nivel_id' => $nivel->id]);

        $mesInicio = now()->startOfMonth();

        // Dos reposiciones ya usadas este mes (de dos faltas distintas).
        for ($i = 0; $i < 2; $i++) {
            $faltaPrevia = Cita::factory()->create([
                'horario_id' => $horarioOriginal->id,
                'alumno_id' => $alumno->id,
                'sucursal_id' => $sucursal->id,
                'fecha' => $mesInicio->copy()->addDays($i),
                'asistio' => false,
            ]);

            Cita::factory()->create([
                'horario_id' => $horarioOriginal->id,
                'alumno_id' => $alumno->id,
                'sucursal_id' => $sucursal->id,
                'fecha' => $mesInicio->copy()->addDays($i + 10),
                'reposicion_de_id' => $faltaPrevia->id,
            ]);
        }

        $terceraFalta = Cita::factory()->create([
            'horario_id' => $horarioOriginal->id,
            'alumno_id' => $alumno->id,
            'sucursal_id' => $sucursal->id,
            'fecha' => $mesInicio->copy()->addDays(3),
            'asistio' => false,
        ]);

        $horarioDestino = Horario::factory()->create([
            'sucursal_id' => $sucursal->id,
            'dia_semana' => $mesInicio->copy()->addDays(20)->isoWeekday(),
        ]);

        $this->actingAs($admin)->post(route('citas.reponer', $terceraFalta), [
            'horario_id' => $horarioDestino->id,
            'fecha' => $mesInicio->copy()->addDays(20)->toDateString(),
        ])->assertSessionHasErrors('fecha');
    }

    public function test_no_se_puede_reponer_en_un_horario_sin_cupo(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();
        $nivel = Nivel::factory()->create(['categoria_edad' => 'Niños']);
        [$alumno, $cita] = $this->crearFalta($sucursal, $nivel, now()->startOfMonth()->addDays(2));

        $fechaDestino = now()->startOfMonth()->addDays(5);
        $horarioDestino = Horario::factory()->create([
            'sucursal_id' => $sucursal->id,
            'dia_semana' => $fechaDestino->isoWeekday(),
            'capacidad_maxima' => 1,
        ]);

        $otroAlumno = Alumno::factory()->create(['sucursal_id' => $sucursal->id]);
        Inscripcion::factory()->create([
            'horario_id' => $horarioDestino->id,
            'alumno_id' => $otroAlumno->id,
            'activa' => true,
        ]);

        $this->actingAs($admin)->post(route('citas.reponer', $cita), [
            'horario_id' => $horarioDestino->id,
            'fecha' => $fechaDestino->toDateString(),
        ])->assertSessionHasErrors('horario_id');
    }
}
