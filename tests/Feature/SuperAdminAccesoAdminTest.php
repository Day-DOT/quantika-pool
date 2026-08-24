<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminAccesoAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_puede_ver_todas_las_paginas_de_admin(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $rutas = [
            'admin.dashboard', 'alumnos.index',
            'instructores.index', 'evaluaciones.index',
            'pagos.index', 'pagos.registrar', 'pagos.deudores',
            'horarios.index', 'niveles.index', 'configuracion.index',
        ];

        foreach ($rutas as $ruta) {
            $response = $this->actingAs($superAdmin)->get(route($ruta));
            $this->assertTrue(
                $response->isOk(),
                "Ruta {$ruta} devolvió {$response->getStatusCode()} para super_admin"
            );
        }

        // alumnos.create redirige al modal de alumnos.index (comportamiento intencional).
        $this->actingAs($superAdmin)->get(route('alumnos.create'))->assertRedirect();
    }
}
