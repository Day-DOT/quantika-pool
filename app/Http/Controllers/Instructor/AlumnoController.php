<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Instructor\Concerns\ResuelveInstructor;
use App\Models\Alumno;
use App\Models\AlumnoNivelHistorial;
use App\Models\Inscripcion;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AlumnoController extends Controller
{
    use AuthorizesRequests;
    use ResuelveInstructor;

    /**
     * Todos los alumnos con inscripción vigente en algún grupo de este
     * instructor (nunca de otro instructor).
     */
    public function index(Request $request): View
    {
        $instructor = $this->instructorActivo($request);

        $horarioIds = $instructor->horarios()->pluck('id');

        $inscripciones = Inscripcion::activas()
            ->whereIn('horario_id', $horarioIds)
            ->with('horario')
            ->get()
            ->groupBy('alumno_id');

        $alumnos = Alumno::whereIn('id', $inscripciones->keys())
            ->with('nivel')
            ->orderBy('nombre')
            ->orderBy('apellidos')
            ->get()
            ->each(function (Alumno $alumno) use ($inscripciones) {
                $alumno->gruposNombres = $inscripciones->get($alumno->id, collect())
                    ->pluck('horario.nombre_grupo')
                    ->filter()
                    ->unique()
                    ->implode(', ');
            });

        return view('quantika.instructor.alumnos.index', [
            'alumnos' => $alumnos,
        ]);
    }

    /**
     * Detalle de un alumno: nivel actual, criterios de evaluación de ese
     * nivel, % de avance y su historial de evaluaciones con este
     * instructor.
     */
    public function show(Request $request, Alumno $alumno): View
    {
        $this->instructorActivo($request);
        $this->authorize('view', $alumno);

        $alumno->load('nivel');

        $criterios = $alumno->nivel
            ? $alumno->nivel->criterios()->where('activo', true)->orderBy('orden')->get()
            : collect();

        $evaluaciones = $alumno->evaluaciones()
            ->with(['nivel', 'detalles'])
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->get();

        $evaluacionNivelActual = $alumno->nivel_id
            ? $evaluaciones->firstWhere('nivel_id', $alumno->nivel_id)
            : null;

        $siguienteNivel = $alumno->nivel?->siguiente();

        $puedePromover = $alumno->nivel_id
            && $siguienteNivel
            && $evaluacionNivelActual
            && $evaluacionNivelActual->porcentajeAvance() >= 100.0;

        return view('quantika.instructor.alumnos.show', [
            'alumno' => $alumno,
            'criterios' => $criterios,
            'evaluaciones' => $evaluaciones,
            'evaluacionNivelActual' => $evaluacionNivelActual,
            'siguienteNivel' => $siguienteNivel,
            'puedePromover' => $puedePromover,
        ]);
    }

    /**
     * Promueve al alumno de su nivel actual al siguiente, cerrando el
     * registro vigente de su historial de niveles y abriendo uno nuevo.
     * Solo se permite si ya tiene una evaluación del nivel actual con el
     * 100% de los criterios logrados.
     */
    public function promover(Request $request, Alumno $alumno): RedirectResponse
    {
        $this->instructorActivo($request);
        $this->authorize('view', $alumno);

        $nivelActual = $alumno->nivel;

        if (! $nivelActual) {
            return back()->withErrors(['nivel' => 'Este alumno no tiene un nivel asignado.']);
        }

        $siguienteNivel = $nivelActual->siguiente();

        if (! $siguienteNivel) {
            return back()->withErrors(['nivel' => 'Este alumno ya está en el nivel máximo.']);
        }

        $evaluacionNivelActual = $alumno->evaluaciones()
            ->where('nivel_id', $nivelActual->id)
            ->with('detalles')
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->first();

        if (! $evaluacionNivelActual || $evaluacionNivelActual->porcentajeAvance() < 100.0) {
            return back()->withErrors([
                'nivel' => 'Debes completar la evaluación del nivel actual (todos los criterios logrados) antes de promoverlo.',
            ]);
        }

        DB::transaction(function () use ($alumno, $siguienteNivel, $nivelActual, $request) {
            AlumnoNivelHistorial::where('alumno_id', $alumno->id)
                ->whereNull('fecha_fin')
                ->update(['fecha_fin' => now()->toDateString()]);

            AlumnoNivelHistorial::create([
                'alumno_id' => $alumno->id,
                'nivel_id' => $siguienteNivel->id,
                'fecha_inicio' => now()->toDateString(),
                'fecha_fin' => null,
                'promovido_por' => $request->user()->id,
                'observaciones' => "Promovido desde {$nivelActual->nombre} tras completar la evaluación.",
            ]);

            $alumno->update(['nivel_id' => $siguienteNivel->id]);
        });

        return redirect()
            ->route('instructor.alumnos.show', $alumno)
            ->with('status', "{$alumno->nombreCompleto()} fue promovido a {$siguienteNivel->nombre}.");
    }
}
