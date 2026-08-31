<?php

namespace Tests\Feature;

use App\Enums\Rol;
use App\Models\Alumno;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegistroTutorTest extends TestCase
{
    use RefreshDatabase;

    private function crearAlumnoConCuentaPendiente(): array
    {
        $sucursal = Sucursal::factory()->create();

        $tutor = User::factory()->tutor()->create([
            'email' => 'monica.cruz@example.com',
        ]);
        $tutor->forceFill(['password_configurada' => false])->save();

        $alumno = Alumno::factory()->create([
            'sucursal_id' => $sucursal->id,
            'tutor_user_id' => $tutor->id,
            'nombre' => 'Valentina',
            'apellidos' => 'Cruz Ortega',
            'fecha_nacimiento' => '2017-03-15',
        ]);

        return [$alumno, $tutor];
    }

    public function test_alta_de_alumno_crea_tutor_con_cuenta_pendiente_de_activar(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();

        $this->actingAs($admin)->post(route('alumnos.store'), [
            'nombre' => 'Nuevo',
            'apellidos' => 'Alumno',
            'fecha_nacimiento' => '2016-01-01',
            'tutor_nombre' => 'Tutor Nuevo',
            'tutor_email' => 'tutor.nuevo@example.com',
        ]);

        $tutor = User::where('email', 'tutor.nuevo@example.com')->first();

        $this->assertNotNull($tutor);
        $this->assertFalse($tutor->password_configurada);
        $this->assertEquals(Rol::Alumno, $tutor->role);
    }

    public function test_tutor_activa_su_cuenta_con_los_datos_correctos_del_alumno(): void
    {
        [, $tutor] = $this->crearAlumnoConCuentaPendiente();

        $response = $this->post(route('registro'), [
            'tutor_email' => $tutor->email,
            'alumno_nombre' => 'Valentina',
            'alumno_apellidos' => 'Cruz Ortega',
            'alumno_fecha_nacimiento' => '2017-03-15',
            'password' => 'contrasena-nueva-123',
            'password_confirmation' => 'contrasena-nueva-123',
        ]);

        $response->assertRedirect(route('portal.dashboard'));
        $this->assertAuthenticatedAs($tutor->fresh());

        $tutor->refresh();
        $this->assertTrue($tutor->password_configurada);
        $this->assertTrue(Hash::check('contrasena-nueva-123', $tutor->password));
    }

    public function test_tutor_no_puede_activar_su_cuenta_si_los_datos_del_alumno_no_coinciden(): void
    {
        [, $tutor] = $this->crearAlumnoConCuentaPendiente();

        $response = $this->post(route('registro'), [
            'tutor_email' => $tutor->email,
            'alumno_nombre' => 'Valentina',
            'alumno_apellidos' => 'Apellido Incorrecto',
            'alumno_fecha_nacimiento' => '2017-03-15',
            'password' => 'contrasena-nueva-123',
            'password_confirmation' => 'contrasena-nueva-123',
        ]);

        $response->assertSessionHasErrors('tutor_email');
        $this->assertGuest();

        $tutor->refresh();
        $this->assertFalse($tutor->password_configurada);
    }

    public function test_no_se_puede_activar_una_cuenta_con_correo_inexistente(): void
    {
        $response = $this->post(route('registro'), [
            'tutor_email' => 'no-existe@example.com',
            'alumno_nombre' => 'Nadie',
            'alumno_apellidos' => 'Registrado',
            'alumno_fecha_nacimiento' => '2017-03-15',
            'password' => 'contrasena-nueva-123',
            'password_confirmation' => 'contrasena-nueva-123',
        ]);

        $response->assertSessionHasErrors('tutor_email');
        $this->assertGuest();
    }

    public function test_una_cuenta_ya_activada_no_se_puede_volver_a_registrar(): void
    {
        [, $tutor] = $this->crearAlumnoConCuentaPendiente();
        $tutor->forceFill(['password_configurada' => true])->save();

        $response = $this->post(route('registro'), [
            'tutor_email' => $tutor->email,
            'alumno_nombre' => 'Valentina',
            'alumno_apellidos' => 'Cruz Ortega',
            'alumno_fecha_nacimiento' => '2017-03-15',
            'password' => 'otra-contrasena-999',
            'password_confirmation' => 'otra-contrasena-999',
        ]);

        $response->assertSessionHasErrors('tutor_email');
        $this->assertGuest();
    }
}
