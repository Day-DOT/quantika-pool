<?php

namespace Tests\Feature;

use App\Enums\EstadoAlumno;
use App\Models\Alumno;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminAlumnoTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_ve_alumnos_de_todas_las_sucursales(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $sucursal1 = Sucursal::factory()->create();
        $sucursal2 = Sucursal::factory()->create();

        Alumno::factory()->create(['sucursal_id' => $sucursal1->id, 'nombre' => 'Alumno Uno']);
        Alumno::factory()->create(['sucursal_id' => $sucursal2->id, 'nombre' => 'Alumno Dos']);

        $response = $this->actingAs($superAdmin)->get(route('alumnos.index'));

        $response->assertOk();
        $response->assertSee('Alumno Uno');
        $response->assertSee('Alumno Dos');
    }

    public function test_super_admin_puede_registrar_un_alumno_para_cualquier_sucursal(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $sucursal = Sucursal::factory()->create();

        $response = $this->actingAs($superAdmin)->post(route('alumnos.store'), [
            'sucursal_id' => $sucursal->id,
            'nombre' => 'Nuevo',
            'apellidos' => 'Alumno',
            'fecha_nacimiento' => now()->subYears(8)->toDateString(),
            'tutor_nombre' => 'Tutor de prueba',
            'tutor_email' => 'tutor-nuevo@example.com',
        ]);

        $alumno = Alumno::where('nombre', 'Nuevo')->first();
        $this->assertNotNull($alumno);
        $response->assertRedirect(route('alumnos.show', $alumno));
    }

    public function test_super_admin_puede_ver_el_detalle_de_un_alumno(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $alumno = Alumno::factory()->create(['nombre' => 'Detalle', 'apellidos' => 'Alumno']);

        $this->actingAs($superAdmin)
            ->get(route('alumnos.show', $alumno))
            ->assertOk()
            ->assertSee('Detalle Alumno');
    }

    public function test_super_admin_puede_editar_un_alumno(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $alumno = Alumno::factory()->create();
        $alumno->loadMissing('tutorUser');

        $this->actingAs($superAdmin)->put(route('alumnos.update', $alumno), [
            'nombre' => 'Actualizado',
            'apellidos' => $alumno->apellidos,
            'fecha_nacimiento' => $alumno->fecha_nacimiento->toDateString(),
            'estado' => EstadoAlumno::Inactivo->value,
            'tutor_nombre' => $alumno->tutorUser->name,
            'tutor_email' => $alumno->tutorUser->email,
        ])->assertRedirect(route('alumnos.show', $alumno));

        $this->assertDatabaseHas('alumnos', ['id' => $alumno->id, 'nombre' => 'Actualizado', 'estado' => 'inactivo']);
    }
}
