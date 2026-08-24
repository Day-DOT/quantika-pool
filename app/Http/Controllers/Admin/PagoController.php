<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ConceptoPago;
use App\Enums\EstadoAlumno;
use App\Enums\EstadoPago;
use App\Enums\MetodoPago;
use App\Http\Controllers\Admin\Concerns\ScopesSucursal;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePagoRequest;
use App\Models\Alumno;
use App\Models\Pago;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PagoController extends Controller
{
    use AuthorizesRequests;
    use ScopesSucursal;

    public function index(): View
    {
        $this->authorize('viewAny', Pago::class);

        $hoy = Carbon::now();
        $periodoActual = $hoy->format('Y-m');

        $cobradoMesQuery = Pago::query()
            ->where('estado', EstadoPago::Pagado->value)
            ->whereYear('fecha_pago', $hoy->year)
            ->whereMonth('fecha_pago', $hoy->month);
        $this->aplicarSucursal($cobradoMesQuery);
        $cobradoMes = (float) $cobradoMesQuery->sum('monto');

        $mesAnterior = $hoy->copy()->subMonthNoOverflow();
        $cobradoMesAnteriorQuery = Pago::query()
            ->where('estado', EstadoPago::Pagado->value)
            ->whereYear('fecha_pago', $mesAnterior->year)
            ->whereMonth('fecha_pago', $mesAnterior->month);
        $this->aplicarSucursal($cobradoMesAnteriorQuery);
        $cobradoMesAnterior = (float) $cobradoMesAnteriorQuery->sum('monto');

        $cambioPct = $cobradoMesAnterior > 0
            ? round((($cobradoMes - $cobradoMesAnterior) / $cobradoMesAnterior) * 100, 1)
            : null;

        $pendientesQuery = Pago::query()->where('periodo', $periodoActual)->where('estado', EstadoPago::Pendiente->value);
        $this->aplicarSucursal($pendientesQuery);
        $pendientesMonto = (float) (clone $pendientesQuery)->sum('monto');
        $pendientesCount = (clone $pendientesQuery)->count();

        $revisionQuery = Pago::query()->where('periodo', $periodoActual)->where('estado', EstadoPago::EnRevision->value);
        $this->aplicarSucursal($revisionQuery);
        $revisionMonto = (float) (clone $revisionQuery)->sum('monto');
        $revisionCount = (clone $revisionQuery)->count();

        $pagadosQuery = Pago::query()->where('periodo', $periodoActual)->where('estado', EstadoPago::Pagado->value);
        $this->aplicarSucursal($pagadosQuery);
        $pagadosMonto = (float) (clone $pagadosQuery)->sum('monto');
        $pagadosCount = (clone $pagadosQuery)->count();

        $deudoresQuery = Pago::vencidos();
        $this->aplicarSucursal($deudoresQuery);
        $deudoresCount = $deudoresQuery->pluck('alumno_id')->unique()->count();

        $ingresosPorMes = collect(range(7, 0))->map(function (int $offset) use ($hoy) {
            $mes = $hoy->copy()->subMonthsNoOverflow($offset);
            $query = Pago::query()
                ->where('estado', EstadoPago::Pagado->value)
                ->whereYear('fecha_pago', $mes->year)
                ->whereMonth('fecha_pago', $mes->month);
            $this->aplicarSucursal($query);

            return [
                'label' => ucfirst($mes->locale('es')->isoFormat('MMM')),
                'total' => (float) $query->sum('monto'),
            ];
        });
        $maxIngresoMensual = max(1, $ingresosPorMes->max('total'));

        $deudoresPreview = Pago::vencidos();
        $this->aplicarSucursal($deudoresPreview);
        $deudoresPreview = $deudoresPreview->with('alumno.nivel')->orderBy('fecha_vencimiento')->take(5)->get();

        $proximosVencerQuery = Pago::proximosAVencer(5);
        $this->aplicarSucursal($proximosVencerQuery);
        $proximosVencer = $proximosVencerQuery->with('alumno.nivel')
            ->orderBy('fecha_vencimiento')
            ->get()
            ->map(fn (Pago $pago) => [
                'pago' => $pago,
                'diasRestantes' => max(0, $hoy->diffInDays($pago->fecha_vencimiento, false)),
            ]);

        return view('quantika.pagos.index', [
            'cobradoMes' => $cobradoMes,
            'cambioPct' => $cambioPct,
            'pendientesMonto' => $pendientesMonto,
            'pendientesCount' => $pendientesCount,
            'revisionMonto' => $revisionMonto,
            'revisionCount' => $revisionCount,
            'pagadosMonto' => $pagadosMonto,
            'pagadosCount' => $pagadosCount,
            'deudoresCount' => $deudoresCount,
            'ingresosPorMes' => $ingresosPorMes,
            'maxIngresoMensual' => $maxIngresoMensual,
            'deudoresPreview' => $deudoresPreview,
            'proximosVencer' => $proximosVencer,
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Pago::class);

        $alumnosQuery = Alumno::query()->where('estado', EstadoAlumno::Activo->value)->with('sucursal');
        $this->aplicarSucursal($alumnosQuery);

        return view('quantika.pagos.registrar', [
            'alumnos' => $alumnosQuery->orderBy('nombre')->get(),
            'alumnoSeleccionado' => $request->integer('alumno'),
            'conceptos' => ConceptoPago::cases(),
            'metodos' => MetodoPago::cases(),
            'estados' => EstadoPago::cases(),
            'periodoSugerido' => now()->format('Y-m'),
        ]);
    }

    public function store(StorePagoRequest $request): RedirectResponse
    {
        $datos = $request->validated();
        $alumno = Alumno::findOrFail($datos['alumno_id']);

        $comprobantePath = null;
        if ($request->hasFile('comprobante')) {
            $comprobantePath = $request->file('comprobante')->store('comprobantes', 'public');
        }

        $pago = Pago::create([
            'alumno_id' => $alumno->id,
            'sucursal_id' => $alumno->sucursal_id,
            'concepto' => $datos['concepto'],
            'periodo' => $datos['periodo'] ?? now()->format('Y-m'),
            'monto' => $datos['monto'],
            'fecha_vencimiento' => $datos['fecha_vencimiento'] ?? null,
            'fecha_pago' => $datos['estado'] === EstadoPago::Pagado->value
                ? ($datos['fecha_pago'] ?? now()->toDateString())
                : ($datos['fecha_pago'] ?? null),
            'metodo_pago' => $datos['metodo_pago'] ?? null,
            'estado' => $datos['estado'],
            'comprobante_path' => $comprobantePath,
            'observaciones' => $datos['observaciones'] ?? null,
            'registrado_por' => auth()->id(),
        ]);

        return redirect()->route('pagos.alumno', $alumno)->with('status', "Pago de {$alumno->nombreCompleto()} registrado correctamente.");
    }

    public function alumno(Alumno $alumno): View
    {
        $this->authorize('viewAny', Pago::class);

        $pagos = $alumno->pagos()->orderByDesc('fecha_vencimiento')->get();

        return view('quantika.pagos.alumno', [
            'alumno' => $alumno->load('nivel', 'sucursal', 'tutorUser'),
            'pagos' => $pagos,
            'totalPagado' => (float) $pagos->where('estado', EstadoPago::Pagado)->sum('monto'),
            'totalPendiente' => (float) $pagos->whereIn('estado', [EstadoPago::Pendiente, EstadoPago::Vencido])->sum('monto'),
        ]);
    }

    public function deudores(): View
    {
        $this->authorize('viewAny', Pago::class);

        $query = Pago::vencidos();
        $this->aplicarSucursal($query);

        $deudores = $query->with('alumno.nivel', 'alumno.sucursal')->orderBy('fecha_vencimiento')->get();

        return view('quantika.pagos.deudores', [
            'deudores' => $deudores,
            'totalVencido' => (float) $deudores->sum('monto'),
        ]);
    }

    public function marcarPagado(Request $request, Pago $pago): RedirectResponse
    {
        $this->authorize('update', $pago);

        $metodo = $request->input('metodo_pago', MetodoPago::Efectivo->value);

        $pago->update([
            'estado' => EstadoPago::Pagado->value,
            'fecha_pago' => now()->toDateString(),
            'metodo_pago' => $metodo,
        ]);

        return back()->with('status', 'Pago marcado como pagado.');
    }
}
