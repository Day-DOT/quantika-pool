<?php

namespace App\Http\Controllers\Instructor;

use App\Enums\EstadoEvaluacionDetalle;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Instructor\Concerns\ResuelveInstructor;
use App\Http\Requests\Instructor\ActualizarEvaluacionRequest;
use App\Models\Alumno;
use App\Models\CriterioEvaluacion;
use App\Models\Evaluacion;
use App\Models\EvaluacionDetalle;
use App\Models\Instructor;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EvaluacionController extends Controller
{
    use AuthorizesRequests;
    use ResuelveInstructor;

    /**
     * Historial de evaluaciones realizadas por este instructor, más
     * recientes primero.
     */
    public function index(Request $request): View
    {
        $instructor = $this->instructorActivo($request);

        $evaluaciones = Evaluacion::where('instructor_id', $instructor->id)
            ->with(['alumno', 'nivel', 'detalles'])
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->get();

        return view('quantika.instructor.evaluaciones.index', [
            'evaluaciones' => $evaluaciones,
        ]);
    }

    /**
     * Punto de entrada para evaluar a un alumno. Si ya existe una
     * evaluación de este instructor para el nivel actual del alumno, se
     * continúa esa (se redirige a editarla) en vez de crear una duplicada.
     */
    public function create(Request $request, Alumno $alumno): View|RedirectResponse
    {
        $instructor = $this->instructorActivo($request);
        $this->authorize('view', $alumno);

        if ($alumno->nivel_id === null) {
            return view('quantika.instructor.evaluaciones.create', [
                'alumno' => $alumno,
                'criterios' => collect(),
                'estados' => EstadoEvaluacionDetalle::cases(),
                'sinNivel' => true,
            ]);
        }

        $existente = $this->evaluacionEnCurso($alumno, $instructor);

        if ($existente) {
            return redirect()->route('instructor.evaluaciones.edit', $existente);
        }

        $criterios = CriterioEvaluacion::where('nivel_id', $alumno->nivel_id)
            ->where('activo', true)
            ->orderBy('orden')
            ->get();

        return view('quantika.instructor.evaluaciones.create', [
            'alumno' => $alumno->load('nivel'),
            'criterios' => $criterios,
            'estados' => EstadoEvaluacionDetalle::cases(),
            'sinNivel' => false,
        ]);
    }

    /**
     * Crea la evaluación del alumno en su nivel actual y guarda el estado
     * inicial capturado para cada criterio.
     */
    public function store(ActualizarEvaluacionRequest $request, Alumno $alumno): RedirectResponse
    {
        $instructor = $this->instructorActivo($request);
        $this->authorize('view', $alumno);
        $this->authorize('create', Evaluacion::class);

        abort_if($alumno->nivel_id === null, 422, 'Este alumno no tiene un nivel asignado todavía.');

        // Por si el instructor abrió el formulario dos veces: evitamos
        // duplicar evaluaciones para el mismo alumno/nivel.
        $evaluacion = $this->evaluacionEnCurso($alumno, $instructor);

        $datos = $request->validated();

        if (! $evaluacion) {
            $evaluacion = Evaluacion::create([
                'alumno_id' => $alumno->id,
                'instructor_id' => $instructor->id,
                'nivel_id' => $alumno->nivel_id,
                'fecha' => today(),
                'observaciones' => $datos['observaciones'] ?? null,
            ]);
        } else {
            $evaluacion->update(['observaciones' => $datos['observaciones'] ?? null]);
        }

        $this->guardarDetalles($evaluacion, $datos['detalles']);

        return redirect()
            ->route('instructor.evaluaciones.edit', $evaluacion)
            ->with('status', 'Evaluación creada correctamente.');
    }

    /**
     * Formulario para continuar/editar una evaluación existente, con el
     * % de avance calculado sobre los criterios ya capturados.
     */
    public function edit(Request $request, Evaluacion $evaluacion): View
    {
        $this->instructorActivo($request);
        $this->authorize('update', $evaluacion);

        $evaluacion->load(['alumno', 'nivel', 'detalles']);

        $criterios = CriterioEvaluacion::where('nivel_id', $evaluacion->nivel_id)
            ->where('activo', true)
            ->orderBy('orden')
            ->get();

        $detallesPorCriterio = $evaluacion->detalles->keyBy('criterio_evaluacion_id');

        return view('quantika.instructor.evaluaciones.edit', [
            'evaluacion' => $evaluacion,
            'criterios' => $criterios,
            'detallesPorCriterio' => $detallesPorCriterio,
            'estados' => EstadoEvaluacionDetalle::cases(),
            'porcentaje' => $evaluacion->porcentajeAvance(),
        ]);
    }

    /**
     * Guarda el estado (No iniciado / En proceso / Logrado) y las
     * observaciones de cada criterio, junto con la observación general.
     */
    public function update(ActualizarEvaluacionRequest $request, Evaluacion $evaluacion): RedirectResponse
    {
        $this->instructorActivo($request);
        $this->authorize('update', $evaluacion);

        $datos = $request->validated();

        $evaluacion->update(['observaciones' => $datos['observaciones'] ?? null]);

        $this->guardarDetalles($evaluacion, $datos['detalles']);

        return redirect()
            ->route('instructor.evaluaciones.edit', $evaluacion)
            ->with('status', 'Evaluación actualizada. Avance: '.$evaluacion->porcentajeAvance().'%.');
    }

    private function evaluacionEnCurso(Alumno $alumno, Instructor $instructor): ?Evaluacion
    {
        return Evaluacion::where('alumno_id', $alumno->id)
            ->where('instructor_id', $instructor->id)
            ->where('nivel_id', $alumno->nivel_id)
            ->latest('fecha')
            ->latest('id')
            ->first();
    }

    /**
     * @param  array<int, array{criterio_evaluacion_id: int, estado: string, observaciones?: string|null}>  $detalles
     */
    private function guardarDetalles(Evaluacion $evaluacion, array $detalles): void
    {
        foreach ($detalles as $detalle) {
            EvaluacionDetalle::updateOrCreate(
                [
                    'evaluacion_id' => $evaluacion->id,
                    'criterio_evaluacion_id' => $detalle['criterio_evaluacion_id'],
                ],
                [
                    'estado' => $detalle['estado'],
                    'observaciones' => $detalle['observaciones'] ?? null,
                ]
            );
        }
    }
}
