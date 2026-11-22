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
use App\Support\Anualidades;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PagoController extends Controller
{
    use AuthorizesRequests;
    use ScopesSucursal;

    private const MAXIMO_RESULTADOS_BUSQUEDA = 50;

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Pago::class);

        $hoy = Carbon::now();
        $mesCalendario = $request->validate([
            'mes' => ['nullable', 'date_format:Y-m'],
        ])['mes'] ?? $hoy->format('Y-m');
        // El "!" pone el día en 1: sin él toma el día de hoy y, un 29, 30 o
        // 31, el mes se desborda (p. ej. "31 de septiembre" pasa a octubre).
        $calendarioMes = Carbon::createFromFormat('!Y-m', $mesCalendario)->startOfMonth();
        $periodoSeleccionado = $calendarioMes->format('Y-m');

        $cobradoMesQuery = Pago::query()
            ->where('estado', EstadoPago::Pagado->value)
            ->whereYear('fecha_pago', $calendarioMes->year)
            ->whereMonth('fecha_pago', $calendarioMes->month);
        $this->aplicarSucursal($cobradoMesQuery);
        $cobradoMes = (float) $cobradoMesQuery->sum('monto');

        $mesAnterior = $calendarioMes->copy()->subMonthNoOverflow();
        $cobradoMesAnteriorQuery = Pago::query()
            ->where('estado', EstadoPago::Pagado->value)
            ->whereYear('fecha_pago', $mesAnterior->year)
            ->whereMonth('fecha_pago', $mesAnterior->month);
        $this->aplicarSucursal($cobradoMesAnteriorQuery);
        $cobradoMesAnterior = (float) $cobradoMesAnteriorQuery->sum('monto');

        $cambioPct = $cobradoMesAnterior > 0
            ? round((($cobradoMes - $cobradoMesAnterior) / $cobradoMesAnterior) * 100, 1)
            : null;

        $pendientesQuery = Pago::query()->where('periodo', $periodoSeleccionado)->where('estado', EstadoPago::Pendiente->value);
        $this->aplicarSucursal($pendientesQuery);
        $pendientesMonto = (float) (clone $pendientesQuery)->sum('monto');
        $pendientesCount = (clone $pendientesQuery)->count();

        $revisionQuery = Pago::query()->where('periodo', $periodoSeleccionado)->where('estado', EstadoPago::EnRevision->value);
        $this->aplicarSucursal($revisionQuery);
        $revisionMonto = (float) (clone $revisionQuery)->sum('monto');
        $revisionCount = (clone $revisionQuery)->count();

        $pagadosQuery = Pago::query()->where('periodo', $periodoSeleccionado)->where('estado', EstadoPago::Pagado->value);
        $this->aplicarSucursal($pagadosQuery);
        $pagadosMonto = (float) (clone $pagadosQuery)->sum('monto');
        $pagadosCount = (clone $pagadosQuery)->count();

        $deudoresQuery = Pago::vencidos();
        $this->aplicarSucursal($deudoresQuery);
        $deudoresCount = $deudoresQuery->pluck('alumno_id')->unique()->count();

        $ingresosPorMes = collect(range(7, 0))->map(function (int $offset) use ($calendarioMes) {
            $mes = $calendarioMes->copy()->subMonthsNoOverflow($offset);
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

        // Cada pago va en el día en que vence; los que se registraron ya
        // pagados sin fecha de vencimiento, en el día en que se pagaron.
        $inicioMes = $calendarioMes->toDateString();
        $finMes = $calendarioMes->copy()->endOfMonth()->toDateString();
        $calendarioPagosQuery = Pago::query()
            ->where(fn ($query) => $query
                ->where(fn ($conVencimiento) => $conVencimiento
                    ->whereDate('fecha_vencimiento', '>=', $inicioMes)
                    ->whereDate('fecha_vencimiento', '<=', $finMes))
                ->orWhere(fn ($sinVencimiento) => $sinVencimiento
                    ->whereNull('fecha_vencimiento')
                    ->whereDate('fecha_pago', '>=', $inicioMes)
                    ->whereDate('fecha_pago', '<=', $finMes)))
            ->with('alumno');
        $this->aplicarSucursal($calendarioPagosQuery);
        $calendarioPagos = $calendarioPagosQuery->get()->sortBy(fn (Pago $pago) => $pago->fechaCalendario()->toDateString())->values();

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
            'busqueda' => $this->resultadosBusqueda($request),
        ]);
    }

    /**
     * Resultados del buscador de pagos (los pide la página mientras se
     * escribe y se reemplazan sin recargarla).
     */
    public function buscar(Request $request): View
    {
        $this->authorize('viewAny', Pago::class);

        return view('quantika.pagos.partials.resultados-busqueda', $this->resultadosBusqueda($request));
    }

    /**
     * Pagos que coinciden con el buscador: alumno (nombre, apellidos,
     * matrícula o ID), estado, concepto y rango de fechas de corte (o de
     * pago, si no tiene corte).
     *
     * @return array{pagos: \Illuminate\Support\Collection<int, Pago>, total: int, buscando: bool}
     */
    private function resultadosBusqueda(Request $request): array
    {
        $filtros = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'estado' => ['nullable', Rule::in(['pagado', 'pendiente', 'vencido', 'en_revision'])],
            'concepto' => ['nullable', Rule::enum(ConceptoPago::class)],
            'desde' => ['nullable', 'date'],
            'hasta' => ['nullable', 'date'],
        ]);

        $buscando = collect($filtros)->filter(fn ($valor) => filled($valor))->isNotEmpty();

        if (! $buscando) {
            return ['pagos' => collect(), 'total' => 0, 'buscando' => false];
        }

        $query = Pago::query()->with('alumno');
        $this->aplicarSucursal($query);

        foreach (preg_split('/\s+/', trim($filtros['q'] ?? ''), -1, PREG_SPLIT_NO_EMPTY) as $palabra) {
            $query->whereHas('alumno', fn ($alumno) => $alumno->where(fn ($coincide) => $coincide
                ->where('nombre', 'like', "%{$palabra}%")
                ->orWhere('apellidos', 'like', "%{$palabra}%")
                ->orWhere('matricula', 'like', "%{$palabra}%")
                ->when(ctype_digit($palabra), fn ($porId) => $porId->orWhere('id', (int) $palabra))));
        }

        match ($filtros['estado'] ?? null) {
            'pagado' => $query->where('estado', EstadoPago::Pagado->value),
            'en_revision' => $query->where('estado', EstadoPago::EnRevision->value),
            'vencido' => $query->where(fn ($vencido) => $vencido
                ->where('estado', EstadoPago::Vencido->value)
                ->orWhere(fn ($sinPagar) => $sinPagar->where('estado', '!=', EstadoPago::Pagado->value)->whereDate('fecha_vencimiento', '<', today()))),
            'pendiente' => $query->where('estado', EstadoPago::Pendiente->value)
                ->where(fn ($aTiempo) => $aTiempo->whereNull('fecha_vencimiento')->orWhereDate('fecha_vencimiento', '>=', today())),
            default => null,
        };

        if (! empty($filtros['concepto'])) {
            $query->where('concepto', $filtros['concepto']);
        }

        if (! empty($filtros['desde']) || ! empty($filtros['hasta'])) {
            $enRango = function ($q, string $columna) use ($filtros) {
                if (! empty($filtros['desde'])) {
                    $q->whereDate($columna, '>=', $filtros['desde']);
                }
                if (! empty($filtros['hasta'])) {
                    $q->whereDate($columna, '<=', $filtros['hasta']);
                }
            };

            $query->where(fn ($fecha) => $fecha
                ->where(fn ($conCorte) => $enRango($conCorte->whereNotNull('fecha_vencimiento'), 'fecha_vencimiento'))
                ->orWhere(fn ($sinCorte) => $enRango($sinCorte->whereNull('fecha_vencimiento'), 'fecha_pago')));
        }

        $total = (clone $query)->count();

        $pagos = $query
            ->orderByRaw('COALESCE(fecha_vencimiento, fecha_pago) DESC')
            ->orderByDesc('id')
            ->limit(self::MAXIMO_RESULTADOS_BUSQUEDA)
            ->get();

        return ['pagos' => $pagos, 'total' => $total, 'buscando' => true];
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

        $fechaPago = $datos['estado'] === EstadoPago::Pagado->value
            ? ($datos['fecha_pago'] ?? now()->toDateString())
            : ($datos['fecha_pago'] ?? null);

        // Un pago de mensualidad o anualidad que llega cuando ya hay un cargo
        // sin pagar de ese concepto liquida ese cargo (el más antiguo) y
        // conserva su fecha de corte. Antes se creaba uno nuevo con la fecha
        // en que llegó el dinero: el cargo original quedaba como deuda y el
        // ciclo se recorría (corte del 12 pagado el 16 → siguiente el 16).
        $adeudo = $datos['estado'] === EstadoPago::Pagado->value
            ? $this->adeudoPorLiquidar($alumno, ConceptoPago::from($datos['concepto']))
            : null;

        if ($adeudo) {
            $adeudo->update([
                'monto' => $datos['monto'],
                'fecha_pago' => $fechaPago,
                'metodo_pago' => $datos['metodo_pago'] ?? null,
                'estado' => EstadoPago::Pagado->value,
                'comprobante_path' => $comprobantePath ?? $adeudo->comprobante_path,
                'observaciones' => $datos['observaciones'] ?? $adeudo->observaciones,
                'registrado_por' => auth()->id(),
            ]);
            $this->agendarSiguientePago($adeudo);

            return redirect()->route('pagos.alumno', $alumno)->with(
                'status',
                "Se liquidó el cargo de {$alumno->nombreCompleto()} con corte el {$adeudo->fecha_vencimiento->format('d/m/Y')}; el siguiente se agendó a partir de esa fecha."
            );
        }

        $pago = Pago::create([
            'alumno_id' => $alumno->id,
            'sucursal_id' => $alumno->sucursal_id,
            'concepto' => $datos['concepto'],
            'periodo' => $datos['periodo'] ?? now()->format('Y-m'),
            'monto' => $datos['monto'],
            'fecha_vencimiento' => $datos['fecha_vencimiento'] ?? null,
            'fecha_pago' => $fechaPago,
            'metodo_pago' => $datos['metodo_pago'] ?? null,
            'estado' => $datos['estado'],
            'comprobante_path' => $comprobantePath,
            'observaciones' => $datos['observaciones'] ?? null,
            'registrado_por' => auth()->id(),
        ]);

        if ($pago->estado === EstadoPago::Pagado) {
            $this->agendarSiguientePago($pago);
        }

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

        if ($pago->estado === EstadoPago::Pagado) {
            $this->agendarSiguientePago($pago);
        }

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

        $metodo = MetodoPago::from($request->validate([
            'metodo_pago' => ['nullable', Rule::enum(MetodoPago::class)],
        ])['metodo_pago'] ?? MetodoPago::Efectivo->value);

        $pago->update([
            'estado' => EstadoPago::Pagado->value,
            'fecha_pago' => now()->toDateString(),
            'metodo_pago' => $metodo->value,
        ]);
        $this->agendarSiguientePago($pago);

        return back()->with('status', "Pago marcado como pagado ({$metodo->label()}).");
    }

    /**
     * Muestra el comprobante de un pago. Se sirve desde aquí (y no con un
     * enlace público) para que solo lo vea quien tiene acceso al pago.
     */
    public function comprobante(Pago $pago): StreamedResponse
    {
        $this->authorize('view', $pago);

        abort_unless(
            $pago->comprobante_path && Storage::disk('public')->exists($pago->comprobante_path),
            404,
            'El archivo del comprobante no está disponible. Vuelve a subirlo desde "Editar".'
        );

        return Storage::disk('public')->response($pago->comprobante_path);
    }

    /**
     * Cargo sin pagar más antiguo (con fecha de corte) de una mensualidad o
     * anualidad del alumno; los demás conceptos siempre son pagos nuevos.
     */
    private function adeudoPorLiquidar(Alumno $alumno, ConceptoPago $concepto): ?Pago
    {
        if (! in_array($concepto, [ConceptoPago::Mensualidad, ConceptoPago::Inscripcion], true)) {
            return null;
        }

        return $alumno->pagos()
            ->where('concepto', $concepto->value)
            ->whereIn('estado', [EstadoPago::Pendiente->value, EstadoPago::Vencido->value, EstadoPago::EnRevision->value])
            ->whereNotNull('fecha_vencimiento')
            ->orderBy('fecha_vencimiento')
            ->first();
    }

    /**
     * Al pagarse una mensualidad o una anualidad se agenda la siguiente.
     */
    private function agendarSiguientePago(Pago $pago): void
    {
        $this->agendarSiguienteMensualidad($pago);
        Anualidades::agendarSiguiente($pago);
    }

    /**
     * Al pagarse una mensualidad se agenda la del mes siguiente por el
     * mismo monto que se acaba de pagar: así se conservan los descuentos y
     * precios acordados con cada alumno, sin volver al precio del plan.
     */
    private function agendarSiguienteMensualidad(Pago $pago): void
    {
        if ($pago->concepto !== ConceptoPago::Mensualidad
            || ! $pago->fecha_vencimiento
            || ! $pago->alumno?->plan
            || $pago->alumno->estado !== EstadoAlumno::Activo) {
            return;
        }

        $siguienteFecha = $pago->fecha_vencimiento->copy()->addMonthNoOverflow();
        $yaExiste = Pago::query()
            ->where('alumno_id', $pago->alumno_id)
            ->where('concepto', ConceptoPago::Mensualidad->value)
            ->whereDate('fecha_vencimiento', $siguienteFecha->toDateString())
            ->exists();

        if ($yaExiste) {
            return;
        }

        Pago::create([
            'alumno_id' => $pago->alumno_id,
            'sucursal_id' => $pago->sucursal_id,
            'concepto' => ConceptoPago::Mensualidad->value,
            'periodo' => $siguienteFecha->format('Y-m'),
            'monto' => $pago->monto,
            'fecha_vencimiento' => $siguienteFecha->toDateString(),
            'estado' => EstadoPago::Pendiente->value,
        ]);
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
