<?php

namespace Tests\Feature;

use App\Models\Alumno;
use App\Models\Carril;
use App\Models\Horario;
use App\Models\Inscripcion;
use App\Models\Instructor;
use App\Models\Nivel;
use App\Models\Sucursal;
use App\Models\User;
use App\Support\SucursalContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_ve_el_dashboard_global_por_defecto(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $sucursal1 = Sucursal::factory()->create(['nombre' => 'Sucursal 1']);
        $sucursal2 = Sucursal::factory()->create(['nombre' => 'Sucursal 2']);

        Alumno::factory()->create(['sucursal_id' => $sucursal1->id]);
        Alumno::factory()->create(['sucursal_id' => $sucursal2->id]);

        $response = $this->actingAs($superAdmin)->get(route('super-admin.dashboard'));

        $response->assertOk();
        $response->assertSee('Todas las sucursales');
        $response->assertSee($sucursal1->nombre);
        $response->assertSee($sucursal2->nombre);
    }

    public function test_super_admin_puede_cambiar_la_sucursal_actual_desde_el_selector(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $sucursal = Sucursal::factory()->create();

        $this->actingAs($superAdmin)
            ->post(route('super-admin.sucursal-actual'), ['sucursal_id' => $sucursal->id])
            ->assertRedirect();

        $this->assertSame($sucursal->id, SucursalContext::actualId());

        $response = $this->actingAs($superAdmin)->get(route('super-admin.dashboard'));
        $response->assertOk();
        $response->assertSee('VISTA FILTRADA');
        $response->assertDontSee('VISTA CONSOLIDADA');
    }

    public function test_super_admin_dashboard_muestra_acciones_rapidas_y_ya_no_el_boton_de_sucursales(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $response = $this->actingAs($superAdmin)->get(route('super-admin.dashboard'));

        $response->assertOk();
        $response->assertSee('Acciones rápidas');
        $response->assertSee('Registrar alumno');
        $response->assertDontSee('Gestionar sucursales');
    }

    public function test_super_admin_dashboard_muestra_el_calendario_semanal_con_horario_disponibilidad_e_instructor(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $sucursal = Sucursal::factory()->create();
        $instructor = Instructor::factory()->create(['sucursal_id' => $sucursal->id]);
        $carril = Carril::factory()->create(['sucursal_id' => $sucursal->id]);
        $nivel = Nivel::factory()->create();

        $horario = Horario::factory()->create([
            'sucursal_id' => $sucursal->id,
            'instructor_id' => $instructor->id,
            'carril_id' => $carril->id,
            'nivel_id' => $nivel->id,
            'nombre_grupo' => 'Tiburones Calendario',
            'capacidad_maxima' => 5,
            'dia_semana' => now()->dayOfWeekIso,
        ]);

        Inscripcion::factory()->create([
            'horario_id' => $horario->id,
            'alumno_id' => Alumno::factory()->create(['sucursal_id' => $sucursal->id])->id,
            'activa' => true,
        ]);

        $response = $this->actingAs($superAdmin)->get(route('super-admin.dashboard'));

        $response->assertOk();
        $response->assertSee('Calendario de clases de la semana');
        $response->assertSee('Tiburones Calendario');
        $response->assertSee($instructor->user->name);
        $response->assertSee('4/5 lugares');
    }

    public function test_super_admin_puede_regresar_a_la_vista_global(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $sucursal = Sucursal::factory()->create();

        SucursalContext::establecer($sucursal->id);

        $this->actingAs($superAdmin)
            ->post(route('super-admin.sucursal-actual'), ['sucursal_id' => ''])
            ->assertRedirect();

        $this->assertNull(SucursalContext::actualId());
    }
}
