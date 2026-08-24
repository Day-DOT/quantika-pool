<?php

namespace Tests\Feature;

use App\Models\Horario;
use App\Models\Instructor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstructorDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_es_redirigido_al_login(): void
    {
        $this->get('/instructor')->assertRedirect('/login');
    }

    public function test_un_alumno_no_puede_entrar_al_portal_del_instructor(): void
    {
        $user = User::factory()->tutor()->create();

        $this->actingAs($user)->get('/instructor')->assertForbidden();
    }

    public function test_instructor_sin_perfil_ve_un_estado_vacio_en_vez_de_un_error(): void
    {
        $user = User::factory()->instructor()->create();

        $response = $this->actingAs($user)->get('/instructor');

        $response->assertOk();
        $response->assertSee('Tu perfil de instructor aún no ha sido configurado');
    }

    public function test_instructor_ve_sus_propios_grupos_y_estadisticas_pero_no_los_de_otro_instructor(): void
    {
        $instructor = Instructor::factory()->create();

        Horario::factory()->create([
            'instructor_id' => $instructor->id,
            'sucursal_id' => $instructor->sucursal_id,
            'nombre_grupo' => 'Delfines Azules',
        ]);

        $otroInstructor = Instructor::factory()->create();
        Horario::factory()->create([
            'instructor_id' => $otroInstructor->id,
            'nombre_grupo' => 'Grupo Ajeno',
        ]);

        $response = $this->actingAs($instructor->user)->get('/instructor');

        $response->assertOk();
        $response->assertSee('Delfines Azules');
        $response->assertDontSee('Grupo Ajeno');
    }
}
