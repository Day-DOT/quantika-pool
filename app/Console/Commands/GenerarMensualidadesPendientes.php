<?php

namespace App\Console\Commands;

use App\Enums\ConceptoPago;
use App\Enums\EstadoAlumno;
use App\Enums\EstadoPago;
use App\Models\Alumno;
use App\Models\Pago;
use Illuminate\Console\Command;

class GenerarMensualidadesPendientes extends Command
{
    protected $signature = 'pagos:generar-mensualidades';

    protected $description = 'Genera automáticamente el pago pendiente del mes para cada alumno activo cuya fecha de corte ya llegó';

    public function handle(): int
    {
        $hoy = now()->toDateString();
        $generados = 0;

        $vencidos = Pago::where('estado', EstadoPago::Pendiente->value)
            ->whereNotNull('fecha_vencimiento')
            ->whereDate('fecha_vencimiento', '<', $hoy)
            ->update(['estado' => EstadoPago::Vencido->value]);

        Alumno::query()
            ->where('estado', EstadoAlumno::Activo->value)
            ->whereNotNull('plan_id')
            ->with('plan', 'ultimoPagoMensualidad')
            ->chunkById(100, function ($alumnos) use ($hoy, &$generados) {
                foreach ($alumnos as $alumno) {
                    if ($alumno->plan->precio === null) {
                        continue;
                    }

                    $proximaFecha = $alumno->proximaFechaPago();

                    if (! $proximaFecha) {
                        continue;
                    }

                    // Después de registrar un pago, agenda de inmediato el
                    // siguiente vencimiento para que aparezca en el calendario.
                    // Para alumnos sin pagos, conserva la fecha de inicio del
                    // servicio y espera hasta que llegue su primer vencimiento.
                    if ($alumno->ultimoPagoMensualidad?->estado === EstadoPago::Pendiente
                        || ($alumno->ultimoPagoMensualidad
                            && $alumno->ultimoPagoMensualidad->estado !== EstadoPago::Pagado
                            && $proximaFecha->toDateString() > $hoy)
                        || (! $alumno->ultimoPagoMensualidad && $proximaFecha->toDateString() > $hoy)) {
                        continue;
                    }

                    $yaExiste = Pago::where('alumno_id', $alumno->id)
                        ->where('concepto', ConceptoPago::Mensualidad->value)
                        ->where('fecha_vencimiento', $proximaFecha->toDateString())
                        ->exists();

                    if ($yaExiste) {
                        continue;
                    }

                    Pago::create([
                        'alumno_id' => $alumno->id,
                        'sucursal_id' => $alumno->sucursal_id,
                        'concepto' => ConceptoPago::Mensualidad->value,
                        'periodo' => $proximaFecha->format('Y-m'),
                        'monto' => $alumno->plan->precio,
                        'fecha_vencimiento' => $proximaFecha->toDateString(),
                        'estado' => EstadoPago::Pendiente->value,
                    ]);

                    $generados++;
                }
            });

        $this->info("Se marcaron {$vencidos} pagos como vencidos y se generaron {$generados} mensualidades pendientes.");

        return self::SUCCESS;
    }
}
