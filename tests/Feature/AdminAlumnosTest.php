<?php

namespace Tests\Feature;

use App\Enums\EstadoAlumno;
use App\Enums\Rol;
use App\Models\Alumno;
use App\Models\Nivel;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminAlumnosTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_ve_el_listado_de_alumnos_de_su_sucursal(): void
    {
        $sucursal = Sucursal::factory()->create();
        $otraSucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();

        $propio = Alumno::factory()->create(['sucursal_id' => $sucursal->id, 'nombre' => 'Propio']);
        Alumno::factory()->create(['sucursal_id' => $otraSucursal->id, 'nombre' => 'Ajeno']);

        $response = $this->actingAs($admin)->get(route('alumnos.index'));

        $response->assertOk();
        $response->assertSee('Propio');
        $response->assertDontSee('Ajeno');
    }

    public function test_alumnos_create_redirige_al_index_con_el_modal(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();

        $response = $this->actingAs($admin)->get(route('alumnos.create'));

        $response->assertRedirect(route('alumnos.index', ['crear' => 1]));
    }

    public function test_admin_registra_un_alumno_nuevo_y_crea_tutor(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();
        $nivel = Nivel::factory()->create();

        $datos = [
            'nombre' => 'Sofía',
            'apellidos' => 'Martínez López',
            'fecha_nacimiento' => '2016-05-10',
            'telefono' => '5511223344',
            'email' => 'sofia@example.com',
            'tutor_nombre' => 'Laura Martínez',
            'tutor_email' => 'laura.tutor@example.com',
            'tutor_telefono' => '5599887766',
            'nivel_id' => $nivel->id,
            'observaciones' => 'Ninguna',
        ];

        $response = $this->actingAs($admin)->post(route('alumnos.store'), $datos);

        $alumno = Alumno::where('nombre', 'Sofía')->first();

        $this->assertNotNull($alumno);
        $response->assertRedirect(route('alumnos.show', $alumno));
        $this->assertEquals($sucursal->id, $alumno->sucursal_id);
        $this->assertEquals(EstadoAlumno::Activo, $alumno->estado);

        $tutor = User::where('email', 'laura.tutor@example.com')->first();
        $this->assertNotNull($tutor);
        $this->assertEquals(Rol::Alumno, $tutor->role);
        $this->assertEquals($tutor->id, $alumno->tutor_user_id);

        $this->assertDatabaseHas('alumno_nivel_historial', [
            'alumno_id' => $alumno->id,
            'nivel_id' => $nivel->id,
        ]);
    }

    public function test_registrar_alumno_reutiliza_tutor_existente_por_correo(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();
        $tutor = User::factory()->tutor()->create(['email' => 'papa@example.com']);

        $datos = [
            'nombre' => 'Hermano',
            'apellidos' => 'Repetido',
            'fecha_nacimiento' => '2015-01-01',
            'tutor_nombre' => $tutor->name,
            'tutor_email' => 'papa@example.com',
        ];

        $this->actingAs($admin)->post(route('alumnos.store'), $datos);

        $this->assertEquals(1, User::where('email', 'papa@example.com')->count());

        $alumno = Alumno::where('nombre', 'Hermano')->first();
        $this->assertEquals($tutor->id, $alumno->tutor_user_id);
    }

    public function test_admin_puede_registrar_un_alumno_sin_tutor(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();

        $response = $this->actingAs($admin)->post(route('alumnos.store'), [
            'nombre' => 'Sin',
            'apellidos' => 'Tutor',
            'fecha_nacimiento' => '2016-05-10',
        ]);

        $alumno = Alumno::where('nombre', 'Sin')->first();

        $this->assertNotNull($alumno);
        $response->assertRedirect(route('alumnos.show', $alumno));
        $this->assertNull($alumno->tutor_user_id);
        $this->assertNull($alumno->tutor_contacto_nombre);

        // El alumno debe seguir siendo visible y editable sin tutor asignado.
        $this->actingAs($admin)->get(route('alumnos.show', $alumno))->assertOk();
        $this->actingAs($admin)->get(route('alumnos.edit', $alumno))->assertOk();
    }

    public function test_admin_puede_registrar_un_alumno_con_tutor_de_solo_contacto_sin_correo(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();

        $this->actingAs($admin)->post(route('alumnos.store'), [
            'nombre' => 'Con',
            'apellidos' => 'TutorContacto',
            'fecha_nacimiento' => '2016-05-10',
            'tiene_tutor' => '1',
            'tutor_nombre' => 'Tutor Sin Correo',
            'tutor_telefono' => '5500112233',
        ]);

        $alumno = Alumno::where('nombre', 'Con')->first();

        $this->assertNotNull($alumno);
        $this->assertNull($alumno->tutor_user_id);
        $this->assertEquals('Tutor Sin Correo', $alumno->tutor_contacto_nombre);
        $this->assertEquals('5500112233', $alumno->tutor_contacto_telefono);
        $this->assertEquals('Tutor Sin Correo', $alumno->nombreTutor());
    }

    public function test_admin_registra_un_alumno_con_documentos_opcionales(): void
    {
        Storage::fake('public');

        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();

        $datos = [
            'nombre' => 'Con',
            'apellidos' => 'Documentos',
            'fecha_nacimiento' => '2016-05-10',
            'tutor_nombre' => 'Tutor Documentado',
            'tutor_email' => 'documentos@example.com',
            'certificado_medico' => UploadedFile::fake()->create('certificado.pdf', 200, 'application/pdf'),
            'identificacion' => UploadedFile::fake()->image('identificacion.jpg'),
            'foto' => UploadedFile::fake()->image('foto.png'),
        ];

        $this->actingAs($admin)->post(route('alumnos.store'), $datos);

        $alumno = Alumno::where('nombre', 'Con')->first();

        $this->assertNotNull($alumno->certificado_medico_path);
        $this->assertNotNull($alumno->identificacion_path);
        $this->assertNotNull($alumno->foto_path);
        Storage::disk('public')->assertExists($alumno->certificado_medico_path);
        Storage::disk('public')->assertExists($alumno->identificacion_path);
        Storage::disk('public')->assertExists($alumno->foto_path);
    }

    public function test_admin_registra_un_alumno_sin_documentos(): void
    {
        Storage::fake('public');

        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();

        $this->actingAs($admin)->post(route('alumnos.store'), [
            'nombre' => 'Sin',
            'apellidos' => 'Documentos',
            'fecha_nacimiento' => '2016-05-10',
            'tutor_nombre' => 'Tutor',
            'tutor_email' => 'sindocumentos@example.com',
        ]);

        $alumno = Alumno::where('nombre', 'Sin')->first();

        $this->assertNotNull($alumno);
        $this->assertNull($alumno->certificado_medico_path);
        $this->assertNull($alumno->identificacion_path);
        $this->assertNull($alumno->foto_path);
    }

    public function test_admin_reemplaza_el_documento_de_un_alumno_al_editar(): void
    {
        Storage::fake('public');

        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();
        $alumno = Alumno::factory()->create([
            'sucursal_id' => $sucursal->id,
            'foto_path' => 'alumnos/documentos/foto-anterior.png',
        ]);
        Storage::disk('public')->put('alumnos/documentos/foto-anterior.png', 'contenido-anterior');

        $this->actingAs($admin)->put(route('alumnos.update', $alumno), [
            'nombre' => $alumno->nombre,
            'apellidos' => $alumno->apellidos,
            'fecha_nacimiento' => $alumno->fecha_nacimiento->format('Y-m-d'),
            'estado' => 'activo',
            'tutor_nombre' => $alumno->tutorUser->name,
            'tutor_email' => $alumno->tutorUser->email,
            'foto' => UploadedFile::fake()->image('foto-nueva.png'),
        ]);

        $alumno->refresh();

        $this->assertNotEquals('alumnos/documentos/foto-anterior.png', $alumno->foto_path);
        Storage::disk('public')->assertExists($alumno->foto_path);
        Storage::disk('public')->assertMissing('alumnos/documentos/foto-anterior.png');
    }

    public function test_admin_no_puede_registrar_alumno_en_otra_sucursal(): void
    {
        // El admin no elige sucursal_id: el controlador la fuerza a la suya.
        $sucursal = Sucursal::factory()->create();
        $otraSucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();

        $this->actingAs($admin)->post(route('alumnos.store'), [
            'nombre' => 'Test',
            'apellidos' => 'Alumno',
            'fecha_nacimiento' => '2015-01-01',
            'tutor_nombre' => 'Tutor',
            'tutor_email' => 'tutor.forzado@example.com',
            'sucursal_id' => $otraSucursal->id,
        ]);

        $alumno = Alumno::where('nombre', 'Test')->first();
        $this->assertEquals($sucursal->id, $alumno->sucursal_id);
    }

    public function test_admin_puede_ver_el_perfil_de_un_alumno_de_su_sucursal(): void
    {
        $this->withoutVite();

        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();
        $alumno = Alumno::factory()->create(['sucursal_id' => $sucursal->id]);

        $response = $this->actingAs($admin)->get(route('alumnos.show', $alumno));

        $response->assertOk();
        $response->assertSee($alumno->nombreCompleto());
    }

    public function test_admin_no_puede_ver_alumno_de_otra_sucursal(): void
    {
        $this->withoutVite();

        $sucursal = Sucursal::factory()->create();
        $otraSucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();
        $alumno = Alumno::factory()->create(['sucursal_id' => $otraSucursal->id]);

        $this->actingAs($admin)->get(route('alumnos.show', $alumno))->assertForbidden();
    }

    public function test_admin_ve_el_formulario_de_edicion_de_un_alumno(): void
    {
        $this->withoutVite();

        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();
        $alumno = Alumno::factory()->create(['sucursal_id' => $sucursal->id]);

        $response = $this->actingAs($admin)->get(route('alumnos.edit', $alumno));

        $response->assertOk();
        $response->assertSee($alumno->nombre);
    }

    public function test_admin_edita_un_alumno_y_registra_cambio_de_nivel(): void
    {
        $this->withoutVite();

        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();
        $nivelInicial = Nivel::factory()->create();
        $nivelNuevo = Nivel::factory()->create();

        $alumno = Alumno::factory()->create([
            'sucursal_id' => $sucursal->id,
            'nivel_id' => $nivelInicial->id,
        ]);

        $response = $this->actingAs($admin)->put(route('alumnos.update', $alumno), [
            'nombre' => $alumno->nombre,
            'apellidos' => $alumno->apellidos,
            'fecha_nacimiento' => $alumno->fecha_nacimiento->format('Y-m-d'),
            'estado' => 'activo',
            'nivel_id' => $nivelNuevo->id,
            'tutor_nombre' => $alumno->tutorUser->name,
            'tutor_email' => $alumno->tutorUser->email,
        ]);

        $response->assertRedirect(route('alumnos.show', $alumno));
        $alumno->refresh();
        $this->assertEquals($nivelNuevo->id, $alumno->nivel_id);

        $this->assertDatabaseHas('alumno_nivel_historial', [
            'alumno_id' => $alumno->id,
            'nivel_id' => $nivelNuevo->id,
            'fecha_fin' => null,
        ]);
    }

    public function test_admin_da_de_baja_a_un_alumno(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();
        $alumno = Alumno::factory()->create(['sucursal_id' => $sucursal->id, 'estado' => EstadoAlumno::Activo->value]);

        $this->actingAs($admin)->patch(route('alumnos.baja', $alumno))->assertRedirect();

        $alumno->refresh();
        $this->assertEquals(EstadoAlumno::BajaTemporal, $alumno->estado);
    }

    public function test_admin_reactiva_un_alumno(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();
        $alumno = Alumno::factory()->create(['sucursal_id' => $sucursal->id, 'estado' => EstadoAlumno::BajaTemporal->value]);

        $this->actingAs($admin)->patch(route('alumnos.reactivar', $alumno))->assertRedirect();

        $alumno->refresh();
        $this->assertEquals(EstadoAlumno::Activo, $alumno->estado);
    }
}
