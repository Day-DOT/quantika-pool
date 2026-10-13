<?php

namespace Tests\Feature;

use App\Enums\ConceptoPago;
use App\Enums\EstadoCita;
use App\Enums\EstadoPago;
use App\Models\Alumno;
use App\Models\Cita;
use App\Models\Horario;
use App\Models\Inscripcion;
use App\Models\Pago;
use App\Models\Plan;
use App\Models\Sucursal;
use App\Models\User;
use App\Support\ClasesDelAlumno;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Migración que limpia las clases apartadas que ya no se usan y corrige
 * las mensualidades pendientes generadas con el precio del plan.
 */
class CorreccionDeClasesYMensualidadesTest extends TestCase
{
    use RefreshDatabase;

    private function correrMigracion(): void
    {
        (require database_path('migrations/2026_10_12_090100_limpia_clases_canceladas_y_corrige_mensualidades.php'))->up();
    }

    public function test_elimina_las_clases_apartadas_y_canceladas_pero_conserva_todo_lo_que_tiene_informacion(): void
    {
        $sucursal = Sucursal::factory()->create();
        $alumno = Alumno::factory()->create(['sucursal_id' => $sucursal->id]);
        $grupo = Horario::factory()->create(['sucursal_id' => $sucursal->id]);
        $otroGrupo = Horario::factory()->create(['sucursal_id' => $sucursal->id]);

        Inscripcion::factory()->create(['alumno_id' => $alumno->id, 'horario_id' => $grupo->id, 'activa' => true]);

        $cita = fn (array $datos) => Cita::factory()->create([
            'alumno_id' => $alumno->id,
            'horario_id' => $grupo->id,
            'sucursal_id' => $sucursal->id,
            'estado' => EstadoCita::Programada->value,
            ...$datos,
        ]);

        // Se eliminan: lugares apartados y cancelaciones que nunca se usaron.
        $apartada = $cita(['fecha' => today()->addDays(7)]);
        $apartadaHoy = $cita(['fecha' => today()]);
        $cancelada = $cita(['fecha' => today()->addDays(14), 'estado' => EstadoCita::Cancelada->value]);

        // Se conservan: asistencia, faltas, clases pasadas y de una sola fecha.
        $asistencia = $cita(['fecha' => today()->addDays(21), 'asistio' => true, 'estado' => EstadoCita::Completada->value]);
        $falta = $cita(['fecha' => today()->addDays(28), 'asistio' => false, 'estado' => EstadoCita::Completada->value]);
        $pasada = $cita(['fecha' => today()->subDays(7)]);
        $extra = $cita(['fecha' => today()->addDays(3), 'horario_id' => $otroGrupo->id, 'es_extra' => true]);
        $reposicion = $cita(['fecha' => today()->addDays(4), 'horario_id' => $otroGrupo->id, 'reposicion_de_id' => $falta->id]);
        $reagendada = $cita(['fecha' => today()->addDays(5), 'horario_id' => $otroGrupo->id]);
        $extraCancelada = $cita(['fecha' => today()->addDays(6), 'horario_id' => $otroGrupo->id, 'es_extra' => true, 'estado' => EstadoCita::Cancelada->value]);

        $this->correrMigracion();

        foreach ([$apartada, $apartadaHoy, $cancelada] as $eliminada) {
            $this->assertModelMissing($eliminada);
        }

        foreach ([$asistencia, $falta, $pasada, $extra, $reposicion, $reagendada, $extraCancelada] as $conservada) {
            $this->assertModelExists($conservada);
        }
    }

