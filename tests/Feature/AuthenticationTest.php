<?php

namespace Tests\Feature;

use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_puede_ver_el_login(): void
    {
        $this->get('/login')->assertOk();
    }

    public function test_guest_es_redirigido_a_login_al_intentar_entrar_a_una_ruta_protegida(): void
    {
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_super_admin_inicia_sesion_y_llega_a_su_dashboard(): void
    {
        $user = User::factory()->superAdmin()->create(['password' => bcrypt('secreto123')]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'secreto123',
        ]);

        $response->assertRedirect(route('super-admin.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_admin_inicia_sesion_y_llega_a_su_dashboard(): void
    {
        $sucursal = Sucursal::factory()->create();
        $user = User::factory()->admin($sucursal->id)->create(['password' => bcrypt('secreto123')]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'secreto123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_login_con_password_incorrecto_falla(): void
    {
        $user = User::factory()->admin(Sucursal::factory()->create()->id)->create([
            'password' => bcrypt('secreto123'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'incorrecta',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_instructor_no_puede_entrar_al_panel_de_administracion(): void
    {
        $sucursal = Sucursal::factory()->create();
        $user = User::factory()->instructor()->create(['sucursal_id' => null]);

        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    public function test_super_admin_si_puede_entrar_al_panel_de_administracion(): void
    {
        $user = User::factory()->superAdmin()->create();

        $this->actingAs($user)->get('/admin')->assertOk();
    }

    public function test_admin_no_puede_entrar_al_panel_de_super_admin(): void
    {
        $sucursal = Sucursal::factory()->create();
        $user = User::factory()->admin($sucursal->id)->create();

        $this->actingAs($user)->get('/super-admin')->assertForbidden();
    }

    public function test_usuario_inactivo_no_puede_iniciar_sesion(): void
    {
        $user = User::factory()->admin(Sucursal::factory()->create()->id)->create([
            'password' => bcrypt('secreto123'),
            'activo' => false,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'secreto123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_logout_cierra_la_sesion(): void
    {
        $user = User::factory()->superAdmin()->create();

        $this->actingAs($user)->post('/logout')->assertRedirect('/login');
        $this->assertGuest();
    }
}
