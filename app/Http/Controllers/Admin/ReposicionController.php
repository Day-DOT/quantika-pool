<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EstadoInscripcion;
use App\Http\Controllers\Admin\Concerns\ScopesSucursal;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReponerCitaRequest;
use App\Models\Cita;
use App\Models\Horario;
use App\Models\SolicitudReposicion;
use App\Support\Reposiciones;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReposicionController extends Controller
{
    use AuthorizesRequests;
    use ScopesSucursal;

    public function index(): View
    {
        $this->authorize('viewAny', Cita::class);

        $sucursalId = $this->sucursalId();

        $faltas = Cita::query()
            ->with(['alumno.nivel', 'horario'])
            ->where('asistio', false)
            ->whereDoesntHave('reposicion')
            ->whereMonth('fecha', now()->month)
            ->whereYear('fecha', now()->year)
            ->when($sucursalId, fn ($q) => $q->where('sucursal_id', $sucursalId))
            ->orderBy('fecha')
            ->get();

        $reposiciones = Cita::query()
            ->with(['alumno', 'horario', 'citaOriginal'])
            ->whereNotNull('reposicion_de_id')
            ->when($sucursalId, fn ($q) => $q->where('sucursal_id', $sucursalId))
            ->orderByDesc('fecha')
            ->limit(30)
            ->get();

        $horariosDisponibles = Horario::query()
            ->where('activo', true)
            ->with(['nivel', 'carril', 'instructor.user'])
            ->when($sucursalId, fn ($q) => $q->where('sucursal_id', $sucursalId))
            ->orderBy('dia_semana')
            ->orderBy('hora_inicio')
            ->get();

        return view('quantika.reposiciones.index', [
            'faltas' => $faltas,
            'reposiciones' => $reposiciones,
            'horariosDisponibles' => $horariosDisponibles,
            'maximoPorMes' => Reposiciones::MAXIMO_POR_MES,
            'solicitudesPendientes' => Reposiciones::solicitudesPendientes($sucursalId)->count(),
        ]);
    }

    public function store(ReponerCitaRequest $request, Cita $cita): RedirectResponse
    {
        $datos = $request->validated();
        $horario = Horario::findOrFail($datos['horario_id']);
        $fecha = Carbon::parse($datos['fecha'])->startOfDay();

        if ($motivo = Reposiciones::motivoDeRechazo($cita, $horario, $fecha)) {
            return back()->withErrors([$motivo[0] => $motivo[1]]);
        }

        Reposiciones::programar($cita, $horario, $fecha, $request->user()->id);

        return redirect()
            ->route('reposiciones.index')
            ->with('status', "Reposición programada para {$cita->alumno->nombreCompleto()}.");
    }

    /**
     * Aprueba una solicitud hecha desde el portal: vuelve a revisar las
     * reglas (el cupo pudo ocuparse mientras esperaba) y crea la clase de
     * reposición para esa única fecha, ligada a la falta original.
     */
    public function aprobarSolicitud(Request $request, SolicitudReposicion $solicitud): RedirectResponse
    {
        $this->authorize('update', $solicitud->falta);

        if ($solicitud->estado !== EstadoInscripcion::Pendiente) {
            return back()->withErrors(['solicitud' => 'Esta solicitud ya fue procesada.']);
        }

        if ($motivo = Reposiciones::motivoDeRechazo($solicitud->falta, $solicitud->horario, $solicitud->fecha)) {
            return back()->withErrors(['solicitud' => "No se puede aprobar: {$motivo[1]}"]);
        }

        DB::transaction(function () use ($request, $solicitud) {
            Reposiciones::programar($solicitud->falta, $solicitud->horario, $solicitud->fecha, $request->user()->id);

            $solicitud->update([
                'estado' => EstadoInscripcion::Aprobada->value,
                'resuelta_por' => $request->user()->id,
                'resuelta_en' => now(),
            ]);
        });

        return back()->with('status', "Reposición de {$solicitud->alumno->nombreCompleto()} aprobada y programada.");
    }

    public function rechazarSolicitud(Request $request, SolicitudReposicion $solicitud): RedirectResponse
    {
        $this->authorize('update', $solicitud->falta);

        if ($solicitud->estado !== EstadoInscripcion::Pendiente) {
            return back()->withErrors(['solicitud' => 'Esta solicitud ya fue procesada.']);
        }

        $solicitud->update([
            'estado' => EstadoInscripcion::Rechazada->value,
            'resuelta_por' => $request->user()->id,
            'resuelta_en' => now(),
        ]);

        return back()->with('status', "Solicitud de reposición de {$solicitud->alumno->nombreCompleto()} rechazada.");
    }
}
