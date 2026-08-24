<?php

namespace Tests\Feature;

use App\Enums\EstadoCita;
use App\Models\Alumno;
use App\Models\Cita;
use App\Models\Horario;
use App\Models\Inscripcion;
use App\Models\Instructor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstructorAsistenciaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array{0: Instructor, 1: Horario, 2: Alumno}
     */
    private function crearGrupoConAlumno(): array
    {
        $instructor = Instructor::factory()->create();
        $horario = Horario::factory()->create(['instructor_id' => $instructor->id]);
        $alumno = Alumno::factory()->create();

        Inscripcion::factory()->create([
            'horario_id' => $horario->id,
            'alumno_id' => $alumno->id,
            'activa' => true,
        ]);

        return [$instructor, $horario, $alumno];
    }

    public function test_instructor_ve_la_lista_de_asistencia_de_su_grupo(): void
    {
        [$instructor, $horario, $alumno] = $this->crearGrupoConAlumno();

        $response = $this->actingAs($instructor->user)->get(route('instructor.grupos.show', $horario));

        $response->assertOk();
        $response->assertSee($alumno->nombreCompleto());
    }

    public function test_instructor_no_puede_ver_el_grupo_de_otro_instructor(): void
    {
        [, $horario] = $this->crearGrupoConAlumno();
        $otro = Instructor::factory()->create();

        $this->actingAs($otro->user)->get(route('instructor.grupos.show', $horario))->assertForbidden();
    }

    public function test_instructor_marca_asistencia_y_crea_la_cita_del_dia_si_no_existia(): void
    {
        [$instructor, $horario, $alumno] = $this->crearGrupoConAlumno();

        $response = $this->actingAs($instructor->user)->post(
            route('instructor.grupos.asistencia', [$horario, $alumno]),
            ['asistio' => '1']
        );

        $response->assertRedirect();

        $this->assertDatabaseHas('citas', [
            'horario_id' => $horario->id,
            'alumno_id' => $alumno->id,
            'asistio' => 1,
            'estado' => EstadoCita::Completada->value,
        ]);
    }

    public function test_instructor_actualiza_la_asistencia_si_la_cita_de_hoy_ya_existia(): void
    {
        [$instructor, $horario, $alumno] = $this->crearGrupoConAlumno();

        $cita = Cita::factory()->create([
            'horario_id' => $horario->id,
            'alumno_id' => $alumno->id,
            'sucursal_id' => $horario->sucursal_id,
            'fecha' => today(),
            'asistio' => null,
        ]);

        $this->actingAs($instructor->user)->post(
            route('instructor.grupos.asistencia', [$horario, $alumno]),
            ['asistio' => '0']
        );

        $this->assertDatabaseHas('citas', [
            'id' => $cita->id,
            'asistio' => 0,
            'estado' => EstadoCita::Completada->value,
        ]);

        $this->assertDatabaseCount('citas', 1);
    }

    public function test_instructor_no_puede_marcar_asistencia_de_un_grupo_ajeno(): void
    {
        [, $horario, $alumno] = $this->crearGrupoConAlumno();
        $otro = Instructor::factory()->create();

        $this->actingAs($otro->user)->post(
            route('instructor.grupos.asistencia', [$horario, $alumno]),
            ['asistio' => '1']
        )->assertForbidden();
    }

    public function test_no_se_puede_marcar_asistencia_de_un_alumno_no_inscrito_en_el_grupo(): void
    {
        [$instructor, $horario] = $this->crearGrupoConAlumno();
        $otroAlumno = Alumno::factory()->create();

        $this->actingAs($instructor->user)->post(
            route('instructor.grupos.asistencia', [$horario, $otroAlumno]),
            ['asistio' => '1']
        )->assertNotFound();
    }
}
