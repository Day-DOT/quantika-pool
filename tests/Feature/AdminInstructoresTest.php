<?php

namespace Tests\Feature;

use App\Enums\Rol;
use App\Models\Instructor;
use App\Models\Sucursal;
use App\Models\User;
use App\Support\SucursalContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminInstructoresTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_ve_instructores_de_su_sucursal(): void
    {
        $sucursal = Sucursal::factory()->create();
        $otraSucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();

        $propio = Instructor::factory()->create(['sucursal_id' => $sucursal->id]);
        $ajeno = Instructor::factory()->create(['sucursal_id' => $otraSucursal->id]);

        $response = $this->actingAs($admin)->get(route('instructores.index'));

        $response->assertOk();
        $response->assertSee($propio->user->name);
        $response->assertDontSee($ajeno->user->name);
    }

    public function test_admin_registra_un_instructor_nuevo(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();

        $response = $this->actingAs($admin)->post(route('instructores.store'), [
            'name' => 'Nuevo Instructor',
            'email' => 'instructor.nuevo@example.com',
            'telefono' => '5512345678',
            'especialidad' => 'Nivel avanzado',
        ]);

        $response->assertRedirect(route('instructores.index'));

        $user = User::where('email', 'instructor.nuevo@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals(Rol::Instructor, $user->role);

        $this->assertDatabaseHas('instructores', [
            'user_id' => $user->id,
            'sucursal_id' => $sucursal->id,
            'especialidad' => 'Nivel avanzado',
        ]);
    }

    public function test_super_admin_con_sucursal_seleccionada_registra_instructor_sin_enviar_sucursal_id(): void
    {
        $sucursal = Sucursal::factory()->create();
        $superAdmin = User::factory()->superAdmin()->create();
        SucursalContext::establecer($sucursal->id);

        $response = $this->actingAs($superAdmin)->post(route('instructores.store'), [
            'name' => 'Instructor De Sucursal',
            'email' => 'instructor.sucursal@example.com',
            'telefono' => '5511223344',
            'especialidad' => 'Nivel básico',
        ]);

        $response->assertSessionDoesntHaveErrors('sucursal_id');
        $response->assertRedirect(route('instructores.index'));

        $this->assertDatabaseHas('instructores', [
            'sucursal_id' => $sucursal->id,
            'especialidad' => 'Nivel básico',
        ]);
    }

    public function test_super_admin_en_vista_global_debe_enviar_sucursal_id_para_registrar_instructor(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $response = $this->actingAs($superAdmin)->post(route('instructores.store'), [
            'name' => 'Instructor Sin Sucursal',
            'email' => 'instructor.global@example.com',
        ]);

        $response->assertSessionHasErrors('sucursal_id');
    }

    public function test_admin_puede_activar_y_desactivar_un_instructor(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();
        $instructor = Instructor::factory()->create(['sucursal_id' => $sucursal->id, 'estado' => 'activo']);

        $this->actingAs($admin)->patch(route('instructores.toggle-estado', $instructor))->assertRedirect();

        $instructor->refresh();
        $this->assertEquals('inactivo', $instructor->estado);
    }
}
