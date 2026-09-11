@extends('quantika.super-admin.layout')

@section('title', 'Pagos')
@section('page-title', 'Pagos')

@push('styles')
<style>
    /* =========================
       HEADER
    ========================= */

    .branch {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 11px 16px;
        border-radius: 16px;
        background: rgba(9, 66, 86, .60);
        border: 1px solid rgba(55, 207, 233, .22);
        color: #43d4ec;
        font-size: 13px;
        font-weight: bold;
        margin-bottom: 20px;
    }

    /* =========================
       STATISTICS
    ========================= */

    .stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 22px;
    }

    .stat {
        position: relative;
        overflow: hidden;
        background: linear-gradient(145deg, rgba(9, 67, 87, .95), rgba(4, 39, 54, .97));
        border: 1px solid rgba(52, 191, 219, .16);
        border-radius: 20px;
        padding: 20px;
        min-height: 130px;
    }

    .stat::after {
        content: "";
        position: absolute;
        width: 110px;
        height: 110px;
        right: -55px;
        top: -55px;
        border-radius: 50%;
        background: rgba(46, 210, 237, .05);
    }

    .stat-label {
        color: #749cab;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .8px;
        margin-bottom: 12px;
    }

    .stat-value {
        font-size: 29px;
        font-weight: 900;
        margin-bottom: 6px;
    }

    .stat-small {
        color: #719aa9;
        font-size: 11px;
    }

    .green { color: #2ce0ae; }
    .yellow { color: #ffc32d; }
    .blue { color: #45d7ed; }
    .red { color: #ff7f8a; }

    /* =========================
       MAIN GRID
    ========================= */

    .main-grid {
        display: grid;
        grid-template-columns: 1.55fr .85fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    .card {
        background: linear-gradient(145deg, rgba(8, 61, 81, .96), rgba(3, 38, 53, .98));
        border: 1px solid rgba(55, 191, 218, .16);
        border-radius: 22px;
        padding: 22px;
    }

    .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .card-title {
        font-size: 18px;
        font-weight: 800;
    }

    .card-description {
        margin-top: 5px;
        color: #729bab;
        font-size: 12px;
    }

    /* =========================
       CHART
    ========================= */

    .chart {
        height: 250px;
        display: flex;
        align-items: flex-end;
        gap: 18px;
        padding: 15px 10px 0;
    }

    .chart-column {
        height: 100%;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        align-items: center;
        gap: 8px;
    }

    .chart-bar {
        width: 100%;
        max-width: 42px;
        border-radius: 9px 9px 4px 4px;
        background: linear-gradient(to top, #159bb9, #3ed8ec);
        box-shadow: 0 8px 20px rgba(31, 202, 229, .10);
    }

    .month {
        color: #688f9e;
        font-size: 10px;
    }

    /* =========================
       PAYMENT STATUS
    ========================= */

    .payment-status {
        display: flex;
        flex-direction: column;
        gap: 13px;
    }

    .status-row {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 13px;
        border-radius: 14px;
        background: rgba(0, 28, 42, .35);
        border: 1px solid rgba(255,255,255,.035);
    }

    .status-circle {
        width: 11px;
        height: 11px;
        border-radius: 50%;
    }

    .circle-green { background: #28dbae; box-shadow: 0 0 12px rgba(40,219,174,.35); }
    .circle-yellow { background: #ffc42d; box-shadow: 0 0 12px rgba(255,196,45,.30); }
    .circle-blue { background: #45d7ed; box-shadow: 0 0 12px rgba(69,215,237,.30); }

    .status-info { flex: 1; }

    .status-name {
        font-size: 13px;
        font-weight: 700;
    }

    .status-count {
        color: #759cab;
        font-size: 11px;
        margin-top: 3px;
    }

    .status-money {
        font-weight: 800;
        font-size: 13px;
    }

    /* =========================
       DEBTORS
    ========================= */

    .debtors {
        margin-top: 20px;
    }

    .table-container {
        overflow-x: auto;
    }

    .table-container table {
        width: 100%;
        border-collapse: collapse;
        min-width: 700px;
    }

    .table-container th {
        text-align: left;
        color: #668f9f;
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: .7px;
        padding: 12px 10px;
        border-bottom: 1px solid rgba(255,255,255,.07);
    }

    .table-container td {
        padding: 15px 10px;
        border-bottom: 1px solid rgba(255,255,255,.045);
        font-size: 13px;
    }

    .student {
        display: flex;
        align-items: center;
        gap: 11px;
    }

    /* Renombrado de .avatar a .row-avatar para no chocar con el .avatar del sidebar/topbar del layout */
    .row-avatar {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        background: linear-gradient(135deg, #d7edf3, #85b8c7);
        color: #063044;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 900;
    }

    .student-name {
        font-weight: 700;
    }

    .student-detail {
        color: #668f9f;
        font-size: 10px;
        margin-top: 3px;
    }

    .amount {
        font-weight: 800;
        color: #ff8790;
    }

    .status-pill {
        display: inline-block;
        padding: 6px 10px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 800;
    }

    .status-overdue {
        color: #ff8790;
        background: rgba(255,96,110,.09);
        border: 1px solid rgba(255,96,110,.18);
    }

    .status-soon {
        color: #ffbd20;
        background: rgba(255,189,32,.09);
        border: 1px solid rgba(255,189,32,.22);
    }

    .upcoming-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 12px 16px;
        border-radius: 14px;
        background: rgba(255,189,32,.05);
        border: 1px solid rgba(255,189,32,.15);
    }

    .upcoming-row + .upcoming-row {
        margin-top: 8px;
    }

    .calendar {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        gap: 8px;
    }

    .calendar-heading {
        color: #7fa5b4;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .6px;
        text-align: center;
        text-transform: uppercase;
        padding: 4px 0;
    }

    .calendar-day {
        min-height: 116px;
        padding: 8px;
        border-radius: 12px;
        background: rgba(0, 28, 42, .32);
        border: 1px solid rgba(255,255,255,.05);
    }

    .calendar-day.is-today { border-color: rgba(66,213,238,.55); }
    .calendar-day.is-empty { background: transparent; border-color: transparent; }
    .calendar-number { color: #a8c3cc; font-size: 11px; font-weight: 800; margin-bottom: 6px; }
    .calendar-event {
        display: block;
        margin-top: 4px;
        padding: 5px 6px;
        border-radius: 7px;
        font-size: 10px;
        line-height: 1.25;
        overflow: hidden;
    }
    .calendar-event strong { display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .calendar-event-green { color: #6ff2c7; background: rgba(22,224,164,.13); border-left: 3px solid #16e0a4; }
    .calendar-event-yellow { color: #ffd76d; background: rgba(255,189,32,.13); border-left: 3px solid #ffbd20; }
    .calendar-event-red { color: #ff9aa0; background: rgba(255,107,107,.13); border-left: 3px solid #ff6b6b; }
    .calendar-event-actions { display: flex; gap: 6px; margin-top: 4px; }
    .calendar-event-actions a, .calendar-event-actions button {
        color: inherit; background: transparent; border: 0; cursor: pointer; font-size: 9px; padding: 0;
        text-decoration: underline;
    }
    .calendar-more {
        width: 100%;
        margin-top: 6px;
        padding: 4px 6px;
        border: 1px solid rgba(69,215,237,.35);
        border-radius: 7px;
        background: rgba(69,215,237,.08);
        color: #8ae8f5;
        cursor: pointer;
        font-size: 10px;
    }
    .calendar-payments-dialog {
        width: min(560px, calc(100% - 32px));
        padding: 0;
        border: 1px solid rgba(55,191,218,.35);
        border-radius: 18px;
        background: linear-gradient(145deg, rgba(8,61,81,.99), rgba(3,38,53,.99));
        color: #e7f7fa;
    }
    .calendar-payments-dialog::backdrop { background: rgba(0, 12, 20, .72); }
    .calendar-payments-dialog-content { padding: 22px; }
    .calendar-payments-dialog-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
    }
    .calendar-payments-dialog-close {
        border: 0;
        background: transparent;
        color: #9bc2cc;
        cursor: pointer;
        font-size: 22px;
    }
    .calendar-modal-event { margin-top: 8px; }
    .calendar-legend { display: flex; flex-wrap: wrap; gap: 14px; color: var(--muted); font-size: 11px; margin-bottom: 15px; }
    .calendar-legend span { display: inline-flex; align-items: center; gap: 5px; }
    .calendar-dot { width: 9px; height: 9px; border-radius: 50%; display: inline-block; }
    .calendar-dot-green { background: #16e0a4; }
    .calendar-dot-yellow { background: #ffbd20; }
    .calendar-dot-red { background: #ff6b6b; }

    /* =========================
       RESPONSIVE
    ========================= */

    @media(max-width: 1100px) {
        .stats { grid-template-columns: repeat(2, 1fr); }
        .main-grid { grid-template-columns: 1fr; }
    }

    @media(max-width: 700px) {
        .stats { grid-template-columns: 1fr; }
        .calendar { gap: 4px; }
        .calendar-day { min-height: 86px; padding: 5px; }
        .calendar-event { font-size: 9px; padding: 4px; }
    }
</style>
@endpush

@section('content')

    <!-- HEADER -->
    <div class="section-header">
        <div>
            <p style="color:var(--muted); font-size:14px;">
                Administración de mensualidades, inscripciones, conceptos adicionales y saldos.
            </p>
        </div>

        <div class="actions" style="display:flex;gap:10px;flex-wrap:wrap;">
            <a href="{{ route('pagos.registrar') }}" class="btn btn-primary">+ Registrar fecha de pago</a>
        </div>
    </div>


    <!-- SUCURSAL -->
    <div class="branch">
        ◉ QUANTIKA POOL · {{ \App\Support\SucursalContext::actualId() ? \App\Models\Sucursal::find(\App\Support\SucursalContext::actualId())->nombre : 'TODAS LAS SUCURSALES' }}
    </div>


    <!-- ESTADÍSTICAS -->
    <div class="stats">

        <div class="stat">
            <div class="stat-label">COBRADO ESTE MES</div>
            <div class="stat-value green">${{ number_format($cobradoMes, 0) }}</div>
            <div class="stat-small">
                @if ($cambioPct === null)
                    Sin datos del mes anterior
                @else
                    {{ $cambioPct >= 0 ? '+' : '' }}{{ $cambioPct }}% respecto al mes anterior
                @endif
            </div>
        </div>

        <div class="stat">
            <div class="stat-label">PAGOS PENDIENTES</div>
            <div class="stat-value yellow">${{ number_format($pendientesMonto, 0) }}</div>
            <div class="stat-small">{{ $pendientesCount }} pagos pendientes</div>
        </div>

        <div class="stat">
            <div class="stat-label">EN REVISIÓN</div>
            <div class="stat-value blue">${{ number_format($revisionMonto, 0) }}</div>
            <div class="stat-small">{{ $revisionCount }} comprobantes</div>
        </div>

        <div class="stat">
            <div class="stat-label">DEUDORES</div>
            <div class="stat-value red">{{ $deudoresCount }}</div>
            <div class="stat-small">Alumnos con saldo vencido</div>
        </div>

    </div>


    <!-- GRAFICA + ESTADOS -->
    <div class="main-grid">

        <!-- GRÁFICA -->
        <div class="card">

            <div class="card-header">
                <div>
                    <div class="card-title">Ingresos del mes</div>
                    <div class="card-description">Comportamiento de los pagos registrados.</div>
                </div>

                <strong class="green">${{ number_format($cobradoMes, 0) }}</strong>
            </div>

            <div class="chart">
                @foreach ($ingresosPorMes as $mes)
                    <div class="chart-column">
                        <div class="chart-bar" style="height:{{ $mes['total'] > 0 ? max(6, round(($mes['total'] / $maxIngresoMensual) * 100)) : 2 }}%;"></div>
                        <span class="month">{{ mb_strtoupper($mes['label']) }}</span>
                    </div>
                @endforeach
            </div>

        </div>

        <!-- ESTADOS -->
        <div class="card">

            <div class="card-header">
                <div>
                    <div class="card-title">Estado de pagos</div>
                    <div class="card-description">Situación actual.</div>
                </div>
            </div>

            <div class="payment-status">

                <div class="status-row">
                    <div class="status-circle circle-green"></div>
                    <div class="status-info">
                        <div class="status-name">Pagados</div>
                        <div class="status-count">{{ $pagadosCount }} pagos</div>
                    </div>
                    <div class="status-money green">${{ number_format($pagadosMonto, 0) }}</div>
                </div>

                <div class="status-row">
                    <div class="status-circle circle-yellow"></div>
                    <div class="status-info">
                        <div class="status-name">Pendientes</div>
                        <div class="status-count">{{ $pendientesCount }} pagos</div>
                    </div>
                    <div class="status-money yellow">${{ number_format($pendientesMonto, 0) }}</div>
                </div>

                <div class="status-row">
                    <div class="status-circle circle-blue"></div>
                    <div class="status-info">
                        <div class="status-name">En revisión</div>
                        <div class="status-count">{{ $revisionCount }} pagos</div>
                    </div>
                    <div class="status-money blue">${{ number_format($revisionMonto, 0) }}</div>
                </div>

            </div>

        </div>

    </div>

    <!-- CALENDARIO DE PAGOS -->
    <div class="card" style="margin-top:22px;">
        <div class="card-header">
            <div>
                <div class="card-title">Calendario de pagos · {{ ucfirst($calendarioMes->locale('es')->monthName) }} {{ $calendarioMes->year }}</div>
                <div class="card-description">Consulta pagos anteriores, próximos y vencidos por fecha.</div>
            </div>
            <div style="display:flex;align-items:center;gap:8px;">
                <a href="{{ route('pagos.index', ['mes' => $calendarioMes->copy()->subMonthNoOverflow()->format('Y-m')]) }}" class="btn btn-outline btn-sm" aria-label="Mes anterior">←</a>
                <a href="{{ route('pagos.index', ['mes' => now()->format('Y-m')]) }}" class="btn btn-outline btn-sm">Mes actual</a>
                <a href="{{ route('pagos.index', ['mes' => $calendarioMes->copy()->addMonthNoOverflow()->format('Y-m')]) }}" class="btn btn-outline btn-sm" aria-label="Mes siguiente">→</a>
            </div>
        </div>

        <div class="calendar-legend">
            <span><i class="calendar-dot calendar-dot-green"></i> Próximo o pagado</span>
            <span><i class="calendar-dot calendar-dot-yellow"></i> Vencido de 1 a 4 días</span>
            <span><i class="calendar-dot calendar-dot-red"></i> Vencido 5 días o más</span>
        </div>

        <div class="calendar">
            @foreach (['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'] as $dia)
                <div class="calendar-heading">{{ $dia }}</div>
            @endforeach

            @for ($i = 0; $i < $calendarioMes->dayOfWeek; $i++)
                <div class="calendar-day is-empty"></div>
            @endfor

            @for ($dia = 1; $dia <= $calendarioMes->daysInMonth; $dia++)
                @php
                    $fechaCalendario = $calendarioMes->copy()->day($dia);
                    $pagosDelDia = $calendarioPagos->filter(fn ($pago) => $pago->fecha_vencimiento->isSameDay($fechaCalendario));
                    $pagosVisibles = $pagosDelDia->take(2);
                @endphp
                <div class="calendar-day {{ $fechaCalendario->isToday() ? 'is-today' : '' }}">
                    <div class="calendar-number">{{ $dia }}</div>
                    @foreach ($pagosVisibles as $pagoCalendario)
                        @php
                            $diasVencido = $pagoCalendario->estado->value !== 'pagado' && $pagoCalendario->fecha_vencimiento->isPast()
                                ? $pagoCalendario->fecha_vencimiento->diffInDays($hoy)
                                : 0;
                            $colorCalendario = $diasVencido >= 5 ? 'red' : ($diasVencido > 0 ? 'yellow' : 'green');
                        @endphp
                        <div class="calendar-event calendar-event-{{ $colorCalendario }}">
                            <strong>{{ $pagoCalendario->alumno->nombreCompleto() }}</strong>
                            ${{ number_format((float) $pagoCalendario->monto, 0) }}
                            <div class="calendar-event-actions">
                                <a href="{{ route('pagos.edit', $pagoCalendario) }}">Editar</a>
                                @if ($pagoCalendario->estado->value !== 'pagado')
                                    <form action="{{ route('pagos.destroy', $pagoCalendario) }}" method="POST" onsubmit="return confirm('¿Eliminar este pago?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit">Eliminar</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                    @if ($pagosDelDia->isNotEmpty())
                        <button type="button" class="calendar-more" data-dialog-target="pagos-dia-{{ $fechaCalendario->format('Y-m-d') }}">
                            Ver todos ({{ $pagosDelDia->count() }})
                        </button>
                    @endif
                </div>

                @if ($pagosDelDia->isNotEmpty())
                    <dialog id="pagos-dia-{{ $fechaCalendario->format('Y-m-d') }}" class="calendar-payments-dialog">
                        <div class="calendar-payments-dialog-content">
                            <div class="calendar-payments-dialog-header">
                                <div>
                                    <div class="card-title">Pagos del {{ $fechaCalendario->translatedFormat('d \d\e F Y') }}</div>
                                    <div class="card-description">{{ $pagosDelDia->count() }} pagos registrados para este día</div>
                                </div>
                                <button type="button" class="calendar-payments-dialog-close" aria-label="Cerrar">×</button>
                            </div>

                            @foreach ($pagosDelDia as $pagoCalendario)
                                @php
                                    $diasVencido = $pagoCalendario->estado->value !== 'pagado' && $pagoCalendario->fecha_vencimiento->isPast()
                                        ? $pagoCalendario->fecha_vencimiento->diffInDays($hoy)
                                        : 0;
                                    $colorCalendario = $diasVencido >= 5 ? 'red' : ($diasVencido > 0 ? 'yellow' : 'green');
                                @endphp
                                <div class="calendar-event calendar-modal-event calendar-event-{{ $colorCalendario }}">
                                    <strong>{{ $pagoCalendario->alumno->nombreCompleto() }}</strong>
                                    ${{ number_format((float) $pagoCalendario->monto, 0) }}
                                    <div class="calendar-event-actions">
                                        <a href="{{ route('pagos.edit', $pagoCalendario) }}">Editar</a>
                                        @if ($pagoCalendario->estado->value !== 'pagado')
                                            <form action="{{ route('pagos.destroy', $pagoCalendario) }}" method="POST" onsubmit="return confirm('¿Eliminar este pago?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit">Eliminar</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </dialog>
                @endif
            @endfor
        </div>
    </div>


    <!-- ALUMNOS PRÓXIMOS A SU NUEVO PAGO -->
    <div class="card" style="margin-top:22px;">

        <div class="card-header">
            <div>
                <div class="card-title">Alumnos próximos a su nuevo pago</div>
                <div class="card-description">
                    Estimado a partir de su último pago (o de su inscripción, si es su primera mensualidad). {{ $proximosAPagar->count() }} alumno{{ $proximosAPagar->count() === 1 ? '' : 's' }} en los próximos 7 días.
                </div>
            </div>
        </div>

        <div style="display:flex;flex-direction:column;gap:0;margin-top:6px;">
            @forelse ($proximosAPagar as $fila)
                @php($alumno = $fila['alumno'])
                <div class="upcoming-row">
                    <div>
                        <strong>{{ $alumno->nombreCompleto() }}</strong>
                        &nbsp;·&nbsp; {{ $alumno->plan?->nombre ?? 'Sin plan asignado' }}
                        &nbsp;·&nbsp; Próximo pago estimado: {{ $fila['proximaFecha']->translatedFormat('d M Y') }}
                    </div>

                    <div style="display:flex; align-items:center; gap:10px;">
                        <span class="status-pill status-soon">
                            @if ($fila['diasRestantes'] === 0)
                                Vence hoy
                            @elseif ($fila['diasRestantes'] === 1)
                                Vence mañana
                            @else
                                Vence en {{ $fila['diasRestantes'] }} días
                            @endif
                        </span>
                        <a href="{{ route('pagos.registrar', ['alumno' => $alumno->id]) }}" class="btn btn-outline btn-sm">Registrar fecha de pago</a>
                    </div>
                </div>
            @empty
                <div style="text-align:center; color:var(--muted); padding:20px 0;">
                    Ningún alumno tiene su próximo pago estimado en los próximos 7 días.
                </div>
            @endforelse
        </div>

    </div>


    <!-- PRÓXIMOS A VENCER -->
    @if ($proximosVencer->isNotEmpty())
        <div class="card" style="margin-top:22px;">

            <div class="card-header">
                <div>
                    <div class="card-title">Pagos próximos a vencer</div>
                    <div class="card-description">
                        {{ $proximosVencer->count() }} {{ Str::plural('pago', $proximosVencer->count()) }} vencen en los próximos 5 días
                    </div>
                </div>
            </div>

            <div style="display:flex;flex-direction:column;gap:0;margin-top:6px;">
                @foreach ($proximosVencer as $fila)
                    @php($pago = $fila['pago'])
                    <div class="upcoming-row">
                        <div>
                            <strong>{{ $pago->alumno->nombreCompleto() }}</strong>
                            &nbsp;·&nbsp; {{ $pago->concepto->label() }}
                            &nbsp;·&nbsp; ${{ number_format((float) $pago->monto, 0) }}
                        </div>

                        <div style="display:flex; align-items:center; gap:10px;">
                            <span class="status-pill status-soon">
                                @if ($fila['diasRestantes'] === 0)
                                    Vence hoy
                                @elseif ($fila['diasRestantes'] === 1)
                                    Vence mañana
                                @else
                                    Vence en {{ $fila['diasRestantes'] }} días
                                @endif
                            </span>
                            <form action="{{ route('pagos.marcar-pagado', $pago) }}" method="POST" onsubmit="return confirm('¿Marcar este pago como pagado?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-secondary" style="padding:6px 12px;font-size:12px;">Registrar fecha de pago</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    @endif


    <!-- DEUDORES -->
    <div class="card debtors">

        <div class="card-header">
            <div>
                <div class="card-title">Alumnos con saldo vencido</div>
                <div class="card-description">{{ $deudoresPreview->count() }} de {{ $deudoresCount }} alumnos con saldo vencido</div>
            </div>

            <a href="{{ route('pagos.deudores') }}" class="btn btn-outline">Ver todos</a>
        </div>

        <div class="table-container">
            <table>

                <thead>
                    <tr>
                        <th>Alumno</th>
                        <th>Concepto</th>
                        <th>Vencimiento</th>
                        <th>Saldo</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($deudoresPreview as $pago)
                        <tr>

                            <td>
                                <div class="student">
                                    <div class="row-avatar">
                                        {{ mb_strtoupper(mb_substr($pago->alumno->nombre, 0, 1).mb_substr($pago->alumno->apellidos, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="student-name">{{ $pago->alumno->nombreCompleto() }}</div>
                                        <div class="student-detail">
                                            {{ $pago->alumno->nivel ? 'Nivel '.str_pad((string) $pago->alumno->nivel->orden, 2, '0', STR_PAD_LEFT).' · '.$pago->alumno->nivel->nombre : 'Sin nivel' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td>{{ $pago->concepto->label() }}</td>

                            <td>{{ $pago->fecha_vencimiento?->translatedFormat('d M Y') }}</td>

                            <td class="amount">${{ number_format((float) $pago->monto, 0) }}</td>

                            <td>
                                <span class="status-pill status-overdue">VENCIDO</span>
                            </td>

                            <td>
                                <a href="{{ route('pagos.alumno', $pago->alumno) }}" class="btn btn-outline btn-sm">Ver</a>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center;">
                                No hay alumnos con saldo vencido. 🎉
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

    </div>

@endsection

@push('scripts')
<script>
    document.querySelectorAll('[data-dialog-target]').forEach(function (button) {
        button.addEventListener('click', function () {
            document.getElementById(button.dataset.dialogTarget)?.showModal();
        });
    });

    document.querySelectorAll('.calendar-payments-dialog').forEach(function (dialog) {
        dialog.querySelector('.calendar-payments-dialog-close')?.addEventListener('click', function () {
            dialog.close();
        });

        dialog.addEventListener('click', function (event) {
            if (event.target === dialog) {
                dialog.close();
            }
        });
    });
</script>
@endpush
