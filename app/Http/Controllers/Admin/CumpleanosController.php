<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesSucursal;
use App\Http\Controllers\Controller;
use App\Support\Cumpleanos;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class CumpleanosController extends Controller
{
    use ScopesSucursal;

    /**
     * Calendario mensual de cumpleaños de los alumnos activos, con la lista
     * de los próximos días. Es una pantalla aparte del calendario de clases.
     */
    public function index(Request $request): View
    {
        // Mes a mostrar (?mes=2026-11); si no llega o no es válido, el actual.
        $mes = rescue(
            fn () => Carbon::createFromFormat('!Y-m', (string) $request->query('mes', today()->format('Y-m'))),
            null,
            false
        ) ?: today()->startOfMonth();

        // Lunes de la semana del día 1 hasta el domingo de la semana del último día.
        $inicio = $mes->copy()->startOfMonth()->startOfWeek(Carbon::MONDAY);
        $fin = $mes->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY)->startOfDay();

        $dias = collect();
        for ($dia = $inicio->copy(); $dia->lte($fin); $dia->addDay()) {
            $dias->push($dia->copy());
        }

        return view('quantika.cumpleanos.index', [
            'mes' => $mes,
            'dias' => $dias,
            'cumpleanosDelMes' => Cumpleanos::delMes($this->sucursalId(), $mes),
            'proximos' => Cumpleanos::proximos($this->sucursalId()),
            'mesAnterior' => $mes->copy()->subMonthNoOverflow()->format('Y-m'),
            'mesSiguiente' => $mes->copy()->addMonthNoOverflow()->format('Y-m'),
        ]);
    }
}
