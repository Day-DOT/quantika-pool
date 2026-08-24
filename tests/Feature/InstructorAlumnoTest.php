<?php

namespace Tests\Feature;

use App\Models\Alumno;
use App\Models\CriterioEvaluacion;
use App\Models\Horario;
use App\Models\Inscripcion;
use App\Models\Instructor;
use App\Models\Nivel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstructorAlumnoTest extends TestCase
{
    use RefreshDatabase;

    public function test_instructor_ve_solo_los_alumnos_de_sus_propios_grupos(): void
    {
        $instructor = Instructor::factory()->create();
        $horario = Horario::factory()->create(['instructor_id' => $instructor->id]);
        $miAlumno = Alumno::factory()->create(['nombre' => 'Ana', 'apellidos' => 'Pérez']);

        Inscripcion::factory()->create([
            'horario_id' => $horario->id,
            'alumno_id' => $miAlumno->id,
            'activa' => true,
        ]);

        $otroInstructor = Instructor::factory()->create();
        $otroHorario = Horario::factory()->create(['instructor_id' => $otroInstructor->id]);
        $alumnoAjeno = Alumno::factory()->create(['nombre' => 'Luis', 'apellidos' => 'Gómez']);

        Inscripcion::factory()->create([
            'horario_id' => $otroHorario->id,
            'alumno_id' => $alumnoAjeno->id,
            'activa' => true,
        ]);

        $response = $this->actingAs($instructor->user)->get('/instructor/alumnos');

        $response->assertOk();
        $response->assertSee('Ana Pérez');
        $response->assertDontSee('Luis Gómez');
    }

    public function test_instructor_puede_ver_el_detalle_de_su_alumno_con_los_criterios_del_nivel(): void
    {
        $nivel = Nivel::factory()->create(['nombre' => 'Pez']);
        CriterioEvaluacion::factory()->create(['nivel_id' => $nivel->id, 'nombre' => 'Flotación']);

        $instructor = Instructor::factory()->create();
        $horario = Horario::factory()->create(['instructor_id' => $instructor->id, 'nivel_id' => $nivel->id]);
        $alumno = Alumno::factory()->create(['nivel_id' => $nivel->id]);

        Inscripcion::factory()->create([
            'horario_id' => $horario->id,
            'alumno_id' => $alumno->id,
            'activa' => true,
        ]);

        $response = $this->actingAs($instructor->user)->get(route('instructor.alumnos.show', $alumno));

        $response->assertOk();
        $response->assertSee('Flotación');
    }

    public function test_instructor_no_puede_ver_el_detalle_de_un_alumno_que_no_es_suyo(): void
    {
        $instructor = Instructor::factory()->create();
        $alumnoAjeno = Alumno::factory()->create();

        $this->actingAs($instructor->user)->get(route('instructor.alumnos.show', $alumnoAjeno))->assertForbidden();
    }
}
