<?php

namespace App\Http\Controllers\Alumno\Concerns;

use App\Models\Alumno;
use App\Support\ClasesDelAlumno;
use Illuminate\Support\Collection;

trait CargaProximasClases
{
    /**
     * Próximas clases del alumno para el portal. Usa el mismo cálculo que
     * la administración (grupos en los que está inscrito más sus clases de
     * una sola fecha), así ambos ven siempre lo mismo. Las clases a las
     * que ya avisó que no asistirá no se listan.
     *
     * @return Collection<int, \App\Models\Cita>
     */
    private function proximasClases(Alumno $alumno, int $limite = 3): Collection
    {
        return ClasesDelAlumno::proximas($alumno, dias: 35)
            ->reject(fn (array $clase) => $clase['cita']->falta_avisada)
            ->pluck('cita')
            ->take($limite)
            ->values();
    }
}
