<?php

namespace Tests\Feature;

use App\Models\Alumno;
use App\Models\Pago;
use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class UltimaMensualidadTest extends TestCase
{
    use RefreshDatabase;

    /**
     * La anualidad del año siguiente vence después que cualquier mensualidad.
     * Eso dejaba vacía la "última mensualidad" y la tarea diaria creaba un
     * cargo en inscripción + 1 mes a quien ya había pagado.
     */
    public function test_una_anualidad_futura_no_hace_que_se_genere_un_cargo_desde_la_inscripcion(): void
    {
        $this->travelTo(Carbon::create(2026, 11, 22, 6));

        $alumno = Alumno::factory()->create([
            'plan_id' => Plan::factory()->create(['precio' => 650])->id,
            'estado' => 'activo',
            'fecha_inscripcion' => '2026-09-02',
        ]);
        $pago = fn (string $concepto, string $corte, string $estado) => Pago::factory()->create([
            'alumno_id' => $alumno->id, 'sucursal_id' => $alumno->sucursal_id,
            'concepto' => $concepto, 'fecha_vencimiento' => $corte, 'estado' => $estado, 'monto' => 650,
        ]);
        $pago('mensualidad', '2026-10-27', 'pagado');
        $ultima = $pago('mensualidad', '2026-11-27', 'pendiente');
        $pago('inscripcion', '2027-10-30', 'pendiente');

        $this->assertSame($ultima->id, $alumno->fresh()->ultimoPagoMensualidad?->id);
        $this->assertSame($ultima->id, Alumno::with('ultimoPagoMensualidad')->find($alumno->id)->ultimoPagoMensualidad?->id);

        $this->artisan('pagos:generar-mensualidades')->assertSuccessful();

        $this->assertSame(3, Pago::count());
        $this->assertFalse(Pago::whereDate('fecha_vencimiento', '2026-10-02')->exists());
    }
}
