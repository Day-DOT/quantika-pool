<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EstadoCita;
use App\Http\Controllers\Admin\Concerns\ScopesSucursal;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReponerCitaRequest;
use App\Models\Cita;
use App\Models\Horario;
use App\Models\Inscripcion;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ReposicionController extends Controller
{
    use AuthorizesRequests;
    use ScopesSucursal;

    private const MAXIMO_POR_MES = 2;

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
            ->when($sucursalId, fn ($q) => $q->where('sucursal_id', $sucursalId))
            ->orderBy('dia_semana')
            ->orderBy('hora_inicio')
            ->get();

        return view('quantika.reposiciones.index', [
            'faltas' => $faltas,
            'reposiciones' => $reposiciones,
            'horariosDisponibles' => $horariosDisponibles,
            'maximoPorMes' => self::MAXIMO_POR_MES,
        ]);
    }

    public function store(ReponerCitaRequest $request, Cita $cita): RedirectResponse
    {
        $datos = $request->validated();
        $alumno = $cita->alumno;

        if ($cita->asistio !== false) {
            return back()->withErrors(['fecha' => 'Esa clase no está marcada como falta.']);
        }

        if ($cita->reposicion()->exists()) {
            return back()->withErrors(['fecha' => 'Esta falta ya tiene una reposición programada.']);
        }

        if ($alumno->nivel?->categoria_edad === 'Bebés') {
            return back()->withErrors(['fecha' => 'Las clases de bebés no tienen reposición.']);
        }

        $nuevaFecha = Carbon::parse($datos['fecha']);

        if (! $nuevaFecha->isSameMonth($cita->fecha)) {
            return back()->withErrors([
                'fecha' => 'La reposición debe realizarse dentro del mismo mes calendario en que se produjo la falta.',
            ]);
        }

        $usadasEsteMes = Cita::where('alumno_id', $alumno->id)
            ->whereNotNull('reposicion_de_id')
            ->whereMonth('fecha', $cita->fecha->month)
            ->whereYear('fecha', $cita->fecha->year)
            ->count();

        if ($usadasEsteMes >= self::MAXIMO_POR_MES) {
            return back()->withErrors([
                'fecha' => "{$alumno->nombreCompleto()} ya alcanzó el máximo de ".self::MAXIMO_POR_MES.' reposiciones este mes.',
            ]);
        }

        $nuevoHorario = Horario::findOrFail($datos['horario_id']);

        if ($nuevoHorario->sucursal_id !== $cita->sucursal_id) {
            return back()->withErrors(['horario_id' => 'El horario debe pertenecer a la misma sucursal.']);
        }

        if ($nuevaFecha->isoWeekday() !== $nuevoHorario->dia_semana->value) {
            return back()->withErrors([
                'fecha' => "La fecha elegida no coincide con el día ({$nuevoHorario->dia_semana->label()}) del horario seleccionado.",
            ]);
        }

        $inscritos = Inscripcion::where('horario_id', $nuevoHorario->id)->where('activa', true)->count();
        $reposicionesEseDia = Cita::where('horario_id', $nuevoHorario->id)
            ->whereDate('fecha', $nuevaFecha->toDateString())
            ->whereNotNull('reposicion_de_id')
            ->count();

        if (($inscritos + $reposicionesEseDia) >= $nuevoHorario->capacidad_maxima) {
            return back()->withErrors(['horario_id' => 'Ese horario ya no tiene cupo disponible para esa fecha.']);
        }

        Cita::create([
            'horario_id' => $nuevoHorario->id,
            'alumno_id' => $alumno->id,
            'sucursal_id' => $cita->sucursal_id,
            'fecha' => $nuevaFecha->toDateString(),
            'hora_inicio' => $nuevoHorario->hora_inicio,
            'hora_fin' => $nuevoHorario->hora_fin,
            'estado' => EstadoCita::Programada->value,
            'reposicion_de_id' => $cita->id,
        ]);

        return redirect()
            ->route('reposiciones.index')
            ->with('status', "Reposición programada para {$alumno->nombreCompleto()}.");
    }
}
