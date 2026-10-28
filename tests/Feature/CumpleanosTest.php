<?php

namespace Tests\Feature;

use App\Enums\EstadoAlumno;
use App\Models\Alumno;
use App\Models\Sucursal;
use App\Models\User;
use App\Support\Cumpleanos;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class CumpleanosTest extends TestCase
{
    use RefreshDatabase;

    private Sucursal $sucursal;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Miércoles 28 de octubre de 2026.
        $this->travelTo(Carbon::create(2026, 10, 28, 9));

        $this->sucursal = Sucursal::factory()->create();
        $this->admin = User::factory()->admin($this->sucursal->id)->create();
    }

    private function alumno(string $nombre, string $nacimiento, array $extra = []): Alumno
    {
        return Alumno::factory()->create([
            'sucursal_id' => $this->sucursal->id,
            'nombre' => $nombre,
            'apellidos' => 'Prueba',
            'fecha_nacimiento' => $nacimiento,
            'estado' => EstadoAlumno::Activo->value,
            ...$extra,
        ]);
    }

    public function test_el_dashboard_avisa_los_cumpleanos_proximos_y_enlaza_al_calendario(): void
    {
        $this->alumno('Sofía', '2019-10-28');
        $this->alumno('Mateo', '2015-11-02');
        $this->alumno('Lejano', '2015-11-20');
        $this->alumno('Inactivo', '2016-10-29', ['estado' => EstadoAlumno::Inactivo->value]);

        $this->actingAs($this->admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertViewHas('cumpleanosProximos', fn ($proximos) => $proximos->count() === 2)
            ->assertSee(route('cumpleanos.index'))
            ->assertSee('Hoy: Sofía 🎉', false);
    }

    public function test_sin_cumpleanos_proximos_no_hay_aviso(): void
    {
        $this->alumno('Lejano', '2015-12-20');

        $this->actingAs($this->admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertViewHas('cumpleanosProximos', fn ($proximos) => $proximos->count() === 0)
            ->assertSee('Calendario de cumpleaños de los alumnos');
    }

    public function test_el_calendario_muestra_los_cumpleanos_del_mes_y_los_proximos(): void
    {
        $this->alumno('Sofía', '2019-10-28');
        $this->alumno('Mateo', '2015-11-02');
        $this->alumno('Valeria', '2018-12-05');

        $respuesta = $this->actingAs($this->admin)->get(route('cumpleanos.index'))->assertOk();

        $this->assertSame([28], $respuesta->viewData('cumpleanosDelMes')->keys()->all());
        $respuesta->assertSee('Octubre 2026')
            ->assertSee('Sofía Prueba')
            ->assertSee('Mateo Prueba')
            ->assertSee('¡Hoy!', false)
            ->assertDontSee('Valeria Prueba');

        // Navegar a diciembre.
        $diciembre = $this->actingAs($this->admin)->get(route('cumpleanos.index', ['mes' => '2026-12']))->assertOk();
        $this->assertSame(8, $diciembre->viewData('cumpleanosDelMes')->get(5)->first()['cumple']);

        // Un mes inválido vuelve al actual.
        $this->actingAs($this->admin)->get(route('cumpleanos.index', ['mes' => 'abc']))->assertOk()->assertSee('Octubre 2026');
    }

    public function test_no_mezcla_alumnos_de_otra_sucursal(): void
    {
        $this->alumno('Otra', '2019-10-29', ['sucursal_id' => Sucursal::factory()->create()->id]);

        $this->actingAs($this->admin)
            ->get(route('admin.dashboard'))
            ->assertViewHas('cumpleanosProximos', fn ($proximos) => $proximos->count() === 0);
    }

    public function test_el_super_admin_tambien_tiene_el_acceso_con_el_aviso(): void
    {
        $this->alumno('Sofía', '2019-10-28');

        $this->actingAs(User::factory()->superAdmin()->create())
            ->get(route('super-admin.dashboard'))
            ->assertOk()
            ->assertSee(route('cumpleanos.index'))
            ->assertSee('Hoy: Sofía 🎉', false);
    }

    public function test_quien_nacio_un_29_de_febrero_lo_celebra_el_28_en_anios_no_bisiestos(): void
    {
        $this->travelTo(Carbon::create(2027, 2, 25, 9));
        $this->alumno('Bisiesto', '2016-02-29');

        $proximo = Cumpleanos::proximos($this->sucursal->id)->sole();

        $this->assertSame('2027-02-28', $proximo['fecha']->toDateString());
        $this->assertSame(11, $proximo['cumple']);
    }

    public function test_los_cumpleanos_de_inicio_de_anio_aparecen_a_fin_de_diciembre(): void
    {
        $this->travelTo(Carbon::create(2026, 12, 29, 9));
        $this->alumno('Enero', '2017-01-02');

        $proximo = Cumpleanos::proximos($this->sucursal->id)->sole();

        $this->assertSame('2027-01-02', $proximo['fecha']->toDateString());
        $this->assertSame(10, $proximo['cumple']);
    }
}
