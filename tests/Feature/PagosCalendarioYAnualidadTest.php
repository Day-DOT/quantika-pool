<?php

namespace Tests\Feature;

use App\Enums\EstadoAlumno;
use App\Enums\EstadoPago;
use App\Enums\MetodoPago;
use App\Models\Alumno;
use App\Models\Carril;
use App\Models\Horario;
use App\Models\Inscripcion;
use App\Models\Instructor;
use App\Models\Nivel;
use App\Models\Pago;
use App\Models\Sucursal;
use App\Models\User;
use App\Support\Anualidades;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PagosCalendarioYAnualidadTest extends TestCase
{
    use RefreshDatabase;

    private Sucursal $sucursal;

    private User $admin;

    private Alumno $alumno;

    protected function setUp(): void
    {
        parent::setUp();

        $this->travelTo(Carbon::create(2026, 11, 6, 10));

        $this->sucursal = Sucursal::factory()->create();
        $this->admin = User::factory()->admin($this->sucursal->id)->create();
        $this->alumno = Alumno::factory()->create([
            'sucursal_id' => $this->sucursal->id,
            'nombre' => 'Sofía',
            'apellidos' => 'Ramírez',
            'estado' => EstadoAlumno::Activo->value,
        ]);
    }

    private function pago(array $datos = []): Pago
    {
        return Pago::factory()->create([
            'alumno_id' => $this->alumno->id,
            'sucursal_id' => $this->sucursal->id,
            'estado' => EstadoPago::Pendiente->value,
            'fecha_pago' => null,
            'metodo_pago' => null,
            ...$datos,
        ]);
    }

    // ------------------------------------------------------------------
    // CALENDARIO DE PAGOS
    // ------------------------------------------------------------------

    public function test_un_dia_31_la_navegacion_entre_meses_no_salta_ni_se_queda_en_el_mismo_mes(): void
    {
        $this->travelTo(Carbon::create(2026, 10, 31, 10));

        foreach (['2026-09' => 9, '2026-11' => 11, '2027-02' => 2] as $mes => $esperado) {
            $calendario = $this->actingAs($this->admin)->get(route('pagos.index', ['mes' => $mes]))->assertOk()->viewData('calendarioMes');
            $this->assertSame($esperado, $calendario->month, $mes);
        }

        // Desde noviembre, "→" lleva a diciembre y "←" a octubre.
        $this->actingAs($this->admin)->get(route('pagos.index', ['mes' => '2026-11']))
            ->assertSee(route('pagos.index', ['mes' => '2026-12']), false)
            ->assertSee(route('pagos.index', ['mes' => '2026-10']), false);
    }

    public function test_un_pago_registrado_sin_vencimiento_aparece_el_dia_en_que_se_pago(): void
    {
        $this->pago(['fecha_vencimiento' => null, 'fecha_pago' => '2026-11-03', 'estado' => EstadoPago::Pagado->value]);

        $pagos = $this->actingAs($this->admin)->get(route('pagos.index', ['mes' => '2026-11']))->assertOk()->viewData('calendarioPagos');

        $this->assertCount(1, $pagos);
        $this->assertSame('2026-11-03', $pagos->first()->fechaCalendario()->toDateString());
    }

    public function test_el_calendario_distingue_la_anualidad_y_avisa_si_esta_vencida(): void
    {
        $this->pago(['concepto' => 'mensualidad', 'fecha_vencimiento' => '2026-11-10']);
        $this->pago(['concepto' => 'inscripcion', 'fecha_vencimiento' => '2026-11-02', 'monto' => 1200]);

        $this->actingAs($this->admin)->get(route('pagos.index'))
            ->assertOk()
            ->assertSee('Anualidad (inscripción)')
            ->assertSee('calendar-event-purple', false)
            ->assertSee('Anualidad vencida')
            ->assertSee('Mensualidad');
    }

    public function test_el_concepto_inscripcion_dice_anualidad(): void
    {
        $this->assertSame('Inscripción (anualidad)', \App\Enums\ConceptoPago::Inscripcion->label());
    }

    public function test_al_marcar_pagado_se_registra_el_metodo_de_pago(): void
    {
        $pago = $this->pago(['fecha_vencimiento' => '2026-11-10']);

        $this->actingAs($this->admin)->get(route('pagos.index'))->assertSee('name="metodo_pago"', false);

        $this->actingAs($this->admin)
            ->patch(route('pagos.marcar-pagado', $pago), ['metodo_pago' => 'transferencia'])
            ->assertSessionHas('status', 'Pago marcado como pagado (Transferencia).');

        $this->assertSame(MetodoPago::Transferencia, $pago->fresh()->metodo_pago);
        $this->assertSame(EstadoPago::Pagado, $pago->fresh()->estado);

        $otro = $this->pago(['fecha_vencimiento' => '2026-11-12']);
        $this->actingAs($this->admin)
            ->patch(route('pagos.marcar-pagado', $otro), ['metodo_pago' => 'bitcoin'])
            ->assertSessionHasErrors('metodo_pago');
        $this->assertSame(EstadoPago::Pendiente, $otro->fresh()->estado);
    }

    // ------------------------------------------------------------------
    // COMPROBANTES
    // ------------------------------------------------------------------

    public function test_el_comprobante_subido_se_puede_ver(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin)->post(route('pagos.store'), [
            'alumno_id' => $this->alumno->id,
            'concepto' => 'mensualidad',
            'monto' => 650,
            'fecha_vencimiento' => '2026-11-10',
            'estado' => 'pagado',
            'metodo_pago' => 'transferencia',
            'comprobante' => UploadedFile::fake()->image('comprobante.jpg'),
        ])->assertSessionHasNoErrors();

        $pago = Pago::whereNotNull('comprobante_path')->sole();

        $this->actingAs($this->admin)->get(route('pagos.comprobante', $pago))->assertOk();

        // Hay enlace en el historial del alumno, en "Editar" y en el calendario.
        $this->actingAs($this->admin)->get(route('pagos.alumno', $this->alumno))->assertSee(route('pagos.comprobante', $pago), false);
        $this->actingAs($this->admin)->get(route('pagos.edit', $pago))->assertSee('Ver comprobante actual');
        $this->actingAs($this->admin)->get(route('pagos.index'))->assertSee(route('pagos.comprobante', $pago), false);

        // Un administrador de otra sucursal no lo puede ver.
        $otraSucursal = User::factory()->admin(Sucursal::factory()->create()->id)->create();
        $this->actingAs($otraSucursal)->get(route('pagos.comprobante', $pago))->assertForbidden();
    }

    public function test_si_el_archivo_del_comprobante_ya_no_existe_responde_404(): void
    {
        Storage::fake('public');
        $pago = $this->pago(['comprobante_path' => 'comprobantes/no-existe.jpg']);

        $this->actingAs($this->admin)->get(route('pagos.comprobante', $pago))->assertNotFound();
    }

    // ------------------------------------------------------------------
    // ANUALIDAD
    // ------------------------------------------------------------------

    public function test_al_pagar_la_anualidad_se_agenda_la_del_anio_siguiente(): void
    {
        $anualidad = $this->pago(['concepto' => 'inscripcion', 'fecha_vencimiento' => '2026-11-06', 'monto' => 1200, 'periodo' => '2026-11']);

        $this->actingAs($this->admin)->patch(route('pagos.marcar-pagado', $anualidad), ['metodo_pago' => 'efectivo']);

        $siguiente = Pago::where('concepto', 'inscripcion')->where('estado', EstadoPago::Pendiente->value)->sole();
        $this->assertSame('2027-11-06', $siguiente->fecha_vencimiento->toDateString());
        $this->assertSame('1200.00', $siguiente->monto);

        // Volver a marcarla o correr la tarea diaria no la duplica.
        $this->assertSame(0, Anualidades::agendarPendientes());
        $this->assertSame(2, Pago::where('concepto', 'inscripcion')->count());
    }

    public function test_la_tarea_diaria_agenda_la_anualidad_de_quien_ya_la_habia_pagado(): void
    {
        $this->pago(['concepto' => 'inscripcion', 'fecha_vencimiento' => null, 'fecha_pago' => '2026-08-20', 'estado' => EstadoPago::Pagado->value, 'monto' => 1000]);

        // Un alumno dado de baja no genera anualidad.
        $baja = Alumno::factory()->create(['sucursal_id' => $this->sucursal->id, 'estado' => EstadoAlumno::Inactivo->value]);
        Pago::factory()->create(['alumno_id' => $baja->id, 'sucursal_id' => $this->sucursal->id, 'concepto' => 'inscripcion', 'estado' => EstadoPago::Pagado->value, 'fecha_vencimiento' => '2026-08-20']);

        $this->artisan('pagos:generar-mensualidades')->assertSuccessful();

        $siguiente = Pago::where('concepto', 'inscripcion')->where('estado', EstadoPago::Pendiente->value)->sole();
        $this->assertSame($this->alumno->id, $siguiente->alumno_id);
        $this->assertSame('2027-08-20', $siguiente->fecha_vencimiento->toDateString());
    }

    public function test_el_tutor_ve_un_aviso_si_la_anualidad_esta_vencida_pero_puede_seguir_usando_el_portal(): void
    {
        $tutor = User::factory()->tutor()->create();
        $this->alumno->update(['tutor_user_id' => $tutor->id]);

        $this->actingAs($tutor)->get(route('portal.dashboard'))->assertOk()->assertDontSee('Se venció la anualidad');

        $this->pago(['concepto' => 'inscripcion', 'fecha_vencimiento' => '2026-11-01', 'monto' => 1200]);

        $this->actingAs($tutor)->get(route('portal.dashboard'))
            ->assertOk()
            ->assertSee('Se venció la anualidad de Sofía Ramírez')
            ->assertSee('Venció el 01/11/2026');

        // Una mensualidad vencida no muestra este aviso.
        Pago::where('concepto', 'inscripcion')->update(['estado' => EstadoPago::Pagado->value]);
        $this->pago(['concepto' => 'mensualidad', 'fecha_vencimiento' => '2026-11-01']);
        $this->actingAs($tutor)->get(route('portal.dashboard'))->assertOk()->assertDontSee('Se venció la anualidad');
    }

    // ------------------------------------------------------------------
    // AGENDA DEL INSTRUCTOR
    // ------------------------------------------------------------------

    public function test_la_agenda_del_instructor_muestra_nombre_edad_y_nivel_a_simple_vista(): void
    {
        $nivel = Nivel::factory()->create(['nombre' => 'Delfines']);
        $this->alumno->update(['fecha_nacimiento' => '2019-03-10', 'nivel_id' => $nivel->id]);
        $horario = Horario::factory()->create([
            'sucursal_id' => $this->sucursal->id,
            'instructor_id' => Instructor::factory()->create(['sucursal_id' => $this->sucursal->id])->id,
            'carril_id' => Carril::factory()->create(['sucursal_id' => $this->sucursal->id])->id,
            'dia_semana' => 1,
            'activo' => true,
        ]);
        Inscripcion::factory()->create(['alumno_id' => $this->alumno->id, 'horario_id' => $horario->id, 'activa' => true]);

        $this->actingAs($horario->instructor->user)
            ->get(route('instructor.agenda'))
            ->assertOk()
            ->assertDontSee('Ver alumnos inscritos')
            ->assertSeeInOrder(['Sofía Ramírez', '7 años', $this->alumno->fresh()->nombreNivelConSubNivel()]);
    }
}
