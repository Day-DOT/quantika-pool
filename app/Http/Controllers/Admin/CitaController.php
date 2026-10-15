<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EstadoCita;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AvisarFaltaRequest;
use App\Http\Requests\Admin\ReagendarCitaRequest;
use App\Http\Requests\Admin\ReagendarClaseRequest;
use App\Http\Requests\Admin\RegistrarAsistenciaRequest;
use App\Models\Alumno;
use App\Models\Cita;
use App\Models\Horario;
use App\Support\ClasesDelAlumno;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CitaController extends Controller
{
    use AuthorizesRequests;

    /**
     * Reagenda una clase ya guardada a otro horario/fecha. La administración
     * puede hacerlo las veces que haga falta: mover una clase no le suma
     * clases al alumno, así que no depende del límite semanal de su plan.
     */
    public function reagendar(ReagendarCitaRequest $request, Cita $cita): RedirectResponse
    {
        $datos = $request->validated();

        return $this->moverClase($request, $cita, Horario::findOrFail($datos['horario_id']), Carbon::parse($datos['fecha']));
    }

    /**
     * Reagenda una clase del alumno indicando cuál es (grupo y fecha). Sirve
     * también para las clases de su plan que todavía no tienen una Cita
     * guardada, que son la mayoría.
     */
    public function reagendarClase(ReagendarClaseRequest $request, Alumno $alumno): RedirectResponse
    {
        $datos = $request->validated();

        $clase = ClasesDelAlumno::localizar(
            $alumno,
            Horario::findOrFail($datos['origen_horario_id']),
            Carbon::parse($datos['origen_fecha'])->startOfDay(),
        );

        if (! $clase) {
            return back()->withErrors(['fecha' => 'El alumno no tiene clase en ese grupo para la fecha indicada.']);
        }

        return $this->moverClase($request, $clase, Horario::findOrFail($datos['horario_id']), Carbon::parse($datos['fecha']));
    }

    /**
     * Registra desde antes que el alumno no asistirá a una clase (la familia
     * avisó). El instructor lo ve en su lista de ese día y la falta queda
     * disponible para reponerse.
     */
    public function avisarFalta(AvisarFaltaRequest $request, Alumno $alumno): RedirectResponse
    {
        $datos = $request->validated();
        $fecha = Carbon::parse($datos['fecha'])->startOfDay();

        $clase = ClasesDelAlumno::localizar($alumno, Horario::findOrFail($datos['horario_id']), $fecha);

        if (! $clase) {
            return back()->withErrors(['falta' => 'El alumno no tiene clase en ese grupo para la fecha indicada.']);
        }

        if ($clase->asistio !== null) {
            return back()->withErrors(['falta' => 'Esa clase ya tiene asistencia o falta registrada.']);
        }

        $clase->fill([
            'asistio' => false,
            'falta_avisada' => true,
            'estado' => EstadoCita::Completada->value,
            'notas' => $datos['notas'] ?? 'Avisó con anticipación que no asistiría.',
            'registrado_por' => $request->user()->id,
        ])->save();

        return back()->with(
            'status',
            "Falta avisada de {$alumno->nombreCompleto()} registrada para el {$fecha->format('d/m/Y')}. Su instructor la verá en la lista de ese día."
        );
    }

    /**
     * Registra a mano la asistencia o la falta de un alumno en una clase de
     * hoy o de días anteriores (también sirve para corregir una falta
     * automática si el alumno sí fue pero no escaneó su QR).
     */
    public function registrarAsistencia(RegistrarAsistenciaRequest $request, Alumno $alumno): RedirectResponse
    {
        $datos = $request->validated();
        $asistio = $request->boolean('asistio');
        $fecha = Carbon::parse($datos['fecha']);

        $motivo = ClasesDelAlumno::registrarAsistencia($alumno, Horario::findOrFail($datos['horario_id']), $fecha, $asistio, $request->user());

        if ($motivo) {
            return back()->withErrors([$motivo[0] => $motivo[1]]);
        }

        return back()->with(
            'status',
            ($asistio ? 'Asistencia' : 'Falta')." de {$alumno->nombreCompleto()} registrada para el {$fecha->format('d/m/Y')}."
        );
    }

    /**
     * Deshace un aviso de falta registrado por error.
     */
    public function quitarAvisoDeFalta(Cita $cita): RedirectResponse
    {
        $this->authorize('update', $cita);

        abort_unless($cita->falta_avisada, 404);

        if ($cita->reposicion()->exists()) {
            return back()->withErrors(['falta' => 'Esa falta ya tiene una reposición programada. Cancela primero la reposición.']);
        }

        if ($cita->solicitudesPendientes()->exists()) {
            return back()->withErrors(['falta' => 'El tutor ya pidió reponer esa falta. Rechaza primero su solicitud en "Reservas pendientes".']);
        }

        $esDeSuPlan = ClasesDelAlumno::estaInscrito($cita->alumno, $cita->horario_id)
            && ! $cita->es_extra
            && ! $cita->reposicion_de_id;

        if ($esDeSuPlan) {
            // Vuelve a ser una clase normal de su grupo: no necesita registro.
            $cita->delete();
        } else {
            $cita->update([
                'asistio' => null,
                'falta_avisada' => false,
                'estado' => EstadoCita::Programada->value,
                'notas' => null,
            ]);
        }

        return back()->with('status', 'Aviso de falta retirado. La clase vuelve a estar programada.');
    }

    private function moverClase(ReagendarCitaRequest|ReagendarClaseRequest $request, Cita $clase, Horario $nuevoHorario, Carbon $nuevaFecha): RedirectResponse
    {
        $motivo = DB::transaction(
            fn () => ClasesDelAlumno::reagendar($clase, $nuevoHorario, $nuevaFecha, $request->user())
        );

        if ($motivo) {
            return back()->withErrors([$motivo[0] => $motivo[1]]);
        }

        return back()->with('status', "Clase de {$clase->alumno->nombreCompleto()} reagendada correctamente.");
    }
}
