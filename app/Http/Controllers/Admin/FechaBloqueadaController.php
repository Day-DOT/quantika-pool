<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesSucursal;
use App\Http\Controllers\Controller;
use App\Models\FechaBloqueada;
use App\Models\Sucursal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FechaBloqueadaController extends Controller
{
    use ScopesSucursal;

    public function index(): View
    {
        $bloqueos = FechaBloqueada::query()->with('sucursal')->orderByDesc('fecha_inicio');

        if ($sucursalId = $this->sucursalId()) {
            $bloqueos->deLaSucursal($sucursalId);
        }

        return view('quantika.fechas-bloqueadas.index', [
            'bloqueos' => $bloqueos->get(),
            'sucursales' => Sucursal::orderBy('nombre')->get(),
            'esVistaGlobal' => $this->sucursalId() === null,
            'editando' => null,
        ]);
    }

    public function edit(FechaBloqueada $fechaBloqueada): View
    {
        $this->autorizar($fechaBloqueada);

        return view('quantika.fechas-bloqueadas.index', [
            'bloqueos' => FechaBloqueada::with('sucursal')->deLaSucursal($this->sucursalId())->orderByDesc('fecha_inicio')->get(),
            'sucursales' => Sucursal::orderBy('nombre')->get(),
            'esVistaGlobal' => $this->sucursalId() === null,
            'editando' => $fechaBloqueada,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        FechaBloqueada::create($this->datos($request) + ['registrado_por' => $request->user()->id]);

        return redirect()->route('fechas-bloqueadas.index')->with('status', 'Fecha bloqueada agregada.');
    }

    public function update(Request $request, FechaBloqueada $fechaBloqueada): RedirectResponse
    {
        $this->autorizar($fechaBloqueada);
        $fechaBloqueada->update($this->datos($request));

        return redirect()->route('fechas-bloqueadas.index')->with('status', 'Fecha bloqueada actualizada.');
    }

    public function destroy(FechaBloqueada $fechaBloqueada): RedirectResponse
    {
        $this->autorizar($fechaBloqueada);
        $fechaBloqueada->delete();

        return redirect()->route('fechas-bloqueadas.index')->with('status', 'Fecha desbloqueada.');
    }

    /**
     * @return array{sucursal_id: ?int, fecha_inicio: string, fecha_fin: string, motivo: string}
     */
    private function datos(Request $request): array
    {
        $datos = $request->validate([
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'motivo' => ['required', 'string', 'max:150'],
            'sucursal_id' => ['nullable', 'integer', 'exists:sucursales,id'],
        ], [
            'fecha_inicio.required' => 'Elige la fecha (o la primera fecha del periodo).',
            'fecha_fin.after_or_equal' => 'La fecha final no puede ser anterior a la inicial.',
            'motivo.required' => 'Escribe el motivo (festivo, vacaciones, mantenimiento...).',
        ]);

        return [
            // El administrador de sucursal solo bloquea la suya; el super admin en
            // vista global puede elegir una o dejarlo para todas.
            'sucursal_id' => $this->sucursalId() ?? ($datos['sucursal_id'] ?? null),
            'fecha_inicio' => $datos['fecha_inicio'],
            'fecha_fin' => $datos['fecha_fin'] ?? $datos['fecha_inicio'],
            'motivo' => $datos['motivo'],
        ];
    }

    private function autorizar(FechaBloqueada $fechaBloqueada): void
    {
        $sucursalId = $this->sucursalId();

        abort_if($sucursalId && $fechaBloqueada->sucursal_id !== $sucursalId, 403, 'Ese bloqueo aplica a todas las sucursales o a otra sucursal.');
    }
}
