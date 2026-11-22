<?php

namespace Tests\Feature;

use App\Enums\EstadoAlumno;
use App\Enums\EstadoPago;
use App\Models\Alumno;
use App\Models\Carril;
use App\Models\Horario;
use App\Models\Inscripcion;
use App\Models\Instructor;
use App\Models\Pago;
use App\Models\Plan;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class CorteDePagosEInscripcionTest extends TestCase
{
    use RefreshDatabase;

    private Sucursal $sucursal;

    private User $admin;

    private Alumno $alumno;

    protected function setUp(): void
    {
        parent::setUp();

        $this->travelTo(Carbon::create(2026, 11, 16, 10));

        $this->sucursal = Sucursal::factory()->create();
        $this->admin = User::factory()->admin($this->sucursal->id)->create();
        $this->alumno = Alumno::factory()->create([
            'sucursal_id' => $this->sucursal->id,
            'plan_id' => Plan::factory()->create(['precio' => 650])->id,
            'estado' => EstadoAlumno::Activo->value,
            'nombre' => 'Sofía',
            'apellidos' => 'Ramírez',
        ]);
    }

    private function pago(array $datos = []): Pago
    {
        return Pago::factory()->create([
            'alumno_id' => $this->alumno->id,
            'sucursal_id' => $this->sucursal->id,
            'concepto' => 'mensualidad',
            'monto' => 650,
            'estado' => EstadoPago::Pendiente->value,
            'fecha_pago' => null,
            'metodo_pago' => null,
            ...$datos,
        ]);
    }

    /**
     * @return list<string>
     */
    private function cortes(): array
    {
        return Pago::where('alumno_id', $this->alumno->id)->orderBy('id')
            ->get()->map(fn (Pago $p) => $p->fecha_vencimiento?->toDateString().':'.$p->estado->value)->all();
    }

    // ------------------------------------------------------------------
    // BUG 1: QR separa anualidad y mensualidad
    // ------------------------------------------------------------------

    public function test_al_escanear_se_distingue_la_anualidad_vencida_de_la_mensualidad(): void
    {
        $this->pago(['concepto' => 'inscripcion', 'monto' => 1200, 'fecha_vencimiento' => '2026-11-06']);

        $aviso = fn () => $this->actingAs($this->admin)->post(route('asistencia.confirmar', $this->alumno->qr_token))->assertOk();

        $aviso()->assertSee('Anualidad vencida desde el 06/11/2026 · debe $1,200.00')->assertDontSee('Mensualidad vencida');

        $this->pago(['fecha_vencimiento' => '2026-11-10']);
        $aviso()->assertSee('Anualidad vencida desde el 06/11/2026')->assertSee('Mensualidad vencida desde el 10/11/2026 · debe $650.00');

        Pago::where('concepto', 'inscripcion')->update(['estado' => EstadoPago::Pagado->value]);
        $aviso()->assertDontSee('Anualidad vencida')->assertSee('Mensualidad vencida');
    }

    // ------------------------------------------------------------------
    // BUG 2: el pago liquida el cargo y respeta el ciclo de corte
    // ------------------------------------------------------------------

    public function test_registrar_un_pago_tardio_liquida_el_cargo_del_12_y_el_siguiente_corte_sigue_siendo_el_12(): void
    {
        $this->pago(['fecha_vencimiento' => '2026-11-12', 'periodo' => '2026-11']);

        $this->actingAs($this->admin)->post(route('pagos.store'), [
            'alumno_id' => $this->alumno->id, 'concepto' => 'mensualidad', 'monto' => 650,
            'fecha_vencimiento' => '2026-11-16', 'fecha_pago' => '2026-11-16', 'estado' => 'pagado', 'metodo_pago' => 'efectivo',
        ])->assertSessionHas('status');

        $this->assertSame(['2026-11-12:pagado', '2026-12-12:pendiente'], $this->cortes());
        $this->assertSame('2026-11-16', Pago::orderBy('id')->first()->fecha_pago->toDateString());

        // La tarea diaria no crea cobros huérfanos ni duplicados.
        $this->artisan('pagos:generar-mensualidades')->assertSuccessful();
        $this->assertSame(['2026-11-12:pagado', '2026-12-12:pendiente'], $this->cortes());
    }

    public function test_con_varios_cargos_atrasados_se_liquida_primero_el_mas_antiguo(): void
    {
        $this->pago(['fecha_vencimiento' => '2026-10-12', 'estado' => EstadoPago::Vencido->value]);
        $this->pago(['fecha_vencimiento' => '2026-11-12']);

        $this->actingAs($this->admin)->post(route('pagos.store'), [
            'alumno_id' => $this->alumno->id, 'concepto' => 'mensualidad', 'monto' => 650, 'estado' => 'pagado',
        ]);

        $this->assertSame(['2026-10-12:pagado', '2026-11-12:pendiente'], $this->cortes());
    }

    public function test_un_pago_de_otro_concepto_o_pendiente_no_liquida_cargos(): void
    {
        $this->pago(['fecha_vencimiento' => '2026-11-12']);

        $this->actingAs($this->admin)->post(route('pagos.store'), [
            'alumno_id' => $this->alumno->id, 'concepto' => 'otro', 'monto' => 100, 'estado' => 'pagado',
        ]);
        $this->actingAs($this->admin)->post(route('pagos.store'), [
            'alumno_id' => $this->alumno->id, 'concepto' => 'mensualidad', 'monto' => 650, 'estado' => 'pendiente', 'fecha_vencimiento' => '2027-01-12',
        ]);

        $this->assertSame(3, Pago::count());
        $this->assertSame(EstadoPago::Pendiente, Pago::orderBy('id')->first()->estado);
    }

    public function test_marcar_pagado_el_16_conserva_el_ciclo_del_12(): void
    {
        $pago = $this->pago(['fecha_vencimiento' => '2026-11-12']);

        $this->actingAs($this->admin)->patch(route('pagos.marcar-pagado', $pago), ['metodo_pago' => 'efectivo']);

        $this->assertSame(['2026-11-12:pagado', '2026-12-12:pendiente'], $this->cortes());
    }

    // ------------------------------------------------------------------
    // HISTORIAL EN EL PERFIL Y BUSCADOR
    // ------------------------------------------------------------------

    public function test_el_perfil_del_alumno_muestra_su_historial_completo_de_pagos(): void
    {
        $this->pago(['concepto' => 'inscripcion', 'monto' => 1200, 'fecha_vencimiento' => '2026-09-01', 'fecha_pago' => '2026-09-03', 'estado' => EstadoPago::Pagado->value, 'metodo_pago' => 'transferencia']);
        $this->pago(['fecha_vencimiento' => '2026-11-12']);

        $this->actingAs($this->admin)->get(route('alumnos.show', $this->alumno))
            ->assertOk()
            ->assertSee('Historial de pagos')
            ->assertSee('Inscripción (anualidad)')
            ->assertSee('01/09/2026')
            ->assertSee('03/09/2026')
            ->assertSee('Transferencia')
            ->assertSee('$1,200.00')
            ->assertSee('Vencido')
            ->assertSee('Pagado');
    }

    public function test_el_buscador_filtra_por_nombre_apellido_matricula_y_estado(): void
    {
        $this->pago(['fecha_vencimiento' => '2026-11-12']);
        $otro = Alumno::factory()->create(['sucursal_id' => $this->sucursal->id, 'nombre' => 'Mateo', 'apellidos' => 'Cruz']);
        Pago::factory()->create(['alumno_id' => $otro->id, 'sucursal_id' => $this->sucursal->id, 'estado' => EstadoPago::Pagado->value, 'fecha_vencimiento' => '2026-11-05']);
        $ajeno = Alumno::factory()->create(['sucursal_id' => Sucursal::factory()->create()->id, 'nombre' => 'Sofía', 'apellidos' => 'Otra']);
        Pago::factory()->create(['alumno_id' => $ajeno->id, 'sucursal_id' => $ajeno->sucursal_id]);

        $buscar = fn (array $filtros) => $this->actingAs($this->admin)->get(route('pagos.buscar', $filtros))->assertOk();

        $buscar(['q' => 'sof'])->assertSee('Sofía Ramírez')->assertDontSee('Mateo Cruz')->assertDontSee('Sofía Otra');
        $buscar(['q' => 'ramírez sofía'])->assertSee('Sofía Ramírez');
        $buscar(['q' => $this->alumno->matricula])->assertSee('Sofía Ramírez')->assertDontSee('Mateo');
        $buscar(['q' => (string) $this->alumno->id])->assertSee('Sofía Ramírez');
        $buscar(['estado' => 'pagado'])->assertSee('Mateo Cruz')->assertDontSee('Sofía Ramírez');
        $buscar(['estado' => 'vencido'])->assertSee('Sofía Ramírez')->assertDontSee('Mateo Cruz');
        $buscar(['desde' => '2026-11-10', 'hasta' => '2026-11-30'])->assertSee('Sofía Ramírez')->assertDontSee('Mateo Cruz');
        $buscar(['q' => 'zzz'])->assertSee('No hay pagos que coincidan');
        $buscar([])->assertSee('Escribe un nombre');

        $this->actingAs($this->admin)->get(route('pagos.index', ['q' => 'mateo']))->assertOk()->assertSee('Mateo Cruz');
        $this->actingAs($this->admin)->get(route('pagos.buscar', ['desde' => 'no-es-fecha']))->assertSessionHasErrors('desde');
    }

    // ------------------------------------------------------------------
    // INSCRIPCIÓN UNIFICADA
    // ------------------------------------------------------------------

    private function grupo(int $dia, int $capacidad = 6, ?Sucursal $sucursal = null): Horario
    {
        $sucursal ??= $this->sucursal;

        return Horario::factory()->create([
            'sucursal_id' => $sucursal->id,
            'instructor_id' => Instructor::factory()->create(['sucursal_id' => $sucursal->id])->id,
            'carril_id' => Carril::factory()->create(['sucursal_id' => $sucursal->id])->id,
            'dia_semana' => $dia, 'capacidad_maxima' => $capacidad, 'activo' => true,
        ]);
    }

    private function datosAlta(array $extra = []): array
    {
        return ['nombre' => 'Nuevo', 'apellidos' => 'Alumno', 'fecha_nacimiento' => '2018-05-05', 'plan_id' => $this->alumno->plan_id, ...$extra];
    }

    public function test_al_inscribir_se_guardan_los_grupos_y_las_fechas_de_pago(): void
    {
        $lunes = $this->grupo(1);
        $miercoles = $this->grupo(3);

        $this->actingAs($this->admin)->post(route('alumnos.store'), $this->datosAlta([
            'fecha_corte_mensualidad' => '2026-11-20', 'monto_mensualidad' => 650,
            'fecha_pago_anualidad' => '2026-12-01', 'monto_anualidad' => 1200,
            'horario_ids' => [$lunes->id, $miercoles->id],
        ]))->assertSessionHasNoErrors();

        $nuevo = Alumno::where('nombre', 'Nuevo')->sole();

        $this->assertEqualsCanonicalizing([$lunes->id, $miercoles->id], $nuevo->inscripciones()->where('activa', true)->pluck('horario_id')->all());
        $this->assertSame(
            ['mensualidad:2026-11-20:650.00', 'inscripcion:2026-12-01:1200.00'],
            $nuevo->pagos()->orderBy('id')->get()->map(fn (Pago $p) => "{$p->concepto->value}:{$p->fecha_vencimiento->toDateString()}:{$p->monto}")->all()
        );

        // Aparece en el cronograma de clases desde sus grupos.
        $this->assertNotEmpty(\App\Support\ClasesDelAlumno::proximas($nuevo));

        // La mensualidad agendada sigue su ciclo: pagarla agenda el 20 del mes siguiente.
        $mensualidad = $nuevo->pagos()->where('concepto', 'mensualidad')->sole();
        $this->actingAs($this->admin)->patch(route('pagos.marcar-pagado', $mensualidad), ['metodo_pago' => 'efectivo']);
        $this->assertTrue($nuevo->pagos()->where('concepto', 'mensualidad')->whereDate('fecha_vencimiento', '2026-12-20')->exists());
    }

    public function test_si_un_grupo_no_tiene_cupo_no_se_inscribe_nada(): void
    {
        $lleno = $this->grupo(1, 1);
        Inscripcion::factory()->create(['alumno_id' => $this->alumno->id, 'horario_id' => $lleno->id, 'activa' => true]);
        $libre = $this->grupo(3);

        $this->actingAs($this->admin)->post(route('alumnos.store'), $this->datosAlta([
            'fecha_pago_anualidad' => '2026-12-01', 'monto_anualidad' => 1200,
            'horario_ids' => [$libre->id, $lleno->id],
        ]))->assertSessionHasErrors('horario_ids');

        $this->assertNull(Alumno::where('nombre', 'Nuevo')->first());
        $this->assertSame(1, Inscripcion::count());
        $this->assertSame(0, Pago::where('concepto', 'inscripcion')->count());
    }

    public function test_no_se_inscribe_en_grupos_de_otra_sucursal_y_las_fechas_piden_su_monto(): void
    {
        $ajeno = $this->grupo(1, 6, Sucursal::factory()->create());

        $this->actingAs($this->admin)->post(route('alumnos.store'), $this->datosAlta(['horario_ids' => [$ajeno->id]]))
            ->assertSessionHasErrors('horario_ids');

        $this->actingAs($this->admin)->post(route('alumnos.store'), $this->datosAlta(['fecha_corte_mensualidad' => '2026-11-20']))
            ->assertSessionHasErrors('monto_mensualidad');

        $this->actingAs($this->admin)->post(route('alumnos.store'), $this->datosAlta(['plan_id' => null, 'fecha_corte_mensualidad' => '2026-11-20', 'monto_mensualidad' => 650]))
            ->assertSessionHasErrors('plan_id');

        $this->assertNull(Alumno::where('nombre', 'Nuevo')->first());
    }

    public function test_el_formulario_de_inscripcion_ofrece_fechas_de_pago_y_horarios_con_su_cupo(): void
    {
        $this->grupo(1, 6);

        $this->actingAs($this->admin)->get(route('alumnos.index'))
            ->assertOk()
            ->assertSee('Fecha de corte de la mensualidad')
            ->assertSee('Fecha de pago de la anualidad')
            ->assertSee('Días y horarios de clase')
            ->assertSee('6 de 6 lugares');
    }
}
