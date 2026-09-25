<?php

namespace App\Http\Controllers\Alumno\Concerns;

use App\Enums\EstadoCita;
use App\Models\Alumno;
use App\Models\Cita;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

trait CargaProximasClases
{
    private function proximasClases(Alumno $alumno, int $limite = 3): Collection
    {
        $hoy = today();
        $citas = $alumno->citas()
            ->with(['horario.nivel', 'horario.instructor.user', 'horario.carril'])
            ->whereIn('estado', [
                EstadoCita::Programada->value,
                EstadoCita::Confirmada->value,
                EstadoCita::Reagendada->value,
            ])
            ->whereDate('fecha', '>=', $hoy)
            ->get();

        $fechasExistentes = $alumno->citas()
            ->whereDate('fecha', '>=', $hoy)
            ->pluck('fecha')
            ->map(fn ($fecha) => Carbon::parse($fecha)->toDateString())
            ->all();

        $inscripciones = $alumno->inscripciones()
            ->activas()
            ->with(['horario.nivel', 'horario.instructor.user', 'horario.carril'])
            ->get();

        foreach ($inscripciones as $inscripcion) {
            $horario = $inscripcion->horario;

            if (! $horario?->activo) {
                continue;
            }

            $fecha = Carbon::parse(max(
                $hoy->toDateString(),
                $inscripcion->fecha_inicio?->toDateString() ?? $hoy->toDateString()
            ));

            for ($dias = 0; $dias < 35 && $citas->count() < $limite; $dias++, $fecha->addDay()) {
                if ($fecha->isoWeekday() !== $horario->dia_semana->value
                    || ($inscripcion->fecha_fin && $fecha->gt($inscripcion->fecha_fin))
                    || in_array($fecha->toDateString(), $fechasExistentes, true)) {
                    continue;
                }

                $cita = new Cita([
                    'horario_id' => $horario->id,
                    'alumno_id' => $alumno->id,
                    'sucursal_id' => $horario->sucursal_id,
                    'fecha' => $fecha->toDateString(),
                    'hora_inicio' => $horario->hora_inicio,
                    'hora_fin' => $horario->hora_fin,
                    'estado' => EstadoCita::Programada,
                ]);
                $cita->setRelation('horario', $horario);
                $citas->push($cita);
                $fechasExistentes[] = $fecha->toDateString();
            }
        }

        return $citas->sortBy(fn (Cita $cita) => $cita->fecha->format('Y-m-d').' '.$cita->hora_inicio)
            ->take($limite)
            ->values();
    }
}
