<?php

namespace Tests\Feature;

use App\Enums\Rol;
use App\Models\Instructor;
use App\Models\Sucursal;
use App\Models\User;
use App\Support\SucursalContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminUsuarioTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_puede_listar_usuarios(): void
    {
        $superAdmin = User::factory()->superAdmin()->create(['name' => 'Jefe Supremo']);

        $this->actingAs($superAdmin)
            ->get(route('super-admin.usuarios.index'))
            ->assertOk()
            ->assertSee('Jefe Supremo');
    }

    public function test_super_admin_puede_crear_un_administrador_con_sucursal(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $sucursal = Sucursal::factory()->create();

        $this->actingAs($superAdmin)->post(route('super-admin.usuarios.store'), [
            'name' => 'Admin Nuevo',
            'email' => 'admin.nuevo@aqualix.test',
            'password' => 'password123',
            'role' => Rol::Admin->value,
            'sucursal_id' => $sucursal->id,
            'activo' => '1',
        ])->assertRedirect(route('super-admin.usuarios.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'admin.nuevo@aqualix.test',
            'role' => Rol::Admin->value,
            'sucursal_id' => $sucursal->id,
        ]);
    }

    public function test_super_admin_puede_crear_un_instructor_y_se_crea_su_registro_de_instructor(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $sucursal = Sucursal::factory()->create();

        $this->actingAs($superAdmin)->post(route('super-admin.usuarios.store'), [
            'name' => 'Instructor Nuevo',
            'email' => 'instructor.nuevo@aqualix.test',
            'password' => 'password123',
            'role' => Rol::Instructor->value,
            'sucursal_id' => $sucursal->id,
            'especialidad' => 'Estilo libre',
            'activo' => '1',
        ])->assertRedirect(route('super-admin.usuarios.index'));

        $usuario = User::where('email', 'instructor.nuevo@aqualix.test')->first();

        $this->assertNotNull($usuario);
        $this->assertDatabaseHas('instructores', [
            'user_id' => $usuario->id,
            'sucursal_id' => $sucursal->id,
            'especialidad' => 'Estilo libre',
        ]);
    }

    public function test_no_se_puede_crear_un_admin_sin_sucursal(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)->post(route('super-admin.usuarios.store'), [
            'name' => 'Admin Sin Sucursal',
            'email' => 'sinsucursal@aqualix.test',
            'password' => 'password123',
            'role' => Rol::Admin->value,
            'activo' => '1',
        ])->assertSessionHasErrors('sucursal_id');
    }

    public function test_el_selector_principal_de_sucursal_filtra_el_listado_de_usuarios(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $sucursal = Sucursal::factory()->create();
        $otraSucursal = Sucursal::factory()->create();

        $adminPropio = User::factory()->admin($sucursal->id)->create(['name' => 'Admin de mi sucursal']);
        $adminAjeno = User::factory()->admin($otraSucursal->id)->create(['name' => 'Admin de otra sucursal']);

        $instructorPropio = User::factory()->create(['role' => Rol::Instructor->value, 'name' => 'Instructor de mi sucursal']);
        Instructor::factory()->create(['user_id' => $instructorPropio->id, 'sucursal_id' => $sucursal->id]);

        $tutor = User::factory()->tutor()->create(['name' => 'Tutor sin sucursal']);

        SucursalContext::establecer($sucursal->id);

        $response = $this->actingAs($superAdmin)->get(route('super-admin.usuarios.index'));

        $response->assertOk();
        $response->assertSee('Admin de mi sucursal');
        $response->assertSee('Instructor de mi sucursal');
        $response->assertSee($superAdmin->name);
        $response->assertSee('Tutor sin sucursal');
        $response->assertDontSee('Admin de otra sucursal');
    }

    public function test_super_admin_puede_activar_y_desactivar_un_usuario(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create(['activo' => true]);

        $this->actingAs($superAdmin)
            ->patch(route('super-admin.usuarios.estado', $admin))
            ->assertRedirect();

        $this->assertFalse($admin->refresh()->activo);
    }

    public function test_super_admin_no_puede_desactivarse_a_si_mismo(): void
    {
        $superAdmin = User::factory()->superAdmin()->create(['activo' => true]);

        $this->actingAs($superAdmin)
            ->patch(route('super-admin.usuarios.estado', $superAdmin))
            ->assertSessionHasErrors('usuario');

        $this->assertTrue($superAdmin->refresh()->activo);
    }
}
