<?php

namespace Tests\Feature;

use App\Enums\EstadoPago;
use App\Models\Alumno;
use App\Models\Nivel;
use App\Models\Pago;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_ve_su_dashboard_con_datos_reales(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();

        $nivel = Nivel::factory()->create([
            'imagen' => 'images/Niveles/tortuga.png',
            'color_hex' => '#16e0a4',
        ]);

        Alumno::factory()->count(3)->create([
            'sucursal_id' => $sucursal->id,
            'nivel_id' => $nivel->id,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('Dashboard');
    }

    public function test_super_admin_ve_dashboard_global(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $response = $this->actingAs($superAdmin)->get(route('admin.dashboard'));

        $response->assertOk();
    }

    public function test_instructor_no_puede_ver_el_dashboard_de_admin(): void
    {
        $instructor = User::factory()->instructor()->create();

        $this->actingAs($instructor)->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_dashboard_muestra_alerta_de_pagos_proximos_a_vencer(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();
        $alumno = Alumno::factory()->create(['sucursal_id' => $sucursal->id]);

        Pago::factory()->create([
            'alumno_id' => $alumno->id,
            'sucursal_id' => $sucursal->id,
            'estado' => EstadoPago::Pendiente->value,
            'fecha_vencimiento' => now()->addDays(2)->toDateString(),
            'fecha_pago' => null,
            'metodo_pago' => null,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('por vencer en los próximos 5 días');
        $response->assertSee($alumno->nombreCompleto());
    }

    public function test_dashboard_no_muestra_alerta_sin_pagos_proximos(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertDontSee('por vencer en los próximos');
    }

    public function test_dashboard_no_mezcla_pagos_de_otra_sucursal_en_la_alerta(): void
    {
        $sucursal = Sucursal::factory()->create();
        $otraSucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();

        $alumnoAjeno = Alumno::factory()->create(['sucursal_id' => $otraSucursal->id]);

        Pago::factory()->create([
            'alumno_id' => $alumnoAjeno->id,
            'sucursal_id' => $otraSucursal->id,
            'estado' => EstadoPago::Pendiente->value,
            'fecha_vencimiento' => now()->addDay()->toDateString(),
            'fecha_pago' => null,
            'metodo_pago' => null,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertDontSee($alumnoAjeno->nombreCompleto());
    }
}
