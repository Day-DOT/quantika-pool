<?php

namespace Tests\Feature;

use App\Enums\EstadoPago;
use App\Models\Alumno;
use App\Models\Pago;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPagosTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_ve_el_index_de_pagos(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();
        $alumno = Alumno::factory()->create(['sucursal_id' => $sucursal->id]);

        Pago::factory()->create([
            'alumno_id' => $alumno->id,
            'sucursal_id' => $sucursal->id,
            'estado' => EstadoPago::Pagado->value,
            'fecha_pago' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('pagos.index'));

        $response->assertOk();
    }

    public function test_super_admin_ve_el_index_de_pagos_de_todas_las_sucursales(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $sucursal1 = Sucursal::factory()->create();
        $sucursal2 = Sucursal::factory()->create();
        $alumno1 = Alumno::factory()->create(['sucursal_id' => $sucursal1->id]);
        $alumno2 = Alumno::factory()->create(['sucursal_id' => $sucursal2->id]);

        Pago::factory()->create([
            'alumno_id' => $alumno1->id,
            'sucursal_id' => $sucursal1->id,
            'estado' => EstadoPago::Pendiente->value,
            'fecha_vencimiento' => now()->addDays(3)->toDateString(),
            'fecha_pago' => null,
            'metodo_pago' => null,
        ]);

        Pago::factory()->create([
            'alumno_id' => $alumno2->id,
            'sucursal_id' => $sucursal2->id,
            'estado' => EstadoPago::Pendiente->value,
            'fecha_vencimiento' => now()->addDays(3)->toDateString(),
            'fecha_pago' => null,
            'metodo_pago' => null,
        ]);

        $response = $this->actingAs($superAdmin)->get(route('pagos.index'));

        $response->assertOk();
        $response->assertSee($alumno1->nombreCompleto());
        $response->assertSee($alumno2->nombreCompleto());
    }

    public function test_admin_registra_un_pago(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();
        $alumno = Alumno::factory()->create(['sucursal_id' => $sucursal->id]);

        $response = $this->actingAs($admin)->post(route('pagos.store'), [
            'alumno_id' => $alumno->id,
            'concepto' => 'mensualidad',
            'periodo' => '2026-08',
            'monto' => 650,
            'fecha_vencimiento' => now()->addDays(5)->toDateString(),
            'estado' => 'pendiente',
        ]);

        $response->assertRedirect(route('pagos.alumno', $alumno));

        $this->assertDatabaseHas('pagos', [
            'alumno_id' => $alumno->id,
            'sucursal_id' => $sucursal->id,
            'monto' => 650,
            'estado' => 'pendiente',
        ]);
    }

    public function test_admin_ve_los_pagos_de_un_alumno(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();
        $alumno = Alumno::factory()->create(['sucursal_id' => $sucursal->id]);
        Pago::factory()->create(['alumno_id' => $alumno->id, 'sucursal_id' => $sucursal->id]);

        $response = $this->actingAs($admin)->get(route('pagos.alumno', $alumno));

        $response->assertOk();
        $response->assertSee($alumno->nombreCompleto());
    }

    public function test_admin_ve_deudores_con_saldo_vencido(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();
        $alumno = Alumno::factory()->create(['sucursal_id' => $sucursal->id]);

        Pago::factory()->create([
            'alumno_id' => $alumno->id,
            'sucursal_id' => $sucursal->id,
            'estado' => EstadoPago::Vencido->value,
            'fecha_vencimiento' => now()->subDays(3)->toDateString(),
        ]);

        $response = $this->actingAs($admin)->get(route('pagos.deudores'));

        $response->assertOk();
        $response->assertSee($alumno->nombreCompleto());
    }

    public function test_admin_ve_pagos_proximos_a_vencer_en_el_index(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();
        $alumno = Alumno::factory()->create(['sucursal_id' => $sucursal->id]);

        Pago::factory()->create([
            'alumno_id' => $alumno->id,
            'sucursal_id' => $sucursal->id,
            'estado' => EstadoPago::Pendiente->value,
            'fecha_vencimiento' => now()->addDays(3)->toDateString(),
            'fecha_pago' => null,
            'metodo_pago' => null,
        ]);

        $response = $this->actingAs($admin)->get(route('pagos.index'));

        $response->assertOk();
        $response->assertSee('Pagos próximos a vencer');
        $response->assertSee($alumno->nombreCompleto());
    }

    public function test_pago_vencido_no_aparece_como_proximo_a_vencer(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();
        $alumno = Alumno::factory()->create(['sucursal_id' => $sucursal->id]);

        // Ya vencido: debe aparecer solo en "deudores", no en "próximos a vencer".
        Pago::factory()->create([
            'alumno_id' => $alumno->id,
            'sucursal_id' => $sucursal->id,
            'estado' => EstadoPago::Pendiente->value,
            'fecha_vencimiento' => now()->subDay()->toDateString(),
            'fecha_pago' => null,
            'metodo_pago' => null,
        ]);

        $response = $this->actingAs($admin)->get(route('pagos.index'));

        $response->assertOk();
        $response->assertDontSee('Pagos próximos a vencer');
    }

    public function test_admin_ve_alumno_proximo_a_su_pago_estimado_desde_su_ultimo_pago(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();
        $alumno = Alumno::factory()->create(['sucursal_id' => $sucursal->id]);

        // Su último pago venció hace casi un mes: un mes después cae
        // dentro de los próximos 7 días.
        Pago::factory()->create([
            'alumno_id' => $alumno->id,
            'sucursal_id' => $sucursal->id,
            'estado' => EstadoPago::Pagado->value,
            'fecha_vencimiento' => now()->addDays(3)->subMonthNoOverflow()->toDateString(),
            'fecha_pago' => now()->subMonthNoOverflow()->toDateString(),
        ]);

        $response = $this->actingAs($admin)->get(route('pagos.index'));

        $response->assertOk();
        $response->assertSee('Alumnos próximos a su nuevo pago');
        $response->assertSee($alumno->nombreCompleto());
    }

    public function test_admin_ve_alumno_proximo_a_su_primer_pago_basado_en_fecha_de_inscripcion(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();

        // Sin pagos registrados todavía: se estima un mes después de su
        // inscripción, que cae dentro de los próximos 7 días.
        $alumno = Alumno::factory()->create([
            'sucursal_id' => $sucursal->id,
            'fecha_inscripcion' => now()->addDays(2)->subMonthNoOverflow()->toDateString(),
        ]);

        $response = $this->actingAs($admin)->get(route('pagos.index'));

        $response->assertOk();
        $response->assertSee($alumno->nombreCompleto());
    }

    public function test_un_alumno_con_proximo_pago_lejano_no_aparece_en_la_lista(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();
        $alumno = Alumno::factory()->create(['sucursal_id' => $sucursal->id]);

        Pago::factory()->create([
            'alumno_id' => $alumno->id,
            'sucursal_id' => $sucursal->id,
            'estado' => EstadoPago::Pagado->value,
            'fecha_vencimiento' => now()->addDays(20)->subMonthNoOverflow()->toDateString(),
            'fecha_pago' => now()->subMonthNoOverflow()->toDateString(),
        ]);

        $response = $this->actingAs($admin)->get(route('pagos.index'));

        $response->assertOk();
        $response->assertDontSee($alumno->nombreCompleto());
    }

    public function test_admin_marca_un_pago_como_pagado(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();
        $alumno = Alumno::factory()->create(['sucursal_id' => $sucursal->id]);

        $pago = Pago::factory()->create([
            'alumno_id' => $alumno->id,
            'sucursal_id' => $sucursal->id,
            'estado' => EstadoPago::Pendiente->value,
        ]);

        $this->actingAs($admin)->patch(route('pagos.marcar-pagado', $pago))->assertRedirect();

        $pago->refresh();
        $this->assertEquals(EstadoPago::Pagado, $pago->estado);
        $this->assertNotNull($pago->fecha_pago);
    }

    public function test_admin_puede_eliminar_un_pago_vencido_no_pagado(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();
        $alumno = Alumno::factory()->create(['sucursal_id' => $sucursal->id]);

        $pago = Pago::factory()->create([
            'alumno_id' => $alumno->id,
            'sucursal_id' => $sucursal->id,
            'estado' => EstadoPago::Vencido->value,
        ]);

        $this->actingAs($admin)->delete(route('pagos.destroy', $pago))->assertRedirect();

        $this->assertDatabaseMissing('pagos', ['id' => $pago->id]);
    }

    public function test_no_se_puede_eliminar_un_pago_ya_pagado(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();
        $alumno = Alumno::factory()->create(['sucursal_id' => $sucursal->id]);

        $pago = Pago::factory()->create([
            'alumno_id' => $alumno->id,
            'sucursal_id' => $sucursal->id,
            'estado' => EstadoPago::Pagado->value,
        ]);

        $this->actingAs($admin)->delete(route('pagos.destroy', $pago))->assertSessionHasErrors('pago');

        $this->assertDatabaseHas('pagos', ['id' => $pago->id]);
    }
}
