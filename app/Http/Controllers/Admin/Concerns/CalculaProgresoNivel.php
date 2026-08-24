<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\Alumno;
use App\Models\Evaluacion;
use App\Models\Nivel;

trait CalculaProgresoNivel
{
    /**
     * Progreso agregado real de un nivel: cuántos alumnos están cursándolo
     * actualmente y el porcentaje promedio de criterios logrados en su
     * evaluación más reciente para ese nivel.
     *
     * @return array{alumnos: int, progreso: float}
     */
    protected function progresoDeNivel(Nivel $nivel, ?int $sucursalId): array
    {
        $alumnosQuery = Alumno::query()->where('nivel_id', $nivel->id);

        if ($sucursalId) {
            $alumnosQuery->where('sucursal_id', $sucursalId);
        }

        $alumnoIds = $alumnosQuery->pluck('id');

        if ($alumnoIds->isEmpty()) {
            return ['alumnos' => 0, 'progreso' => 0.0];
        }

        $porcentajes = Evaluacion::query()
            ->whereIn('alumno_id', $alumnoIds)
            ->where('nivel_id', $nivel->id)
            ->get()
            ->groupBy('alumno_id')
            ->map(fn ($evaluaciones) => $evaluaciones->sortByDesc('fecha')->first()->porcentajeAvance());

        return [
            'alumnos' => $alumnoIds->count(),
            'progreso' => $porcentajes->isEmpty() ? 0.0 : round((float) $porcentajes->avg(), 1),
        ];
    }
}
