<?php

namespace Tests\Feature;

use App\Models\Alumno;
use App\Models\Nivel;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminNivelesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_ve_los_niveles_con_progreso_real(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();

        $nivel = Nivel::factory()->create([
            'nombre' => 'Nivel de prueba',
            'imagen' => 'images/Niveles/tortuga.png',
            'color_hex' => '#16e0a4',
        ]);

        Alumno::factory()->count(2)->create([
            'sucursal_id' => $sucursal->id,
            'nivel_id' => $nivel->id,
        ]);

        $response = $this->actingAs($admin)->get(route('niveles.index'));

        $response->assertOk();
        $response->assertSee('Nivel de prueba');
    }
}
