<?php

namespace Tests\Feature;

use App\Models\Alumno;
use App\Models\Instructor;
use App\Models\Sucursal;
use App\Models\User;
use App\Support\SucursalContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoPorSucursalTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_de_una_sucursal_con_logo_ve_ese_logo(): void
    {
        $sucursal = Sucursal::factory()->create(['logo_path' => 'images/logo-sucursal-2.png']);
        $admin = User::factory()->admin($sucursal->id)->create();
        $this->actingAs($admin);

        $this->assertSame(asset('images/logo-sucursal-2.png'), $admin->logoUrl());
    }

    public function test_admin_de_una_sucursal_sin_logo_ve_el_logo_por_defecto(): void
    {
        $sucursal = Sucursal::factory()->create(['logo_path' => null]);
        $admin = User::factory()->admin($sucursal->id)->create();
        $this->actingAs($admin);

        $this->assertSame(asset('images/quantika-logo.png'), $admin->logoUrl());
    }

    public function test_instructor_ve_el_logo_de_la_sucursal_de_su_registro_de_instructor(): void
    {
        $sucursal = Sucursal::factory()->create(['logo_path' => 'images/logo-sucursal-2.png']);
        $instructorUser = User::factory()->instructor()->create();
        Instructor::factory()->create(['user_id' => $instructorUser->id, 'sucursal_id' => $sucursal->id]);
        $this->actingAs($instructorUser->fresh());

        $this->assertSame(asset('images/logo-sucursal-2.png'), auth()->user()->logoUrl());
    }

    public function test_tutor_ve_el_logo_de_la_sucursal_de_sus_alumnos(): void
    {
        $sucursal = Sucursal::factory()->create(['logo_path' => 'images/logo-sucursal-2.png']);
        $tutor = User::factory()->tutor()->create();
        Alumno::factory()->create(['tutor_user_id' => $tutor->id, 'sucursal_id' => $sucursal->id]);
        $this->actingAs($tutor);

        $this->assertSame(asset('images/logo-sucursal-2.png'), $tutor->logoUrl());
    }

    public function test_super_admin_en_vista_global_ve_el_logo_por_defecto(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $this->actingAs($superAdmin);

        $this->assertSame(asset('images/quantika-logo.png'), $superAdmin->logoUrl());
    }

    public function test_super_admin_al_navegar_a_una_sucursal_ve_su_logo(): void
    {
        $sucursal = Sucursal::factory()->create(['logo_path' => 'images/logo-sucursal-2.png']);
        $superAdmin = User::factory()->superAdmin()->create();
        $this->actingAs($superAdmin);

        SucursalContext::establecer($sucursal->id);

        $this->assertSame(asset('images/logo-sucursal-2.png'), $superAdmin->logoUrl());
    }
}
