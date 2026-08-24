<?php

namespace Tests\Feature;

use App\Models\Horario;
use App\Models\Instructor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstructorAgendaTest extends TestCase
{
    use RefreshDatabase;

    public function test_instructor_ve_su_agenda_semanal_agrupada_por_dia(): void
    {
        $instructor = Instructor::factory()->create();

        Horario::factory()->create([
            'instructor_id' => $instructor->id,
            'dia_semana' => 1,
            'nombre_grupo' => 'Grupo Lunes',
        ]);

        $response = $this->actingAs($instructor->user)->get('/instructor/agenda');

        $response->assertOk();
        $response->assertSee('Lunes');
        $response->assertSee('Grupo Lunes');
    }

    public function test_instructor_sin_grupos_ve_un_estado_vacio(): void
    {
        $instructor = Instructor::factory()->create();

        $response = $this->actingAs($instructor->user)->get('/instructor/agenda');

        $response->assertOk();
        $response->assertSee('Todavía no tienes grupos asignados');
    }

    public function test_instructor_sin_perfil_no_puede_ver_la_agenda(): void
    {
        $user = User::factory()->instructor()->create();

        $this->actingAs($user)->get('/instructor/agenda')->assertForbidden();
    }
}
