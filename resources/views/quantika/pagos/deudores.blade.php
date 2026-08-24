@extends('quantika.super-admin.layout')

@section('title', 'Deudores')
@section('page-title', 'Alumnos con saldo vencido')

@push('styles')
<style>
    .summary-card {
        display: inline-flex;
        align-items: center;
        gap: 14px;
        background: linear-gradient(145deg, #07394e, #052d40);
        border: 1px solid rgba(255,95,109,.25);
        border-radius: 18px;
        padding: 20px 26px;
        margin-bottom: 26px;
    }

    .summary-card strong { font-size: 26px; color: #ff5f6d; }
    .summary-card span { color: #8db1c3; font-size: 13px; }

    .student { display: flex; align-items: center; gap: 12px; }

    .row-avatar {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: #42d4eb;
        color: #063044;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
    }

    .student-name { font-weight: 800; }
    .student-detail { color: #72a5b8; font-size: 12px; margin-top: 2px; }

    .status-pill {
        display: inline-flex;
        padding: 6px 12px;
        border-radius: 30px;
        font-size: 11px;
        font-weight: 800;
        background: rgba(255,95,109,.10);
        color: #ff5f6d;
        border: 1px solid rgba(255,95,109,.30);
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 36px;
        padding: 0 14px;
        border-radius: 11px;
        font-weight: 900;
        font-size: 12px;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: .2s ease;
    }

    .btn-secondary { border: 1px solid rgba(66,213,238,.40); color: white; background: rgba(2,29,43,.25); }
    .btn-secondary:hover { background: rgba(66,213,238,.10); }

    @media (max-width: 800px) {
        .data-table { min-width: 900px; }
        .panel { overflow-x: auto; }
    }
</style>
@endpush

@section('content')

    <a href="{{ route('pagos.index') }}" class="breadcrumb-back">
        ← Volver a pagos
    </a>

    <p style="color:var(--muted); font-size:15px; margin-bottom:20px;">
        Pagos pendientes cuya fecha de vencimiento ya pasó.
    </p>

    <div class="summary-card">
        <strong>${{ number_format($totalVencido, 2) }}</strong>
        <span>en {{ $deudores->count() }} pago{{ $deudores->count() === 1 ? '' : 's' }} vencido{{ $deudores->count() === 1 ? '' : 's' }}</span>
    </div>

    <div class="panel">

        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Alumno</th>
                        <th>Sucursal</th>
                        <th>Concepto</th>
                        <th>Periodo</th>
                        <th>Vencimiento</th>
                        <th>Días vencido</th>
                        <th>Monto</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($deudores as $pago)
                        <tr>
                            <td>
                                <div class="student">
                                    <div class="row-avatar">
                                        {{ mb_strtoupper(mb_substr($pago->alumno->nombre, 0, 1).mb_substr($pago->alumno->apellidos, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="student-name">{{ $pago->alumno->nombreCompleto() }}</div>
                                        <div class="student-detail">{{ $pago->alumno->nivel?->nombre ?? 'Sin nivel' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $pago->alumno->sucursal->nombre }}</td>
                            <td>{{ $pago->concepto->label() }}</td>
                            <td>{{ $pago->periodo ?? '—' }}</td>
                            <td>{{ $pago->fecha_vencimiento?->format('d/m/Y') }}</td>
                            <td>{{ $pago->fecha_vencimiento?->diffInDays(now()) }} días</td>
                            <td>${{ number_format((float) $pago->monto, 2) }}</td>
                            <td>
                                <div style="display:flex;gap:8px;">
                                    <a href="{{ route('pagos.alumno', $pago->alumno) }}" class="btn btn-secondary">Ver</a>
                                    <form action="{{ route('pagos.marcar-pagado', $pago) }}" method="POST" onsubmit="return confirm('¿Marcar este pago como pagado?');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-secondary">Marcar pagado</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="empty-state">
                                No hay alumnos con saldo vencido. 🎉
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

@endsection
