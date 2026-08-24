@extends('quantika.super-admin.layout')

@section('title', 'Evaluaciones · ' . ($instructor->user?->name ?? 'Instructor'))
@section('page-title', $instructor->user?->name ?? 'Instructor')

@push('styles')
<style>
    .title-area p {
        color: #79aabd;
        font-size: 15px;
        margin-bottom: 20px;
    }

    .stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 18px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: linear-gradient(145deg, rgba(10,65,86,.95), rgba(4,38,54,.96));
        border: 1px solid rgba(55,190,220,.18);
        border-radius: 18px;
        padding: 20px;
    }

    .stat-title {
        color: #79aabd;
        font-size: 13px;
        margin-bottom: 10px;
    }

    .stat-number {
        font-size: 28px;
        font-weight: 800;
    }

    .panel {
        background: rgba(5, 45, 62, .9);
        border: 1px solid rgba(65, 208, 235, .18);
        border-radius: 20px;
        overflow: hidden;
    }

    .panel-header {
        padding: 20px 24px;
        border-bottom: 1px solid rgba(255,255,255,.08);
        font-size: 18px;
        font-weight: 800;
    }

    .panel table {
        width: 100%;
        border-collapse: collapse;
    }

    .panel th {
        text-align: left;
        padding: 14px 22px;
        font-size: 11px;
        color: #72a5b8;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .panel td {
        padding: 16px 22px;
        border-top: 1px solid rgba(255,255,255,.06);
        color: #d8e8ee;
    }

    /* Renombrado a .row-avatar para no chocar con .avatar del sidebar/topbar del layout */
    .row-avatar {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #40d5ed, #21aeca);
        color: #043043;
        font-weight: 900;
        margin-right: 10px;
    }

    .pill {
        display: inline-flex;
        padding: 6px 12px;
        border-radius: 30px;
        font-size: 12px;
        font-weight: 700;
    }

    .pill.ok {
        background: rgba(19,227,162,.10);
        color: #13e3a2;
        border: 1px solid rgba(19,227,162,.30);
    }

    .pill.pending {
        background: rgba(255,195,41,.10);
        color: #ffc329;
        border: 1px solid rgba(255,195,41,.30);
    }

    .link {
        color: #42d4ee;
        font-weight: 700;
        text-decoration: none;
    }

    @media (max-width: 800px) {
        .stats {
            grid-template-columns: 1fr;
        }

        .panel table {
            min-width: 700px;
        }

        .panel {
            overflow-x: auto;
        }
    }
</style>
@endpush

@section('content')

    <a href="{{ route('evaluaciones.index') }}" class="section-link" style="display:inline-flex;align-items:center;gap:8px;margin-bottom:18px;">
        ← Volver a evaluaciones
    </a>

    <div class="top" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:30px;gap:20px;">

        <div class="title-area">
            <p>{{ $instructor->especialidad ?? 'Instructor de natación' }} · {{ $instructor->sucursal?->nombre }}</p>
        </div>

    </div>

    <div class="stats">

        <div class="stat-card">
            <div class="stat-title">Alumnos asignados</div>
            <div class="stat-number">{{ $alumnos->count() }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-title">Evaluados</div>
            <div class="stat-number">{{ $totalEvaluados }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-title">Pendientes de evaluar</div>
            <div class="stat-number">{{ $alumnos->count() - $totalEvaluados }}</div>
        </div>

    </div>

    <div class="panel">

        <div class="panel-header">
            Alumnos asignados
        </div>

        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>Alumno</th>
                        <th>Nivel</th>
                        <th>Última evaluación</th>
                        <th>Progreso</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($alumnos as $fila)
                        @php($alumno = $fila['alumno'])
                        <tr>
                            <td>
                                <span class="row-avatar">{{ mb_strtoupper(mb_substr($alumno->nombre, 0, 1).mb_substr($alumno->apellidos, 0, 1)) }}</span>
                                {{ $alumno->nombreCompleto() }}
                            </td>
                            <td>{{ $alumno->nivel?->nombre ?? 'Sin nivel' }}</td>
                            <td>{{ $fila['ultimaEvaluacion']?->fecha?->format('d/m/Y') ?? '—' }}</td>
                            <td>{{ $fila['porcentaje'] !== null ? $fila['porcentaje'].'%' : '—' }}</td>
                            <td>
                                @if ($fila['ultimaEvaluacion'])
                                    <span class="pill ok">Evaluado</span>
                                @else
                                    <span class="pill pending">Pendiente</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('evaluaciones.alumno', $alumno) }}" class="link">Ver detalle →</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center;color:#79aabd;">
                                Este instructor no tiene alumnos asignados actualmente.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

@endsection
