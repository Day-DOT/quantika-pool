<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\CalculaProgresoNivel;
use App\Http\Controllers\Admin\Concerns\ScopesSucursal;
use App\Http\Controllers\Controller;
use App\Models\Nivel;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;

class NivelController extends Controller
{
    use AuthorizesRequests;
    use CalculaProgresoNivel;
    use ScopesSucursal;

    public function index(): View
    {
        $this->authorize('viewAny', Nivel::class);

        $sucursalId = $this->sucursalId();

        $niveles = Nivel::ordenados()->get()->map(function (Nivel $nivel) use ($sucursalId) {
            $datos = $this->progresoDeNivel($nivel, $sucursalId);

            return [
                'nivel' => $nivel,
                'alumnos' => $datos['alumnos'],
                'progreso' => $datos['progreso'],
            ];
        });

        return view('quantika.niveles.index', [
            'niveles' => $niveles,
            'totalAlumnosConNivel' => $niveles->sum('alumnos'),
        ]);
    }
}
