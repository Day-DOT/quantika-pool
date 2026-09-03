<?php

namespace Tests\Feature;

use App\Enums\ConceptoPago;
use App\Enums\EstadoAlumno;
use App\Enums\EstadoPago;
use App\Models\Alumno;
use App\Models\Pago;
use App\Models\Plan;
use App\Models\Sucursal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenerarMensualidadesPendientesTest extends TestCase
{
    use RefreshDatabase;

    public function test_genera_la_mensualidad_cuando_ya_llego_la_fecha_de_corte(): void
    {
        $sucursal = Sucursal::factory()->create();
        $plan = Plan::factory()->create(['precio' => 950]);

        $alumno = Alumno::factory()->create([
            'sucursal_id' => $sucursal->id,
            'plan_id' => $plan->id,
            'estado' => EstadoAlumno::Activo->value,
            'fecha_inscripcion' => now()->subMonthNoOverflow()->toDateString(),
        ]);

        $this->artisan('pagos:generar-mensualidades')->assertExitCode(0);

        $this->assertDatabaseHas('pagos', [
            'alumno_id' => $alumno->id,
            'concepto' => ConceptoPago::Mensualidad->value,
            'estado' => EstadoPago::Pendiente->value,
            'monto' => '950.00',
        ]);
    }

    public function test_no_genera_nada_si_la_fecha_de_corte_todavia_no_llega(): void
    {
        $sucursal = Sucursal::factory()->create();
        $plan = Plan::factory()->create();

        $alumno = Alumno::factory()->create([
            'sucursal_id' => $sucursal->id,
            'plan_id' => $plan->id,
            'estado' => EstadoAlumno::Activo->value,
            'fecha_inscripcion' => now()->toDateString(),
        ]);

        $this->artisan('pagos:generar-mensualidades');

        $this->assertDatabaseMissing('pagos', ['alumno_id' => $alumno->id]);
    }

    public function test_no_duplica_la_mensualidad_si_ya_se_genero(): void
    {
        $sucursal = Sucursal::factory()->create();
        $plan = Plan::factory()->create(['precio' => 950]);

        $alumno = Alumno::factory()->create([
            'sucursal_id' => $sucursal->id,
            'plan_id' => $plan->id,
            'estado' => EstadoAlumno::Activo->value,
            'fecha_inscripcion' => now()->subMonthNoOverflow()->toDateString(),
        ]);

        $this->artisan('pagos:generar-mensualidades');
        $this->artisan('pagos:generar-mensualidades');

        $this->assertEquals(1, Pago::where('alumno_id', $alumno->id)->count());
    }

    public function test_no_genera_nada_para_un_alumno_inactivo(): void
    {
        $sucursal = Sucursal::factory()->create();
        $plan = Plan::factory()->create();

        $alumno = Alumno::factory()->create([
            'sucursal_id' => $sucursal->id,
            'plan_id' => $plan->id,
            'estado' => EstadoAlumno::BajaTemporal->value,
            'fecha_inscripcion' => now()->subMonthNoOverflow()->toDateString(),
        ]);

        $this->artisan('pagos:generar-mensualidades');

        $this->assertDatabaseMissing('pagos', ['alumno_id' => $alumno->id]);
    }

    public function test_no_genera_nada_para_un_alumno_sin_plan(): void
    {
        $sucursal = Sucursal::factory()->create();

        $alumno = Alumno::factory()->create([
            'sucursal_id' => $sucursal->id,
            'plan_id' => null,
            'estado' => EstadoAlumno::Activo->value,
            'fecha_inscripcion' => now()->subMonthNoOverflow()->toDateString(),
        ]);

        $this->artisan('pagos:generar-mensualidades');

        $this->assertDatabaseMissing('pagos', ['alumno_id' => $alumno->id]);
    }
}
