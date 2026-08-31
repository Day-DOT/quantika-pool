<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReagendarCitaRequest;
use App\Models\Cita;
use App\Models\Horario;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;

class CitaController extends Controller
{
    use AuthorizesRequests;

    /**
     * Reagenda la clase individual de un alumno a otro horario/fecha.
     * Solo se permite si el alumno todavía tiene clases disponibles esta
     * semana según su plan (validación exigida antes de aplicar el cambio).
     */
    public function reagendar(ReagendarCitaRequest $request, Cita $cita): RedirectResponse
    {
        $datos = $request->validated();

        $alumno = $cita->alumno;

        if (! $alumno->tieneClasesDisponiblesEstaSemana()) {
            return back()->withErrors([
                'fecha' => "{$alumno->nombreCompleto()} ya alcanzó el límite de clases de esta semana según su plan. No se puede reagendar.",
            ]);
        }

        $nuevoHorario = Horario::findOrFail($datos['horario_id']);

        if ($nuevoHorario->sucursal_id !== $cita->sucursal_id) {
            return back()->withErrors([
                'horario_id' => 'El nuevo horario debe pertenecer a la misma sucursal que la clase.',
            ]);
        }

        $nuevaFecha = Carbon::parse($datos['fecha']);

        if ($nuevaFecha->isoWeekday() !== $nuevoHorario->dia_semana->value) {
            return back()->withErrors([
                'fecha' => "La fecha elegida no coincide con el día ({$nuevoHorario->dia_semana->label()}) del horario seleccionado.",
            ]);
        }

        $cita->update([
            'horario_id' => $nuevoHorario->id,
            'fecha' => $nuevaFecha->toDateString(),
            'hora_inicio' => $nuevoHorario->hora_inicio,
            'hora_fin' => $nuevoHorario->hora_fin,
        ]);

        return back()->with('status', "Clase de {$alumno->nombreCompleto()} reagendada correctamente.");
    }
}
