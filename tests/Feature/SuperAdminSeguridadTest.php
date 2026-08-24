<?php

namespace Tests\Feature;

use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SuperAdminSeguridadTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_ve_la_pagina_de_seguridad(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)
            ->get(route('super-admin.seguridad.index'))
            ->assertOk()
            ->assertSee('Seguridad');
    }

    public function test_super_admin_puede_actualizar_su_contrasena(): void
    {
        $superAdmin = User::factory()->superAdmin()->create([
            'password' => Hash::make('contrasena-actual'),
        ]);

        $response = $this->actingAs($superAdmin)->put(route('super-admin.seguridad.update'), [
            'password_actual' => 'contrasena-actual',
            'password' => 'nueva-contrasena-123',
            'password_confirmation' => 'nueva-contrasena-123',
        ]);

        $response->assertRedirect(route('super-admin.seguridad.index'));

        $this->assertTrue(Hash::check('nueva-contrasena-123', $superAdmin->refresh()->password));
    }

    public function test_no_se_puede_actualizar_la_contrasena_con_la_actual_incorrecta(): void
    {
        $superAdmin = User::factory()->superAdmin()->create([
            'password' => Hash::make('contrasena-actual'),
        ]);

        $this->actingAs($superAdmin)->put(route('super-admin.seguridad.update'), [
            'password_actual' => 'contrasena-incorrecta',
            'password' => 'nueva-contrasena-123',
            'password_confirmation' => 'nueva-contrasena-123',
        ])->assertSessionHasErrors('password_actual');

        $this->assertTrue(Hash::check('contrasena-actual', $superAdmin->refresh()->password));
    }

    public function test_un_admin_normal_no_puede_acceder_a_la_pagina_de_seguridad_de_super_admin(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();

        $this->actingAs($admin)
            ->get(route('super-admin.seguridad.index'))
            ->assertForbidden();
    }
}
