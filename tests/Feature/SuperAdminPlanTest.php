<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminPlanTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_puede_crear_un_plan(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)->post(route('super-admin.planes.store'), [
            'nombre' => 'Plan 3 clases/semana',
            'clases_por_semana' => 3,
            'precio' => 1100,
            'activo' => '1',
        ])->assertRedirect(route('super-admin.planes.index'));

        $this->assertDatabaseHas('planes', [
            'nombre' => 'Plan 3 clases/semana',
            'clases_por_semana' => 3,
        ]);
    }

    public function test_no_se_puede_crear_un_plan_con_clases_por_semana_invalidas(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)->post(route('super-admin.planes.store'), [
            'nombre' => 'Plan inválido',
            'clases_por_semana' => 8,
            'activo' => '1',
        ])->assertSessionHasErrors('clases_por_semana');

        $this->assertDatabaseMissing('planes', ['nombre' => 'Plan inválido']);
    }

    public function test_se_puede_crear_un_plan_con_cualquier_numero_de_dias_de_la_semana(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)->post(route('super-admin.planes.store'), [
            'nombre' => 'Plan 5 clases/semana',
            'clases_por_semana' => 5,
            'precio' => 1800,
            'activo' => '1',
        ])->assertRedirect(route('super-admin.planes.index'));

        $this->assertDatabaseHas('planes', [
            'nombre' => 'Plan 5 clases/semana',
            'clases_por_semana' => 5,
        ]);
    }

    public function test_super_admin_puede_editar_un_plan(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $plan = Plan::factory()->create(['clases_por_semana' => 2]);

        $this->actingAs($superAdmin)->put(route('super-admin.planes.update', $plan), [
            'nombre' => 'Plan actualizado',
            'clases_por_semana' => 3,
            'precio' => 1200,
            'activo' => '1',
        ])->assertRedirect(route('super-admin.planes.index'));

        $plan->refresh();
        $this->assertSame('Plan actualizado', $plan->nombre);
        $this->assertSame(3, $plan->clases_por_semana);
    }

    public function test_un_admin_normal_no_puede_crear_planes(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();

        $this->actingAs($admin)->post(route('super-admin.planes.store'), [
            'nombre' => 'Plan de admin',
            'clases_por_semana' => 2,
            'activo' => '1',
        ])->assertForbidden();

        $this->assertDatabaseMissing('planes', ['nombre' => 'Plan de admin']);
    }

    public function test_un_admin_normal_no_puede_ver_el_panel_de_gestion_de_planes(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();
        Plan::factory()->create(['nombre' => 'Plan visible']);

        // El panel de gestión de planes vive bajo el grupo de rutas de
        // Super Admin (igual que Niveles/Criterios); un Admin normal sí
        // puede elegir un plan para un alumno desde el formulario de
        // Alumnos, pero no tiene acceso a este panel de administración.
        $this->actingAs($admin)->get(route('super-admin.planes.index'))
            ->assertForbidden();
    }
}
