<?php

namespace App\Http\Controllers\Concerns;

use App\Enums\EstadoCita;
use App\Models\Alumno;
use App\Models\Cita;
use App\Models\Horario;
use Illuminate\Support\Carbon;

trait AgendaCitasIniciales
{
    /**
     * Cuántas ocurrencias de "Cita" se agendan automáticamente al aprobar
     * una reserva.
     */
    private const CITAS_INICIALES = 4;

    /**
     * Agenda las próximas ocurrencias de "Cita" para el día de la semana
     * del horario, a partir de hoy.
     */
    private function agendarPrimerasCitas(Horario $horario, Alumno $alumno, int $registradoPorUserId): void
    {
        $fecha = Carbon::today();
        $creadas = 0;

        while ($creadas < self::CITAS_INICIALES) {
            if ($fecha->isoWeekday() === $horario->dia_semana->value) {
                Cita::firstOrCreate(
                    [
                        'horario_id' => $horario->id,
                        'alumno_id' => $alumno->id,
                        'fecha' => $fecha->toDateString(),
                    ],
                    [
                        'sucursal_id' => $horario->sucursal_id,
                        'hora_inicio' => $horario->hora_inicio,
                        'hora_fin' => $horario->hora_fin,
                        'estado' => EstadoCita::Programada->value,
                        'registrado_por' => $registradoPorUserId,
                    ]
                );

                $creadas++;
            }

            $fecha = $fecha->copy()->addDay();
        }
    }
}
