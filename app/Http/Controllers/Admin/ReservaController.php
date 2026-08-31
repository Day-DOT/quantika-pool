<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EstadoInscripcion;
use App\Http\Controllers\Admin\Concerns\ScopesSucursal;
use App\Http\Controllers\Concerns\AgendaCitasIniciales;
use App\Http\Controllers\Controller;
use App\Models\Horario;
use App\Models\Inscripcion;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ReservaController extends Controller
{
    use AgendaCitasIniciales;
    use AuthorizesRequests;
    use ScopesSucursal;

    /**
     * Reservas hechas por alumnos/tutores que están pendientes de
     * aprobación por un Admin.
     */
    public function index(): View
    {
        $this->authorize('viewAny', Horario::class);

        $sucursalId = $this->sucursalId();

        $reservas = Inscripcion::pendientes()
            ->with(['alumno', 'horario.nivel', 'horario.instructor.user', 'horario.carril'])
            ->when($sucursalId, fn ($q) => $q->whereHas('horario', fn ($h) => $h->where('sucursal_id', $sucursalId)))
            ->orderBy('created_at')
            ->get();

        return view('quantika.reservas.index', [
            'reservas' => $reservas,
        ]);
    }

    /**
     * Aprueba una reserva pendiente: reconfirma el cupo del horario, marca
     * la Inscripcion como activa/aprobada y agenda sus primeras Citas.
     */
    public function aprobar(Request $request, Inscripcion $inscripcion): RedirectResponse
    {
        $this->authorize('update', $inscripcion->horario);

        if ($inscripcion->estado !== EstadoInscripcion::Pendiente) {
            return back()->withErrors(['inscripcion' => 'Esta reserva ya fue procesada.']);
        }

        try {
            DB::transaction(function () use ($inscripcion, $request) {
                $horario = Horario::where('id', $inscripcion->horario_id)->lockForUpdate()->first();

                $inscritos = Inscripcion::where('horario_id', $horario->id)
                    ->activas()
                    ->lockForUpdate()
                    ->count();

                if ($inscritos >= $horario->capacidad_maxima) {
                    throw ValidationException::withMessages([
                        'inscripcion' => 'Ya no hay cupo disponible en este grupo. No se puede aprobar la reserva.',
                    ]);
                }

                $inscripcion->update([
                    'activa' => true,
                    'estado' => EstadoInscripcion::Aprobada->value,
                    'aprobado_por' => $request->user()->id,
                    'aprobado_en' => now(),
                ]);

                $this->agendarPrimerasCitas($horario, $inscripcion->alumno, $request->user()->id);
            });
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return redirect()
            ->route('reservas.index')
            ->with('status', "Reserva de {$inscripcion->alumno->nombreCompleto()} aprobada correctamente.");
    }

    /**
     * Rechaza una reserva pendiente sin ocupar cupo ni agendar Citas.
     */
    public function rechazar(Request $request, Inscripcion $inscripcion): RedirectResponse
    {
        $this->authorize('update', $inscripcion->horario);

        if ($inscripcion->estado !== EstadoInscripcion::Pendiente) {
            return back()->withErrors(['inscripcion' => 'Esta reserva ya fue procesada.']);
        }

        $inscripcion->update([
            'activa' => false,
            'estado' => EstadoInscripcion::Rechazada->value,
            'aprobado_por' => $request->user()->id,
            'aprobado_en' => now(),
        ]);

        return redirect()
            ->route('reservas.index')
            ->with('status', "Reserva de {$inscripcion->alumno->nombreCompleto()} rechazada.");
    }
}
