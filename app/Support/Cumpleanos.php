<?php

namespace App\Support;

use App\Enums\EstadoAlumno;
use App\Models\Alumno;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Cumpleaños de los alumnos activos, a partir de su fecha de nacimiento.
 * Quien nació un 29 de febrero lo celebra el 28 en los años no bisiestos.
 */
class Cumpleanos
{
    /** Días hacia adelante (contando hoy) que se avisan en el dashboard. */
    public const DIAS_DE_AVISO = 7;

    /**
     * Cumpleaños de hoy y de los próximos días, ordenados por fecha.
     *
     * @return Collection<int, array{alumno: Alumno, fecha: Carbon, cumple: int}>
     */
    public static function proximos(?int $sucursalId, int $dias = self::DIAS_DE_AVISO): Collection
    {
        $hoy = today();
        $hasta = $hoy->copy()->addDays($dias - 1);

        return self::alumnos($sucursalId)
            ->map(function (Alumno $alumno) use ($hoy) {
                $fecha = self::fechaEnAnio($alumno->fecha_nacimiento, $hoy->year);

                if ($fecha->lt($hoy)) {
                    $fecha = self::fechaEnAnio($alumno->fecha_nacimiento, $hoy->year + 1);
                }

                return self::fila($alumno, $fecha);
            })
            ->filter(fn (array $fila) => $fila['fecha']->lte($hasta))
            ->sortBy(fn (array $fila) => $fila['fecha']->format('Y-m-d').' '.$fila['alumno']->nombreCompleto())
            ->values();
    }

    /**
     * Cumpleaños de un mes, agrupados por día del mes.
     *
     * @return Collection<int, Collection<int, array{alumno: Alumno, fecha: Carbon, cumple: int}>>
     */
    public static function delMes(?int $sucursalId, Carbon $mes): Collection
    {
        return self::alumnos($sucursalId)
            ->map(fn (Alumno $alumno) => self::fila($alumno, self::fechaEnAnio($alumno->fecha_nacimiento, $mes->year)))
            ->filter(fn (array $fila) => $fila['fecha']->month === $mes->month)
            ->sortBy(fn (array $fila) => $fila['alumno']->nombreCompleto())
            ->groupBy(fn (array $fila) => $fila['fecha']->day);
    }

    /**
     * @return Collection<int, Alumno>
     */
    private static function alumnos(?int $sucursalId): Collection
    {
        return Alumno::query()
            ->where('estado', EstadoAlumno::Activo->value)
            ->whereNotNull('fecha_nacimiento')
            ->when($sucursalId, fn ($query) => $query->where('sucursal_id', $sucursalId))
            ->with('nivel')
            ->get();
    }

    /**
     * @return array{alumno: Alumno, fecha: Carbon, cumple: int}
     */
    private static function fila(Alumno $alumno, Carbon $fecha): array
    {
        return [
            'alumno' => $alumno,
            'fecha' => $fecha,
            'cumple' => $fecha->year - $alumno->fecha_nacimiento->year,
        ];
    }

    private static function fechaEnAnio(Carbon $nacimiento, int $anio): Carbon
    {
        $dia = $nacimiento->month === 2 && $nacimiento->day === 29 && ! Carbon::create($anio)->isLeapYear()
            ? 28
            : $nacimiento->day;

        return Carbon::create($anio, $nacimiento->month, $dia)->startOfDay();
    }
}
