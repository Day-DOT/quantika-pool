<?php

namespace Tests\Feature;

use App\Models\Alumno;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FirmaContratoTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_puede_ver_la_pantalla_de_firma(): void
    {
        Storage::fake('public');

        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();
        $alumno = Alumno::factory()->create(['sucursal_id' => $sucursal->id]);

        $this->actingAs($admin)
            ->get(route('alumnos.contrato.create', $alumno))
            ->assertOk()
            ->assertSee($alumno->nombreCompleto());
    }

    public function test_admin_puede_firmar_el_contrato_y_se_genera_el_pdf(): void
    {
        Storage::fake('public');

        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create(['name' => 'Admin Recepción']);
        $alumno = Alumno::factory()->create(['sucursal_id' => $sucursal->id, 'nombre' => 'Valentina']);

        $firmaFake = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=';

        $response = $this->actingAs($admin)->post(route('alumnos.contrato.store', $alumno), [
            'cuota_inscripcion' => 500,
            'lugar' => 'Toluca',
            'firma_titular_nombre' => 'Tutor de Prueba',
            'firma_titular_imagen' => $firmaFake,
            'firma_responsable_nombre' => 'Admin Recepción',
            'firma_responsable_imagen' => $firmaFake,
        ]);

        $alumno->refresh();

        $response->assertRedirect(route('alumnos.show', $alumno));
        $this->assertNotNull($alumno->contrato_firmado_path);
        Storage::disk('public')->assertExists($alumno->contrato_firmado_path);
    }

    public function test_no_se_puede_firmar_el_contrato_sin_las_firmas(): void
    {
        Storage::fake('public');

        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($sucursal->id)->create();
        $alumno = Alumno::factory()->create(['sucursal_id' => $sucursal->id]);

        $this->actingAs($admin)->post(route('alumnos.contrato.store', $alumno), [
            'firma_titular_nombre' => 'Tutor de Prueba',
            'firma_responsable_nombre' => 'Admin',
        ])->assertSessionHasErrors(['firma_titular_imagen', 'firma_responsable_imagen']);

        $this->assertNull($alumno->refresh()->contrato_firmado_path);
    }

    public function test_admin_de_otra_sucursal_no_puede_firmar_el_contrato(): void
    {
        Storage::fake('public');

        $sucursalAlumno = Sucursal::factory()->create();
        $otraSucursal = Sucursal::factory()->create();
        $admin = User::factory()->admin($otraSucursal->id)->create();
        $alumno = Alumno::factory()->create(['sucursal_id' => $sucursalAlumno->id]);

        $this->actingAs($admin)->get(route('alumnos.contrato.create', $alumno))->assertForbidden();
    }
}
