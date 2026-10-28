<?php

namespace App\Http\Controllers\Instructor;

use App\Enums\EstadoCita;
use App\Enums\EstadoClaseMuestra;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Instructor\Concerns\ResuelveInstructor;
use App\Http\Requests\Instructor\MarcarAsistenciaRequest;
use App\Models\Alumno;
use App\Models\Cita;
use App\Models\ClaseMuestra;
use App\Models\Horario;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GrupoController extends Controller
{
    use AuthorizesRequests;
    use ResuelveInstructor;

    /**
     * Lista de asistencia de un grupo: alumnos con inscripción vigente y,
     * si ya existe, el estado de su cita de hoy (asistió / no asistió).
     */
    public function show(Request $request, Horario $horario): View
    {
        $this->instructorActivo($request);
        $this->authorize('view', $horario);

        $hoy = today();

        $citasHoy = Cita::where('horario_id', $horario->id)
            ->whereDate('fecha', $hoy)
            ->get()
            ->keyBy('alumno_id');

        // Además de los inscritos, hoy toman la clase quienes tienen una
        // clase de una sola fecha en este grupo: extra, reposición o
        // reagendada desde otro grupo.
        $visitantesHoy = Cita::visitantesDelDia($horario->id, $hoy)->pluck('alumno_id');
        $inscritosIds = $horario->inscripciones()->activas()->pluck('alumno_id');

        $alumnos = Alumno::query()
            ->whereIn('id', $inscritosIds->merge($visitantesHoy))
            ->with('nivel')
            ->orderBy('nombre')
            ->orderBy('apellidos')
            ->get();

        $horario->load(['nivel', 'carril', 'sucursal']);

        return view('quantika.instructor.grupos.show', [
            'horario' => $horario,
            'alumnos' => $alumnos,
            'clasesMuestra' => ClaseMuestra::delDia($horario->id, $hoy)->orderBy('nombre_alumno')->get(),
            'citasHoy' => $citasHoy,
            'inscritosIds' => $inscritosIds,
            'hoy' => $hoy,
        ]);
    }

    /**
     * Marca la asistencia de un alumno para la cita de hoy en este grupo.
     * Si la cita de hoy todavía no existe (no fue generada de antemano),
     * se crea en este momento con estado "Completada".
     */
    public function marcarAsistencia(MarcarAsistenciaRequest $request, Horario $horario, Alumno $alumno): RedirectResponse
    {
        $this->instructorActivo($request);
        $this->authorize('view', $horario);

        $hoy = today();

        $inscrito = $horario->inscripciones()->activas()->where('alumno_id', $alumno->id)->exists()
            || Cita::visitantesDelDia($horario->id, $hoy)->where('alumno_id', $alumno->id)->exists();

        abort_unless($inscrito, 404, 'El alumno no está inscrito en este grupo.');

        // Buscamos por whereDate en lugar de una igualdad exacta de cadena:
        // el cast a "date" de Eloquent puede persistir la fecha con hora
        // (p. ej. "2026-08-20 00:00:00"), así que comparar el valor crudo
        // con toDateString() no siempre encuentra la fila ya existente.
        $cita = Cita::where('horario_id', $horario->id)
            ->where('alumno_id', $alumno->id)
            ->whereDate('fecha', $hoy)
            ->first();

        if (! $cita) {
            $cita = new Cita([
                'horario_id' => $horario->id,
                'alumno_id' => $alumno->id,
                'fecha' => $hoy,
            ]);
        }

        // El modelo ya tiene horario_id asignado (exista o no todavía en
        // base de datos), así que la Policy puede resolver la propiedad
        // del grupo tanto si la cita es nueva como si ya existía.
        $this->authorize('update', $cita);

        if (! $cita->exists) {
            $cita->sucursal_id = $horario->sucursal_id;
            $cita->hora_inicio = $horario->hora_inicio;
            $cita->hora_fin = $horario->hora_fin;
        }

        $cita->asistio = $request->boolean('asistio');
        $cita->falta_avisada = false;
        $cita->estado = EstadoCita::Completada;
        $cita->notas = $request->filled('notas') ? $request->string('notas')->toString() : $cita->notas;
        $cita->registrado_por = $request->user()->id;
        $cita->save();

        return back()->with('status', 'Asistencia registrada para '.$alumno->nombreCompleto().'.');
    }

    /**
     * Marca si quien venía a su clase muestra de hoy asistió o no.
     */
    public function marcarAsistenciaMuestra(Request $request, Horario $horario, ClaseMuestra $claseMuestra): RedirectResponse
    {
        $this->instructorActivo($request);
        $this->authorize('view', $horario);

        abort_unless(
            $claseMuestra->horario_id === $horario->id
                && $claseMuestra->fecha->isToday()
                && $claseMuestra->estado !== EstadoClaseMuestra::Cancelada,
            404
        );

        $estado = $request->boolean('asistio') ? EstadoClaseMuestra::Asistio : EstadoClaseMuestra::NoAsistio;
        $claseMuestra->update(['estado' => $estado->value]);

        return back()->with('status', "Clase muestra de {$claseMuestra->nombre_alumno}: {$estado->label()}.");
    }
}
