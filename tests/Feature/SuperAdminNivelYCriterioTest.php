<?php

namespace Tests\Feature;

use App\Models\CriterioEvaluacion;
use App\Models\Nivel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SuperAdminNivelYCriterioTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_puede_crear_un_nivel(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)->post(route('super-admin.niveles.store'), [
            'orden' => 13,
            'nombre' => 'Kraken',
            'categoria' => 'Élite',
            'categoria_edad' => 'Niños',
            'activo' => '1',
        ])->assertRedirect(route('niveles.index'));

        $this->assertDatabaseHas('niveles', ['nombre' => 'Kraken', 'orden' => 13]);
    }

    public function test_la_imagen_de_un_nivel_nuevo_se_guarda_en_el_disco_persistente(): void
    {
        Storage::fake('public');

        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)->post(route('super-admin.niveles.store'), [
            'orden' => 14,
            'nombre' => 'Kraken',
            'categoria' => 'Élite',
            'categoria_edad' => 'Niños',
            'activo' => '1',
            'imagen' => UploadedFile::fake()->image('kraken.png'),
        ])->assertRedirect(route('niveles.index'));

        $nivel = Nivel::where('nombre', 'Kraken')->first();

        $this->assertNotNull($nivel->imagen);
        $this->assertStringStartsWith('storage/niveles/', $nivel->imagen);
        Storage::disk('public')->assertExists('niveles/'.basename($nivel->imagen));
    }

    public function test_se_puede_crear_mas_de_un_nivel_con_el_mismo_orden(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Nivel::factory()->create(['orden' => 5, 'categoria_edad' => 'Niños']);

        $this->actingAs($superAdmin)->post(route('super-admin.niveles.store'), [
            'orden' => 5,
            'nombre' => 'Repetido',
            'categoria' => 'Prueba',
            'categoria_edad' => 'Niños',
            'activo' => '1',
        ])->assertRedirect(route('niveles.index'));

        $this->assertEquals(2, Nivel::where('orden', 5)->where('categoria_edad', 'Niños')->count());
    }

    public function test_super_admin_puede_editar_y_desactivar_un_nivel(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $nivel = Nivel::factory()->create(['orden' => 20, 'activo' => true]);

        $this->actingAs($superAdmin)->put(route('super-admin.niveles.update', $nivel), [
            'orden' => $nivel->orden,
            'nombre' => $nivel->nombre,
            'categoria' => $nivel->categoria,
            'categoria_edad' => $nivel->categoria_edad,
            'activo' => '0',
        ])->assertRedirect(route('niveles.index'));

        $this->assertFalse($nivel->refresh()->activo);
    }

    public function test_super_admin_puede_crear_editar_y_eliminar_criterios_de_evaluacion(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $nivel = Nivel::factory()->create();

        $this->actingAs($superAdmin)->post(route('super-admin.criterios.store'), [
            'nivel_id' => $nivel->id,
            'nombre' => 'Patada de rana',
            'orden' => 1,
            'activo' => '1',
        ])->assertRedirect(route('super-admin.criterios.index'));

        $criterio = CriterioEvaluacion::where('nombre', 'Patada de rana')->first();
        $this->assertNotNull($criterio);

        $this->actingAs($superAdmin)->put(route('super-admin.criterios.update', $criterio), [
            'nivel_id' => $nivel->id,
            'nombre' => 'Patada de rana avanzada',
            'orden' => 1,
            'activo' => '1',
        ])->assertRedirect(route('super-admin.criterios.index'));

        $this->assertDatabaseHas('criterios_evaluacion', ['id' => $criterio->id, 'nombre' => 'Patada de rana avanzada']);

        $this->actingAs($superAdmin)
            ->delete(route('super-admin.criterios.destroy', $criterio))
            ->assertRedirect(route('super-admin.criterios.index'));

        $this->assertDatabaseMissing('criterios_evaluacion', ['id' => $criterio->id]);
    }
}
