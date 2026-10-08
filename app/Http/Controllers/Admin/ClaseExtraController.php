<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ConceptoPago;
use App\Enums\EstadoAlumno;
use App\Enums\EstadoCita;
use App\Enums\EstadoPago;
use App\Enums\MetodoPago;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreClaseExtraRequest;
use App\Models\Alumno;
use App\Models\Cita;
use App\Models\Horario;
use App\Models\Inscripcion;
use App\Models\Pago;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ClaseExtraController extends Controller
{
    use AuthorizesRequests;

    /**
     * Agenda una clase extra: una sola clase, en una fecha concreta, fuera
     * del plan del alumno. No crea inscripción, así que el alumno aparece
     * en el grupo únicamente ese día y la semana siguiente ya no figura.
     * Si se captura el monto, el cobro queda registrado como pagado.
     */
    public function store(StoreClaseExtraRequest $request, Alumno $alumno): RedirectResponse
    {
        $datos = $request->validated();

        if ($alumno->estado !== EstadoAlumno::Activo) {
            return back()->withErrors(['clase_extra' => 'Solo se pueden agendar clases extra a alumnos activos.']);
        }

        $horario = Horario::findOrFail($datos['horario_id']);
        $fecha = Carbon::parse($datos['fecha'])->startOfDay();

        if ($horario->sucursal_id !== $alumno->sucursal_id || ! $horario->activo) {
            return back()->withErrors(['clase_extra' => 'El grupo debe estar activo y pertenecer a la sucursal del alumno.']);
        }

        if ($fecha->isoWeekday() !== $horario->dia_semana->value) {
            return back()->withErrors([
                'clase_extra' => "La fecha elegida no coincide con el día ({$horario->dia_semana->label()}) del grupo seleccionado.",
            ]);
        }

        if (Inscripcion::where('horario_id', $horario->id)->where('alumno_id', $alumno->id)->where('activa', true)->exists()) {
            return back()->withErrors(['clase_extra' => 'El alumno ya está inscrito en ese grupo: esa clase ya forma parte de su plan.']);
        }

        if (Cita::where('horario_id', $horario->id)->where('alumno_id', $alumno->id)->whereDate('fecha', $fecha)->exists()) {
            return back()->withErrors(['clase_extra' => 'El alumno ya tiene una clase registrada en ese grupo para esa fecha.']);
        }

        $inscritos = Inscripcion::where('horario_id', $horario->id)->where('activa', true)->count();

        if (($inscritos + Cita::visitantesDelDia($horario->id, $fecha)->count()) >= $horario->capacidad_maxima) {
            return back()->withErrors(['clase_extra' => 'Ese grupo ya no tiene cupo disponible para esa fecha.']);
        }

        DB::transaction(function () use ($request, $alumno, $horario, $fecha, $datos) {
            Cita::create([
                'horario_id' => $horario->id,
                'alumno_id' => $alumno->id,
                'sucursal_id' => $horario->sucursal_id,
                'fecha' => $fecha->toDateString(),
                'hora_inicio' => $horario->hora_inicio,
                'hora_fin' => $horario->hora_fin,
                'estado' => EstadoCita::Programada->value,
                'es_extra' => true,
                'registrado_por' => $request->user()->id,
            ]);

            if ((float) ($datos['monto'] ?? 0) > 0) {
                Pago::create([
                    'alumno_id' => $alumno->id,
                    'sucursal_id' => $alumno->sucursal_id,
                    'concepto' => ConceptoPago::Otro->value,
                    'periodo' => $fecha->format('Y-m'),
                    'monto' => $datos['monto'],
                    'fecha_vencimiento' => today()->toDateString(),
                    'fecha_pago' => today()->toDateString(),
                    'metodo_pago' => $datos['metodo_pago'] ?? MetodoPago::Efectivo->value,
                    'estado' => EstadoPago::Pagado->value,
                    'observaciones' => "Clase extra del {$fecha->format('d/m/Y')} en {$horario->nombre_grupo}.",
                    'registrado_por' => $request->user()->id,
                ]);
            }
        });

        return back()->with(
            'status',
            "Clase extra agendada para {$alumno->nombreCompleto()} el {$fecha->format('d/m/Y')} en {$horario->nombre_grupo}."
        );
    }

    /**
     * Cancela una clase extra que todavía no se ha tomado. El pago, si se
     * registró, se conserva para que la administración decida qué hacer.
     */
    public function destroy(Cita $cita): RedirectResponse
    {
        $this->authorize('delete', $cita);

        abort_unless($cita->es_extra, 404);

        if ($cita->asistio !== null) {
            return back()->withErrors(['clase_extra' => 'Esa clase extra ya tiene asistencia registrada y no se puede cancelar.']);
        }

        $cita->delete();

        return back()->with('status', 'Clase extra cancelada.');
    }
}