    public function test_marca_la_fecha_original_de_las_clases_que_ya_se_habian_reagendado(): void
    {
        $this->travelTo(Carbon::create(2026, 10, 13, 9)); // martes

        $sucursal = Sucursal::factory()->create();
        $alumno = Alumno::factory()->create(['sucursal_id' => $sucursal->id]);
        $suGrupo = Horario::factory()->create(['sucursal_id' => $sucursal->id, 'dia_semana' => 2]); // martes
        $otroGrupo = Horario::factory()->create(['sucursal_id' => $sucursal->id, 'dia_semana' => 2]);

        // Inscrito el viernes 9: se apartaron los martes 13, 20, 27 y 3 nov.
        $inscrito = Carbon::create(2026, 10, 9, 16, 40, 20);

        Inscripcion::factory()->create([
            'alumno_id' => $alumno->id,
            'horario_id' => $suGrupo->id,
            'activa' => true,
            'fecha_inicio' => '2026-10-09',
            'created_at' => $inscrito,
            'updated_at' => $inscrito,
        ]);

        $apartar = fn (string $fecha, Horario $grupo, ?Carbon $editada = null) => Cita::factory()->create([
            'alumno_id' => $alumno->id,
            'horario_id' => $grupo->id,
            'sucursal_id' => $sucursal->id,
            'fecha' => $fecha,
            'estado' => EstadoCita::Programada->value,
            'created_at' => $inscrito,
            'updated_at' => $editada ?? $inscrito,
        ]);

        // La del martes 13 se movió a otro grupo ese mismo día.
        $movida = $apartar('2026-10-13', $otroGrupo, $inscrito->copy()->addMinutes(2));
        $apartar('2026-10-20', $suGrupo);
        $apartar('2026-10-27', $suGrupo);
        $apartar('2026-11-03', $suGrupo);

        $this->correrMigracion();

        // Queda la marca en su grupo el martes 13 y la clase movida intacta.
        $marca = Cita::where('alumno_id', $alumno->id)->where('horario_id', $suGrupo->id)->sole();

        $this->assertSame(EstadoCita::Reagendada, $marca->estado);
        $this->assertSame('2026-10-13', $marca->fecha->toDateString());
        $this->assertModelExists($movida);

        // Resultado: hoy tiene clase solo en el grupo al que se movió.
        $hoy = ClasesDelAlumno::enRango($alumno, today(), today());

        $this->assertCount(1, $hoy);
        $this->assertSame($otroGrupo->id, $hoy->first()['cita']->horario_id);
        $this->assertSame(ClasesDelAlumno::TIPO_REAGENDADA, $hoy->first()['tipo']);

        // Y las siguientes semanas vuelve a su grupo.
        $this->assertSame(
            ['2026-10-13', '2026-10-20', '2026-10-27', '2026-11-03', '2026-11-10'],
            ClasesDelAlumno::proximas($alumno)->map(fn (array $clase) => $clase['cita']->fecha->toDateString())->all()
        );
    }

    public function test_corrige_solo_las_mensualidades_que_el_sistema_genero_con_el_precio_del_plan(): void
    {
        $sucursal = Sucursal::factory()->create();
        $plan = Plan::factory()->create(['precio' => 1100]);
        $admin = User::factory()->admin($sucursal->id)->create();

        /**
         * Crea un alumno con una mensualidad pagada y la siguiente pendiente.
         */
        $caso = function (float $pagado, float $pendiente, array $datosPendiente = []) use ($sucursal, $plan): Pago {
            $alumno = Alumno::factory()->create(['sucursal_id' => $sucursal->id, 'plan_id' => $plan->id]);

            $base = [
                'alumno_id' => $alumno->id,
                'sucursal_id' => $sucursal->id,
                'concepto' => ConceptoPago::Mensualidad->value,
                'registrado_por' => null,
            ];

            Pago::factory()->create([...$base, 'estado' => EstadoPago::Pagado->value, 'monto' => $pagado, 'fecha_vencimiento' => today()->subDays(5)]);

            return Pago::factory()->create([
                ...$base,
                'estado' => EstadoPago::Pendiente->value,
                'monto' => $pendiente,
                'fecha_vencimiento' => today()->subDays(5)->addMonthNoOverflow(),
                ...$datosPendiente,
            ]);
        };

        $conDescuento = $caso(pagado: 990, pendiente: 1100);
        $pagoCompleto = $caso(pagado: 1100, pendiente: 1100);
        $capturadaAMano = $caso(pagado: 990, pendiente: 1100, datosPendiente: ['registrado_por' => $admin->id]);
        $pagoSimbolico = $caso(pagado: 1, pendiente: 1100);
        $pagoMayor = $caso(pagado: 1500, pendiente: 1100);
        $yaConOtroMonto = $caso(pagado: 990, pendiente: 900);

        $editada = $caso(pagado: 990, pendiente: 1100);
        $editada->forceFill(['updated_at' => now()->addHour()])->saveQuietly();

        $this->correrMigracion();

        $this->assertEquals(990, $conDescuento->fresh()->monto);

        // Todo lo demás conserva su monto: no es un caso inequívoco.
        $this->assertEquals(1100, $pagoCompleto->fresh()->monto);
        $this->assertEquals(1100, $capturadaAMano->fresh()->monto);
        $this->assertEquals(1100, $pagoSimbolico->fresh()->monto);
        $this->assertEquals(1100, $pagoMayor->fresh()->monto);
        $this->assertEquals(900, $yaConOtroMonto->fresh()->monto);
        $this->assertEquals(1100, $editada->fresh()->monto);
    }
}
