<?php

namespace Tests\Feature;

use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminSucursalTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_puede_listar_sucursales(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sucursal::factory()->create(['nombre' => 'Sucursal Norte']);

        $this->actingAs($superAdmin)
            ->get(route('super-admin.sucursales.index'))
            ->assertOk()
            ->assertSee('Sucursal Norte');
    }

    public function test_super_admin_puede_crear_una_sucursal(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $response = $this->actingAs($superAdmin)->post(route('super-admin.sucursales.store'), [
            'nombre' => 'Sucursal Nueva',
            'codigo' => 'SUCNEW',
            'direccion' => 'Av. Siempre Viva 123',
            'telefono' => '5555555555',
            'activa' => '1',
        ]);

        $this->assertDatabaseHas('sucursales', ['codigo' => 'SUCNEW', 'nombre' => 'Sucursal Nueva']);
        $sucursal = Sucursal::where('codigo', 'SUCNEW')->first();
        $response->assertRedirect(route('super-admin.sucursales.show', $sucursal));
    }

    public function test_no_se_puede_crear_una_sucursal_con_codigo_duplicado(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sucursal::factory()->create(['codigo' => 'DUPCODE']);

        $this->actingAs($superAdmin)->post(route('super-admin.sucursales.store'), [
            'nombre' => 'Otra sucursal',
            'codigo' => 'DUPCODE',
            'activa' => '1',
        ])->assertSessionHasErrors('codigo');
    }

    public function test_super_admin_puede_actualizar_una_sucursal(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $sucursal = Sucursal::factory()->create();

        $this->actingAs($superAdmin)->put(route('super-admin.sucursales.update', $sucursal), [
            'nombre' => 'Nombre actualizado',
            'codigo' => $sucursal->codigo,
            'activa' => '1',
        ])->assertRedirect(route('super-admin.sucursales.show', $sucursal));

        $this->assertDatabaseHas('sucursales', ['id' => $sucursal->id, 'nombre' => 'Nombre actualizado']);
    }

    public function test_no_se_puede_eliminar_una_sucursal_con_datos_asociados(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $sucursal = Sucursal::factory()->create();
        User::factory()->admin($sucursal->id)->create();

        $this->actingAs($superAdmin)
            ->delete(route('super-admin.sucursales.destroy', $sucursal))
            ->assertSessionHasErrors('sucursal');

        $this->assertDatabaseHas('sucursales', ['id' => $sucursal->id]);
    }

    public function test_la_ruta_reservada_sucursal_2_muestra_la_segunda_sucursal(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sucursal::factory()->create(['codigo' => 'SUC1', 'nombre' => 'Sucursal 1']);
        Sucursal::factory()->create(['codigo' => 'SUC2', 'nombre' => 'Sucursal 2']);

        $this->actingAs($superAdmin)
            ->get(route('super-admin.sucursal-2'))
            ->assertOk()
            ->assertSee('Sucursal 2');
    }

    public function test_un_admin_no_puede_acceder_a_la_gestion_de_sucursales(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();

        $this->actingAs($admin)
            ->get(route('super-admin.sucursales.index'))
            ->assertForbidden();
    }
}
