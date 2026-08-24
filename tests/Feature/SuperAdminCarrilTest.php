<?php

namespace Tests\Feature;

use App\Models\Carril;
use App\Models\Horario;
use App\Models\Sucursal;
use App\Models\User;
use App\Support\SucursalContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminCarrilTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_puede_crear_un_carril_para_una_sucursal(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $sucursal = Sucursal::factory()->create();

        $this->actingAs($superAdmin)->post(route('super-admin.carriles.store'), [
            'sucursal_id' => $sucursal->id,
            'nombre' => 'Carril 9',
            'capacidad_maxima' => 6,
            'activo' => '1',
        ])->assertRedirect(route('super-admin.carriles.index'));

        $this->assertDatabaseHas('carriles', ['nombre' => 'Carril 9', 'sucursal_id' => $sucursal->id]);
    }

    public function test_el_selector_principal_de_sucursal_filtra_el_listado_de_carriles(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $sucursal = Sucursal::factory()->create();
        $otraSucursal = Sucursal::factory()->create();

        $carrilPropio = Carril::factory()->create(['sucursal_id' => $sucursal->id, 'nombre' => 'Carril propio']);
        $carrilAjeno = Carril::factory()->create(['sucursal_id' => $otraSucursal->id, 'nombre' => 'Carril ajeno']);

        SucursalContext::establecer($sucursal->id);

        $response = $this->actingAs($superAdmin)->get(route('super-admin.carriles.index'));

        $response->assertOk();
        $response->assertSee('Carril propio');
        $response->assertDontSee('Carril ajeno');
    }

    public function test_no_se_puede_repetir_el_nombre_de_carril_en_la_misma_sucursal(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $sucursal = Sucursal::factory()->create();
        Carril::factory()->create(['sucursal_id' => $sucursal->id, 'nombre' => 'Carril 1']);

        $this->actingAs($superAdmin)->post(route('super-admin.carriles.store'), [
            'sucursal_id' => $sucursal->id,
            'nombre' => 'Carril 1',
            'capacidad_maxima' => 6,
            'activo' => '1',
        ])->assertSessionHasErrors('nombre');
    }

    public function test_no_se_puede_eliminar_un_carril_con_horarios_asignados(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $carril = Carril::factory()->create();
        Horario::factory()->create(['carril_id' => $carril->id, 'sucursal_id' => $carril->sucursal_id]);

        $this->actingAs($superAdmin)
            ->delete(route('super-admin.carriles.destroy', $carril))
            ->assertSessionHasErrors('carril');

        $this->assertDatabaseHas('carriles', ['id' => $carril->id]);
    }

    public function test_super_admin_puede_eliminar_un_carril_sin_horarios(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $carril = Carril::factory()->create();

        $this->actingAs($superAdmin)
            ->delete(route('super-admin.carriles.destroy', $carril))
            ->assertRedirect(route('super-admin.carriles.index'));

        $this->assertDatabaseMissing('carriles', ['id' => $carril->id]);
    }
}
