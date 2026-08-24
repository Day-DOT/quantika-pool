<?php

namespace Tests\Feature;

use App\Models\Alumno;
use App\Models\Carril;
use App\Models\Horario;
use App\Models\Inscripcion;
use App\Models\Instructor;
use App\Models\Nivel;
use App\Models\Sucursal;
use App\Models\User;
use App\Support\SucursalContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminHorariosTest extends TestCase
{
    use RefreshDatabase;

    private function crearEscenario(): array
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();
        $instructor = Instructor::factory()->create(['sucursal_id' => $sucursal->id]);
        $nivel = Nivel::factory()->create();
        $carril = Carril::factory()->create(['sucursal_id' => $sucursal->id]);
        $otroCarril = Carril::factory()->create(['sucursal_id' => $sucursal->id]);

        return compact('sucursal', 'admin', 'instructor', 'nivel', 'carril', 'otroCarril');
    }

    public function test_admin_ve_el_tablero_de_horarios(): void
    {
        $e = $this->crearEscenario();

        Horario::factory()->create([
            'sucursal_id' => $e['sucursal']->id,
            'instructor_id' => $e['instructor']->id,
            'nivel_id' => $e['nivel']->id,
            'carril_id' => $e['carril']->id,
            'nombre_grupo' => 'Grupo de prueba',
        ]);

        $response = $this->actingAs($e['admin'])->get(route('horarios.index'));

        $response->assertOk();
        $response->assertSee('Grupo de prueba');
    }

    public function test_admin_crea_una_nueva_clase(): void
    {
        $e = $this->crearEscenario();

        $response = $this->actingAs($e['admin'])->post(route('horarios.store'), [
            'nombre_grupo' => 'Nueva clase de prueba',
            'nivel_id' => $e['nivel']->id,
            'instructor_id' => $e['instructor']->id,
            'carril_id' => $e['carril']->id,
            'dia_semana' => 2,
            'hora_inicio' => '09:00',
            'hora_fin' => '10:00',
            'capacidad_maxima' => 10,
        ]);

        $response->assertRedirect(route('horarios.index'));

        $this->assertDatabaseHas('horarios', [
            'sucursal_id' => $e['sucursal']->id,
            'nombre_grupo' => 'Nueva clase de prueba',
        ]);
    }

    public function test_super_admin_con_sucursal_seleccionada_crea_clase_sin_enviar_sucursal_id(): void
    {
        $e = $this->crearEscenario();
        $superAdmin = User::factory()->superAdmin()->create();
        SucursalContext::establecer($e['sucursal']->id);

        $response = $this->actingAs($superAdmin)->post(route('horarios.store'), [
            'nombre_grupo' => 'Clase del selector',
            'nivel_id' => $e['nivel']->id,
            'instructor_id' => $e['instructor']->id,
            'carril_id' => $e['carril']->id,
            'dia_semana' => 4,
            'hora_inicio' => '09:00',
            'hora_fin' => '10:00',
            'capacidad_maxima' => 10,
        ]);

        $response->assertSessionDoesntHaveErrors('sucursal_id');
        $response->assertRedirect(route('horarios.index'));

        $this->assertDatabaseHas('horarios', [
            'sucursal_id' => $e['sucursal']->id,
            'nombre_grupo' => 'Clase del selector',
        ]);
    }

    public function test_super_admin_en_vista_global_crea_una_clase_enviando_sucursal_id(): void
    {
        $e = $this->crearEscenario();
        $superAdmin = User::factory()->superAdmin()->create();

        $response = $this->actingAs($superAdmin)->post(route('horarios.store'), [
            'sucursal_id' => $e['sucursal']->id,
            'nombre_grupo' => 'Clase global',
            'nivel_id' => $e['nivel']->id,
            'instructor_id' => $e['instructor']->id,
            'carril_id' => $e['carril']->id,
            'dia_semana' => 5,
            'hora_inicio' => '09:00',
            'hora_fin' => '10:00',
            'capacidad_maxima' => 10,
        ]);

        $response->assertRedirect(route('horarios.index'));

        $this->assertDatabaseHas('horarios', [
            'sucursal_id' => $e['sucursal']->id,
            'nombre_grupo' => 'Clase global',
        ]);
    }

    public function test_no_se_puede_crear_una_clase_con_instructor_de_otra_sucursal(): void
    {
        $e = $this->crearEscenario();
        $otraSucursal = Sucursal::factory()->create();
        $instructorAjeno = Instructor::factory()->create(['sucursal_id' => $otraSucursal->id]);
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)->post(route('horarios.store'), [
            'sucursal_id' => $e['sucursal']->id,
            'nombre_grupo' => 'Clase inconsistente',
            'nivel_id' => $e['nivel']->id,
            'instructor_id' => $instructorAjeno->id,
            'carril_id' => $e['carril']->id,
            'dia_semana' => 5,
            'hora_inicio' => '09:00',
            'hora_fin' => '10:00',
            'capacidad_maxima' => 10,
        ])->assertSessionHasErrors('instructor_id');

        $this->assertDatabaseMissing('horarios', ['nombre_grupo' => 'Clase inconsistente']);
    }

    public function test_no_se_puede_reagendar_una_clase_con_un_carril_de_otra_sucursal(): void
    {
        $e = $this->crearEscenario();
        $otraSucursal = Sucursal::factory()->create();
        $carrilAjeno = Carril::factory()->create(['sucursal_id' => $otraSucursal->id]);

        $horario = Horario::factory()->create([
            'sucursal_id' => $e['sucursal']->id,
            'instructor_id' => $e['instructor']->id,
            'nivel_id' => $e['nivel']->id,
            'carril_id' => $e['carril']->id,
            'dia_semana' => 1,
        ]);

        $this->actingAs($e['admin'])->patch(route('horarios.reagendar', $horario), [
            'dia_semana' => 3,
            'hora_inicio' => '11:00',
            'hora_fin' => '12:00',
            'carril_id' => $carrilAjeno->id,
        ])->assertSessionHasErrors('carril_id');

        $this->assertEquals($e['carril']->id, $horario->refresh()->carril_id);
    }

    public function test_no_se_puede_cambiar_a_un_alumno_a_un_grupo_de_otra_sucursal(): void
    {
        $e = $this->crearEscenario();
        $otraSucursal = Sucursal::factory()->create();
        $superAdmin = User::factory()->superAdmin()->create();

        $horarioViejo = Horario::factory()->create([
            'sucursal_id' => $e['sucursal']->id,
            'instructor_id' => $e['instructor']->id,
            'nivel_id' => $e['nivel']->id,
            'carril_id' => $e['carril']->id,
        ]);

        $instructorAjeno = Instructor::factory()->create(['sucursal_id' => $otraSucursal->id]);
        $carrilAjeno = Carril::factory()->create(['sucursal_id' => $otraSucursal->id]);
        $horarioAjeno = Horario::factory()->create([
            'sucursal_id' => $otraSucursal->id,
            'instructor_id' => $instructorAjeno->id,
            'nivel_id' => $e['nivel']->id,
            'carril_id' => $carrilAjeno->id,
        ]);

        $alumno = Alumno::factory()->create(['sucursal_id' => $e['sucursal']->id]);
        Inscripcion::factory()->create(['horario_id' => $horarioViejo->id, 'alumno_id' => $alumno->id, 'activa' => true]);

        $this->actingAs($superAdmin)->patch(route('inscripciones.cambiar-grupo'), [
            'alumno_id' => $alumno->id,
            'horario_id' => $horarioAjeno->id,
        ])->assertSessionHasErrors('horario_id');

        $this->assertDatabaseHas('inscripciones', [
            'alumno_id' => $alumno->id,
            'horario_id' => $horarioViejo->id,
            'activa' => true,
        ]);
    }

    public function test_admin_reagenda_una_clase_existente(): void
    {
        $e = $this->crearEscenario();

        $horario = Horario::factory()->create([
            'sucursal_id' => $e['sucursal']->id,
            'instructor_id' => $e['instructor']->id,
            'nivel_id' => $e['nivel']->id,
            'carril_id' => $e['carril']->id,
            'dia_semana' => 1,
        ]);

        $response = $this->actingAs($e['admin'])->patch(route('horarios.reagendar', $horario), [
            'dia_semana' => 3,
            'hora_inicio' => '11:00',
            'hora_fin' => '12:00',
            'carril_id' => $e['otroCarril']->id,
        ]);

        $response->assertRedirect(route('horarios.index'));

        $horario->refresh();
        $this->assertEquals(3, $horario->dia_semana->value);
        $this->assertEquals($e['otroCarril']->id, $horario->carril_id);
    }

    public function test_admin_asigna_un_alumno_sin_inscripcion_a_una_clase(): void
    {
        $e = $this->crearEscenario();

        $horario = Horario::factory()->create([
            'sucursal_id' => $e['sucursal']->id,
            'instructor_id' => $e['instructor']->id,
            'nivel_id' => $e['nivel']->id,
            'carril_id' => $e['carril']->id,
            'capacidad_maxima' => 5,
        ]);

        $alumno = Alumno::factory()->create(['sucursal_id' => $e['sucursal']->id]);

        $response = $this->actingAs($e['admin'])->post(route('inscripciones.store'), [
            'alumno_id' => $alumno->id,
            'horario_id' => $horario->id,
        ]);

        $response->assertRedirect(route('horarios.index'));

        $this->assertDatabaseHas('inscripciones', [
            'alumno_id' => $alumno->id,
            'horario_id' => $horario->id,
            'activa' => true,
        ]);
    }

    public function test_no_se_puede_asignar_un_alumno_a_una_clase_sin_cupo(): void
    {
        $e = $this->crearEscenario();

        $horario = Horario::factory()->create([
            'sucursal_id' => $e['sucursal']->id,
            'instructor_id' => $e['instructor']->id,
            'nivel_id' => $e['nivel']->id,
            'carril_id' => $e['carril']->id,
            'capacidad_maxima' => 1,
        ]);

        $inscrito = Alumno::factory()->create(['sucursal_id' => $e['sucursal']->id]);
        Inscripcion::factory()->create(['horario_id' => $horario->id, 'alumno_id' => $inscrito->id, 'activa' => true]);

        $nuevo = Alumno::factory()->create(['sucursal_id' => $e['sucursal']->id]);

        $this->actingAs($e['admin'])->post(route('inscripciones.store'), [
            'alumno_id' => $nuevo->id,
            'horario_id' => $horario->id,
        ])->assertSessionHasErrors('horario_id');

        $this->assertDatabaseMissing('inscripciones', ['alumno_id' => $nuevo->id]);
    }

    public function test_no_se_puede_asignar_un_alumno_ya_inscrito_en_la_misma_clase(): void
    {
        $e = $this->crearEscenario();

        $horario = Horario::factory()->create([
            'sucursal_id' => $e['sucursal']->id,
            'instructor_id' => $e['instructor']->id,
            'nivel_id' => $e['nivel']->id,
            'carril_id' => $e['carril']->id,
            'capacidad_maxima' => 5,
        ]);

        $alumno = Alumno::factory()->create(['sucursal_id' => $e['sucursal']->id]);
        Inscripcion::factory()->create(['horario_id' => $horario->id, 'alumno_id' => $alumno->id, 'activa' => true]);

        $this->actingAs($e['admin'])->post(route('inscripciones.store'), [
            'alumno_id' => $alumno->id,
            'horario_id' => $horario->id,
        ])->assertSessionHasErrors('alumno_id');
    }

    public function test_un_alumno_ya_inscrito_en_una_clase_puede_ser_asignado_a_otra_clase_distinta(): void
    {
        $e = $this->crearEscenario();

        $primeraClase = Horario::factory()->create([
            'sucursal_id' => $e['sucursal']->id,
            'instructor_id' => $e['instructor']->id,
            'nivel_id' => $e['nivel']->id,
            'carril_id' => $e['carril']->id,
        ]);

        $segundaClase = Horario::factory()->create([
            'sucursal_id' => $e['sucursal']->id,
            'instructor_id' => $e['instructor']->id,
            'nivel_id' => $e['nivel']->id,
            'carril_id' => $e['otroCarril']->id,
        ]);

        $alumno = Alumno::factory()->create(['sucursal_id' => $e['sucursal']->id]);

        Inscripcion::factory()->create([
            'horario_id' => $primeraClase->id,
            'alumno_id' => $alumno->id,
            'activa' => true,
        ]);

        $response = $this->actingAs($e['admin'])->get(route('horarios.index'));
        $response->assertOk();
        $response->assertSee($alumno->nombreCompleto());

        $store = $this->actingAs($e['admin'])->post(route('inscripciones.store'), [
            'alumno_id' => $alumno->id,
            'horario_id' => $segundaClase->id,
        ]);

        $store->assertSessionDoesntHaveErrors();
        $store->assertRedirect(route('horarios.index'));

        $this->assertDatabaseHas('inscripciones', [
            'alumno_id' => $alumno->id,
            'horario_id' => $primeraClase->id,
            'activa' => true,
        ]);

        $this->assertDatabaseHas('inscripciones', [
            'alumno_id' => $alumno->id,
            'horario_id' => $segundaClase->id,
            'activa' => true,
        ]);
    }

    public function test_no_se_puede_asignar_un_alumno_a_una_clase_de_otra_sucursal(): void
    {
        $e = $this->crearEscenario();
        $otraSucursal = Sucursal::factory()->create();

        $horario = Horario::factory()->create([
            'sucursal_id' => $e['sucursal']->id,
            'instructor_id' => $e['instructor']->id,
            'nivel_id' => $e['nivel']->id,
            'carril_id' => $e['carril']->id,
            'capacidad_maxima' => 5,
        ]);

        $alumnoAjeno = Alumno::factory()->create(['sucursal_id' => $otraSucursal->id]);

        $this->actingAs($e['admin'])->post(route('inscripciones.store'), [
            'alumno_id' => $alumnoAjeno->id,
            'horario_id' => $horario->id,
        ])->assertSessionHasErrors('alumno_id');

        $this->assertDatabaseMissing('inscripciones', ['alumno_id' => $alumnoAjeno->id]);
    }

    public function test_admin_cambia_a_un_alumno_de_grupo(): void
    {
        $e = $this->crearEscenario();

        $horarioViejo = Horario::factory()->create([
            'sucursal_id' => $e['sucursal']->id,
            'instructor_id' => $e['instructor']->id,
            'nivel_id' => $e['nivel']->id,
            'carril_id' => $e['carril']->id,
        ]);

        $horarioNuevo = Horario::factory()->create([
            'sucursal_id' => $e['sucursal']->id,
            'instructor_id' => $e['instructor']->id,
            'nivel_id' => $e['nivel']->id,
            'carril_id' => $e['otroCarril']->id,
        ]);

        $alumno = Alumno::factory()->create(['sucursal_id' => $e['sucursal']->id]);

        Inscripcion::factory()->create([
            'horario_id' => $horarioViejo->id,
            'alumno_id' => $alumno->id,
            'activa' => true,
        ]);

        $response = $this->actingAs($e['admin'])->patch(route('inscripciones.cambiar-grupo'), [
            'alumno_id' => $alumno->id,
            'horario_id' => $horarioNuevo->id,
        ]);

        $response->assertRedirect(route('horarios.index'));

        $this->assertDatabaseHas('inscripciones', [
            'alumno_id' => $alumno->id,
            'horario_id' => $horarioNuevo->id,
            'activa' => true,
        ]);

        $this->assertDatabaseHas('inscripciones', [
            'alumno_id' => $alumno->id,
            'horario_id' => $horarioViejo->id,
            'activa' => false,
        ]);
    }
}
