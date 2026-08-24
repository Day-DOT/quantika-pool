<?php

namespace Tests\Feature;

use App\Models\Carril;
use App\Models\Sucursal;
use App\Models\User;
use App\Support\SucursalContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminConfiguracionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_ve_la_pagina_de_configuracion(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();

        $this->actingAs($admin)->get(route('configuracion.index'))->assertOk();
    }

    public function test_admin_ve_y_crea_carriles_de_su_sucursal(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();

        $response = $this->actingAs($admin)->get(route('carriles.index'));
        $response->assertOk();

        $store = $this->actingAs($admin)->post(route('carriles.store'), [
            'nombre' => 'Carril 5',
            'capacidad_maxima' => 8,
        ]);

        $store->assertRedirect(route('carriles.index'));

        $this->assertDatabaseHas('carriles', [
            'sucursal_id' => $sucursal->id,
            'nombre' => 'Carril 5',
        ]);
    }

    public function test_super_admin_con_sucursal_seleccionada_crea_carril_sin_enviar_sucursal_id(): void
    {
        $sucursal = Sucursal::factory()->create();
        $superAdmin = User::factory()->superAdmin()->create();
        SucursalContext::establecer($sucursal->id);

        $response = $this->actingAs($superAdmin)->post(route('carriles.store'), [
            'nombre' => 'Carril del selector',
            'capacidad_maxima' => 8,
        ]);

        $response->assertSessionDoesntHaveErrors('sucursal_id');
        $response->assertRedirect(route('carriles.index'));

        $this->assertDatabaseHas('carriles', [
            'sucursal_id' => $sucursal->id,
            'nombre' => 'Carril del selector',
        ]);
    }

    public function test_admin_actualiza_y_elimina_un_carril(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();
        $carril = Carril::factory()->create(['sucursal_id' => $sucursal->id]);

        $this->actingAs($admin)->put(route('carriles.update', $carril), [
            'nombre' => 'Carril renombrado',
            'capacidad_maxima' => 6,
        ])->assertRedirect(route('carriles.index'));

        $carril->refresh();
        $this->assertEquals('Carril renombrado', $carril->nombre);

        $this->actingAs($admin)->delete(route('carriles.destroy', $carril))
            ->assertRedirect(route('carriles.index'));

        $this->assertDatabaseMissing('carriles', ['id' => $carril->id]);
    }

    public function test_admin_de_otra_sucursal_no_puede_editar_carril_ajeno(): void
    {
        $sucursal = Sucursal::factory()->create();
        $otraSucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($otraSucursal->id)->create();
        $carril = Carril::factory()->create(['sucursal_id' => $sucursal->id]);

        $this->actingAs($admin)->put(route('carriles.update', $carril), [
            'nombre' => 'Intento ajeno',
            'capacidad_maxima' => 6,
        ])->assertForbidden();
    }
}
