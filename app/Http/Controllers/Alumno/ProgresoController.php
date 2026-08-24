<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Alumno\Concerns\ResuelveAlumnoActivo;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ProgresoController extends Controller
{
    use AuthorizesRequests;
    use ResuelveAlumnoActivo;

    /**
     * "Boleta digital" del alumno: % global de dominio del nivel actual
     * y el desglose de la última evaluación por cada criterio, con las
     * observaciones que dejó el instructor.
     */
    public function index(Request $request): View
    {
        $alumnos = $this->alumnosDelTutor($request);
        $alumno = $this->alumnoActivo($request, $alumnos);

        if (! $alumno) {
            return view('quantika.portal.progreso', [
                'alumnos' => $alumnos,
                'alumno' => null,
                'nivelActual' => null,
                'ultimaEvaluacion' => null,
                'criterios' => collect(),
                'detallesPorCriterio' => collect(),
                'porcentaje' => 0.0,
                'historial' => collect(),
            ]);
        }

        $nivelActual = $alumno->nivel;

        $ultimaEvaluacion = $alumno->evaluaciones()
            ->with(['detalles.criterio', 'instructor.user'])
            ->when($nivelActual, fn ($q) => $q->where('nivel_id', $nivelActual->id))
            ->latest('fecha')
            ->first();

        $criterios = $nivelActual
            ? $nivelActual->criterios()->where('activo', true)->orderBy('orden')->get()
            : collect();

        /** @var Collection<int, \App\Models\EvaluacionDetalle> $detallesPorCriterio */
        $detallesPorCriterio = $ultimaEvaluacion
            ? $ultimaEvaluacion->detalles->keyBy('criterio_evaluacion_id')
            : collect();

        $historial = $alumno->evaluaciones()
            ->with('nivel')
            ->orderByDesc('fecha')
            ->limit(6)
            ->get();

        return view('quantika.portal.progreso', [
            'alumnos' => $alumnos,
            'alumno' => $alumno,
            'nivelActual' => $nivelActual,
            'ultimaEvaluacion' => $ultimaEvaluacion,
            'criterios' => $criterios,
            'detallesPorCriterio' => $detallesPorCriterio,
            'porcentaje' => $ultimaEvaluacion?->porcentajeAvance() ?? 0.0,
            'historial' => $historial,
        ]);
    }
}
