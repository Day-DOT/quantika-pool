<?php

namespace Tests\Feature;

use App\Enums\EstadoCita;
use App\Models\Alumno;
use App\Models\Carril;
use App\Models\Cita;
use App\Models\Horario;
use App\Models\Instructor;
use App\Models\Nivel;
use App\Models\Plan;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AdminCitaReagendarTest extends TestCase
{
    use RefreshDatabase;

    private function crearHorario(Sucursal $sucursal, Nivel $nivel, int $diaSemana): Horario
    {
        return Horario::factory()->create([
            'sucursal_id' => $sucursal->id,
            'instructor_id' => Instructor::factory()->create(['sucursal_id' => $sucursal->id])->id,
            'carril_id' => Carril::factory()->create(['sucursal_id' => $sucursal->id])->id,
            'nivel_id' => $nivel->id,
            'dia_semana' => $diaSemana,
            'activo' => true,
        ]);
    }

    public function test_admin_reagenda_la_clase_de_un_alumno_a_otro_horario(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();
        $nivel = Nivel::factory()->create();
        $alumno = Alumno::factory()->create(['sucursal_id' => $sucursal->id]);

        $horarioViejo = $this->crearHorario($sucursal, $nivel, diaSemana: 1);
        $horarioNuevo = $this->crearHorario($sucursal, $nivel, diaSemana: 3);

        $lunes = Carbon::now()->next(1)->toDateString();
        $miercoles = Carbon::now()->next(3)->toDateString();

        $cita = Cita::factory()->create([
            'horario_id' => $horarioViejo->id,
            'alumno_id' => $alumno->id,
            'sucursal_id' => $sucursal->id,
            'fecha' => $lunes,
            'estado' => EstadoCita::Programada->value,
        ]);

        $response = $this->actingAs($admin)->patch(route('citas.reagendar', $cita), [
            'horario_id' => $horarioNuevo->id,
            'fecha' => $miercoles,
        ]);

        $response->assertRedirect();
        $response->assertSessionDoesntHaveErrors();

        $cita->refresh();
        $this->assertEquals($horarioNuevo->id, $cita->horario_id);
        $this->assertEquals($miercoles, $cita->fecha->toDateString());
    }

    public function test_no_se_puede_reagendar_si_el_alumno_ya_agoto_su_limite_semanal_del_plan(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();
        $nivel = Nivel::factory()->create();
        $plan = Plan::factory()->create(['clases_por_semana' => 2]);
        $alumno = Alumno::factory()->create(['sucursal_id' => $sucursal->id, 'plan_id' => $plan->id]);

        $horarioViejo = $this->crearHorario($sucursal, $nivel, diaSemana: 1);
        $horarioNuevo = $this->crearHorario($sucursal, $nivel, diaSemana: 3);

        $inicioSemana = Carbon::now()->startOfWeek(Carbon::MONDAY);

        // El alumno ya tiene 2 citas esta semana (su límite según el plan).
        Cita::factory()->create([
            'horario_id' => $horarioViejo->id,
            'alumno_id' => $alumno->id,
            'sucursal_id' => $sucursal->id,
            'fecha' => $inicioSemana->copy()->addDays(0)->toDateString(),
            'estado' => EstadoCita::Programada->value,
        ]);

        $citaAReagendar = Cita::factory()->create([
            'horario_id' => $horarioViejo->id,
            'alumno_id' => $alumno->id,
            'sucursal_id' => $sucursal->id,
            'fecha' => $inicioSemana->copy()->addDays(2)->toDateString(),
            'estado' => EstadoCita::Programada->value,
        ]);

        $response = $this->actingAs($admin)->patch(route('citas.reagendar', $citaAReagendar), [
            'horario_id' => $horarioNuevo->id,
            'fecha' => $inicioSemana->copy()->addDays(2)->toDateString(),
        ]);

        $response->assertSessionHasErrors('fecha');

        $this->assertEquals($horarioViejo->id, $citaAReagendar->refresh()->horario_id);
    }

    public function test_no_se_puede_reagendar_a_un_horario_de_otra_sucursal(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();
        $otraSucursal = Sucursal::factory()->create();
        $nivel = Nivel::factory()->create();
        $alumno = Alumno::factory()->create(['sucursal_id' => $sucursal->id]);

        $horarioViejo = $this->crearHorario($sucursal, $nivel, diaSemana: 1);
        $horarioOtraSucursal = $this->crearHorario($otraSucursal, $nivel, diaSemana: 3);

        $cita = Cita::factory()->create([
            'horario_id' => $horarioViejo->id,
            'alumno_id' => $alumno->id,
            'sucursal_id' => $sucursal->id,
            'fecha' => Carbon::now()->next(1)->toDateString(),
            'estado' => EstadoCita::Programada->value,
        ]);

        $response = $this->actingAs($admin)->patch(route('citas.reagendar', $cita), [
            'horario_id' => $horarioOtraSucursal->id,
            'fecha' => Carbon::now()->next(3)->toDateString(),
        ]);

        $response->assertSessionHasErrors('horario_id');
        $this->assertEquals($horarioViejo->id, $cita->refresh()->horario_id);
    }
}
