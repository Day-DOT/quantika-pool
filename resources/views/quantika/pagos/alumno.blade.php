@extends('quantika.super-admin.layout')

@section('title', 'Pagos · ' . $alumno->nombreCompleto())
@section('page-title', 'Pagos de ' . $alumno->nombreCompleto())

@push('styles')
<style>
    .summary { display: grid; grid-template-columns: repeat(2, 1fr); gap: 18px; margin-bottom: 26px; }

    .summary-card {
        background: linear-gradient(145deg, #07394e, #052d40);
        border: 1px solid rgba(69,207,234,.18);
        border-radius: 18px;
        padding: 22px;
    }

    .summary-label { color: #8db1c3; font-size: 13px; margin-bottom: 10px; }
    .summary-value { font-size: 28px; font-weight: 800; }
    .summary-value.green { color: #13e3a2; }
    .summary-value.red { color: #ff5f6d; }

    .panel-header {
        padding: 20px 24px;
        border-bottom: 1px solid rgba(255,255,255,.08);
        font-size: 18px;
        font-weight: 800;
    }

    .status-pill {
        display: inline-flex;
        padding: 6px 12px;
        border-radius: 30px;
        font-size: 11px;
        font-weight: 800;
    }

    .status-pagado { background: rgba(19,227,162,.10); color: #13e3a2; border: 1px solid rgba(19,227,162,.30); }
    .status-pendiente { background: rgba(255,195,41,.10); color: #ffc329; border: 1px solid rgba(255,195,41,.30); }
    .status-vencido { background: rgba(255,95,109,.10); color: #ff5f6d; border: 1px solid rgba(255,95,109,.30); }
    .status-en_revision { background: rgba(66,212,235,.10); color: #42d4eb; border: 1px solid rgba(66,212,235,.30); }

    .btn-secondary { border: 1px solid rgba(66,213,238,.40); color: white; background: rgba(2,29,43,.25); }
    .btn-secondary:hover { background: rgba(66,213,238,.10); }

    @media (max-width: 700px) {
        .summary { grid-template-columns: 1fr; }
        table { min-width: 700px; }
        .panel { overflow-x: auto; }
    }
</style>
@endpush

@section('content')

    <a href="{{ route('alumnos.show', $alumno) }}" class="breadcrumb-back">
        ← Volver al perfil del alumno
    </a>

    <div class="section-header" style="margin-top:0;">
        <div>
            <p style="color:var(--muted); font-size:14px;">
                {{ $alumno->nivel?->nombre ?? 'Sin nivel' }} · {{ $alumno->sucursal->nombre }} · Tutor: {{ $alumno->nombreTutor() ?? 'Sin tutor' }}
            </p>
        </div>

        <a href="{{ route('pagos.registrar', ['alumno' => $alumno->id]) }}" class="btn btn-primary">
            + Registrar pago
        </a>
    </div>

    <div class="summary">

        <div class="summary-card">
            <div class="summary-label">TOTAL PAGADO</div>
            <div class="summary-value green">${{ number_format($totalPagado, 2) }}</div>
        </div>

        <div class="summary-card">
            <div class="summary-label">SALDO PENDIENTE</div>
            <div class="summary-value red">${{ number_format($totalPendiente, 2) }}</div>
        </div>

    </div>

    <div class="panel">

        <div class="panel-header">
            Historial de pagos
        </div>

        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Concepto</th>
                        <th>Periodo</th>
                        <th>Vencimiento</th>
                        <th>Monto</th>
                        <th>Método</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pagos as $pago)
                        <tr>
                            <td>{{ $pago->concepto->label() }}</td>
                            <td>{{ $pago->periodo ?? '—' }}</td>
                            <td>{{ $pago->fecha_vencimiento?->format('d/m/Y') ?? '—' }}</td>
                            <td>${{ number_format((float) $pago->monto, 2) }}</td>
                            <td>{{ $pago->metodo_pago?->label() ?? '—' }}</td>
                            <td>
                                <span class="status-pill status-{{ $pago->estado->value }}">
                                    {{ mb_strtoupper($pago->estado->label()) }}
                                </span>
                            </td>
                            <td>
                                @if ($pago->estado->value !== 'pagado')
                                    <div style="display:flex; gap:8px;">
                                        <form action="{{ route('pagos.marcar-pagado', $pago) }}" method="POST" onsubmit="return confirm('¿Marcar este pago como pagado?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-secondary" style="padding:8px 14px;font-size:12px;">Marcar pagado</button>
                                        </form>
                                        <form action="{{ route('pagos.destroy', $pago) }}" method="POST" onsubmit="return confirm('¿Eliminar este adeudo? Ya no aparecerá como pendiente.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline" style="padding:8px 14px;font-size:12px;color:#ff6b6b;">Eliminar</button>
                                        </form>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center;color:var(--muted);">
                                Este alumno no tiene pagos registrados todavía.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

@endsection
