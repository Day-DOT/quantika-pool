<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ConceptoPago;
use App\Enums\EstadoAlumno;
use App\Enums\EstadoPago;
use App\Enums\MetodoPago;
use App\Http\Controllers\Admin\Concerns\ScopesSucursal;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePagoRequest;
use App\Http\Requests\Admin\UpdatePagoRequest;
use App\Models\Alumno;
use App\Models\Pago;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PagoController extends Controller
{
    use AuthorizesRequests;
    use ScopesSucursal;

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Pago::class);

        $hoy = Carbon::now();
        $mesCalendario = $request->validate([
            'mes' => ['nullable', 'date_format:Y-m'],
        ])['mes'] ?? $hoy->format('Y-m');
        $calendarioMes = Carbon::createFromFormat('Y-m', $mesCalendario)->startOfMonth();
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

        // Alumnos próximos a que les toque su siguiente mensualidad, estimado
        // a partir de su último pago (o de su inscripción si nunca ha
        // pagado). No depende de que ya exista un registro de pago
        // "pendiente" para el siguiente periodo.
        $diasVentanaProximoPago = 7;
        $alumnosActivosQuery = Alumno::query()
            ->where('estado', EstadoAlumno::Activo->value)
            ->with(['nivel', 'ultimoPagoMensualidad']);
        $this->aplicarSucursal($alumnosActivosQuery);

        $proximosAPagar = $alumnosActivosQuery->get()
            ->map(fn (Alumno $alumno) => [
                'alumno' => $alumno,
                'proximaFecha' => $alumno->proximaFechaPago(),
            ])
            ->filter(fn (array $fila) => $fila['proximaFecha'] !== null
                && $fila['proximaFecha']->between($hoy->copy()->startOfDay(), $hoy->copy()->addDays($diasVentanaProximoPago)->endOfDay()))
            ->sortBy(fn (array $fila) => $fila['proximaFecha'])
            ->map(fn (array $fila) => [
                'alumno' => $fila['alumno'],
                'proximaFecha' => $fila['proximaFecha'],
                'diasRestantes' => max(0, $hoy->copy()->startOfDay()->diffInDays($fila['proximaFecha'], false)),
            ])
            ->values();

        $calendarioPagosQuery = Pago::query()
            ->whereNotNull('fecha_vencimiento')
            ->whereBetween('fecha_vencimiento', [
                $calendarioMes->toDateString(),
                $calendarioMes->copy()->endOfMonth()->toDateString(),
            ])
            ->with('alumno')
            ->orderBy('fecha_vencimiento');
        $this->aplicarSucursal($calendarioPagosQuery);
        $calendarioPagos = $calendarioPagosQuery->get();

        $proyeccionesQuery = Alumno::query()
            ->where('estado', EstadoAlumno::Activo->value)
            ->whereNotNull('plan_id')
            ->with(['plan', 'ultimoPagoMensualidad', 'sucursal']);
        $this->aplicarSucursal($proyeccionesQuery);

        $proyecciones = $proyeccionesQuery->get()
            ->filter(function (Alumno $alumno) use ($calendarioMes) {
                $fecha = $alumno->proximaFechaPago();

                return $fecha
                    && $alumno->plan?->precio !== null
                    && $fecha->isSameMonth($calendarioMes)
                    && ! $alumno->pagos()
                        ->whereDate('fecha_vencimiento', $fecha->toDateString())
                        ->exists();
            })
            ->map(function (Alumno $alumno) {
                $pago = new \App\Models\Pago([
                    'alumno_id' => $alumno->id,
                    'sucursal_id' => $alumno->sucursal_id,
                    'concepto' => ConceptoPago::Mensualidad->value,
                    'periodo' => $alumno->proximaFechaPago()->format('Y-m'),
                    'monto' => $alumno->plan->precio,
                    'fecha_vencimiento' => $alumno->proximaFechaPago()->toDateString(),
                    'estado' => EstadoPago::Pendiente->value,
                ]);
                $pago->setRelation('alumno', $alumno);
                $pago->setAttribute('es_proyeccion', true);

                return $pago;
            });

        $calendarioPagos = $calendarioPagos->concat($proyecciones)->sortBy('fecha_vencimiento')->values();

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
            'proximosAPagar' => $proximosAPagar,
            'hoy' => $hoy,
            'calendarioMes' => $calendarioMes,
            'calendarioPagos' => $calendarioPagos,
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

    public function edit(Pago $pago): View
    {
        $this->authorize('update', $pago);

        $alumnosQuery = Alumno::query()
            ->where(function ($query) use ($pago) {
                $query->where('estado', EstadoAlumno::Activo->value)
                    ->orWhere('id', $pago->alumno_id);
            })
            ->with('sucursal');
        $this->aplicarSucursal($alumnosQuery);

        return view('quantika.pagos.registrar', [
            'alumnos' => $alumnosQuery->orderBy('nombre')->get(),
            'alumnoSeleccionado' => $pago->alumno_id,
            'conceptos' => ConceptoPago::cases(),
            'estados' => EstadoPago::cases(),
            'periodoSugerido' => $pago->periodo ?? now()->format('Y-m'),
            'pago' => $pago,
        ]);
    }

    public function update(UpdatePagoRequest $request, Pago $pago): RedirectResponse
    {
        $datos = $request->validated();
        $alumno = Alumno::findOrFail($datos['alumno_id']);

        if (! auth()->user()->isSuperAdmin() && $alumno->sucursal_id !== $pago->sucursal_id) {
            abort(403);
        }

        $actualizacion = [
            'alumno_id' => $alumno->id,
            'sucursal_id' => $alumno->sucursal_id,
            'concepto' => $datos['concepto'],
            'periodo' => $datos['periodo'] ?? null,
            'monto' => $datos['monto'],
            'fecha_vencimiento' => $datos['fecha_vencimiento'] ?? null,
            'fecha_pago' => $datos['estado'] === EstadoPago::Pagado->value
                ? ($datos['fecha_pago'] ?? $pago->fecha_pago?->toDateString() ?? now()->toDateString())
                : ($datos['fecha_pago'] ?? null),
            'metodo_pago' => $datos['metodo_pago'] ?? null,
            'estado' => $datos['estado'],
            'observaciones' => $datos['observaciones'] ?? null,
        ];

        if ($request->hasFile('comprobante')) {
            if ($pago->comprobante_path) {
                Storage::disk('public')->delete($pago->comprobante_path);
            }
            $actualizacion['comprobante_path'] = $request->file('comprobante')->store('comprobantes', 'public');
        }

        $pago->update($actualizacion);

        return redirect()->route('pagos.alumno', $alumno)->with('status', 'Pago actualizado correctamente.');
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

    public function convertirProyeccion(Request $request): RedirectResponse
    {
        $this->authorize('create', Pago::class);

        $datos = $request->validate([
            'alumno_id' => ['required', 'integer', 'exists:alumnos,id'],
            'fecha_vencimiento' => ['required', 'date'],
        ]);

        $alumno = Alumno::with(['plan'])->findOrFail($datos['alumno_id']);

        if (! auth()->user()->isSuperAdmin() && $alumno->sucursal_id !== auth()->user()->sucursal_id) {
            abort(403);
        }

        $fechaVencimiento = Carbon::parse($datos['fecha_vencimiento'])->toDateString();
        $fechaProyectada = $alumno->proximaFechaPago()?->toDateString();

        if ($alumno->estado !== EstadoAlumno::Activo
            || ! $alumno->plan
            || $alumno->plan->precio === null
            || $fechaProyectada !== $fechaVencimiento) {
            abort(422, 'La proyección seleccionada ya no está disponible para convertirse.');
        }

        DB::transaction(function () use ($alumno, $fechaVencimiento): void {
            $pagoExistente = Pago::query()
                ->where('alumno_id', $alumno->id)
                ->whereDate('fecha_vencimiento', $fechaVencimiento)
                ->lockForUpdate()
                ->first();

            if ($pagoExistente) {
                if ($pagoExistente->estado !== EstadoPago::Pagado) {
                    $pagoExistente->update([
                        'estado' => EstadoPago::Pagado->value,
                        'fecha_pago' => now()->toDateString(),
                        'metodo_pago' => MetodoPago::Efectivo->value,
                        'registrado_por' => auth()->id(),
                    ]);
                }

                return;
            }

            Pago::create([
                'alumno_id' => $alumno->id,
                'sucursal_id' => $alumno->sucursal_id,
                'concepto' => ConceptoPago::Mensualidad->value,
                'periodo' => Carbon::parse($fechaVencimiento)->format('Y-m'),
                'monto' => $alumno->plan->precio,
                'fecha_vencimiento' => $fechaVencimiento,
                'fecha_pago' => now()->toDateString(),
                'metodo_pago' => MetodoPago::Efectivo->value,
                'estado' => EstadoPago::Pagado->value,
                'registrado_por' => auth()->id(),
            ]);
        });

        return back()->with('status', "Proyección de {$alumno->nombreCompleto()} convertida en pago pagado.");
    }

    public function destroy(Pago $pago): RedirectResponse
    {
        $this->authorize('delete', $pago);

        if ($pago->estado === EstadoPago::Pagado) {
            return back()->withErrors(['pago' => 'No se puede eliminar un pago que ya está marcado como pagado.']);
        }

        $alumno = $pago->alumno;

        if ($pago->comprobante_path) {
            Storage::disk('public')->delete($pago->comprobante_path);
        }

        $pago->delete();

        return back()->with('status', "Adeudo de {$alumno->nombreCompleto()} eliminado. No volverá a aparecer como pendiente.");
    }
}
