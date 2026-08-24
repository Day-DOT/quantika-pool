@extends('quantika.super-admin.layout')

@section('title', 'Evaluaciones · ' . $alumno->nombreCompleto())
@section('page-title', $alumno->nombreCompleto())

@push('styles')
<style>
    .title-area p {
        color: #79aabd;
        font-size: 15px;
        margin-bottom: 20px;
    }

    .eval-card {
        background: linear-gradient(145deg, rgba(10,65,86,.95), rgba(4,38,54,.96));
        border: 1px solid rgba(55,190,220,.18);
        border-radius: 20px;
        padding: 24px;
        margin-bottom: 22px;
    }

    .eval-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 18px;
        flex-wrap: wrap;
        gap: 10px;
    }

    .eval-header h3 {
        font-size: 18px;
    }

    .eval-header span {
        color: #79aabd;
        font-size: 13px;
    }

    .progress-pct {
        font-size: 22px;
        font-weight: 900;
        color: #42d4ee;
    }

    .criterios {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 12px;
    }

    .criterio {
        background: rgba(0,25,38,.35);
        border: 1px solid rgba(255,255,255,.06);
        border-radius: 14px;
        padding: 13px;
    }

    .criterio strong {
        display: block;
        margin-bottom: 6px;
        font-size: 13px;
    }

    .pill {
        display: inline-flex;
        padding: 5px 11px;
        border-radius: 30px;
        font-size: 11px;
        font-weight: 700;
    }

    .pill.logrado {
        background: rgba(19,227,162,.10);
        color: #13e3a2;
        border: 1px solid rgba(19,227,162,.30);
    }

    .pill.en_proceso {
        background: rgba(255,195,41,.10);
        color: #ffc329;
        border: 1px solid rgba(255,195,41,.30);
    }

    .pill.no_iniciado {
        background: rgba(255,95,109,.10);
        color: #ff5f6d;
        border: 1px solid rgba(255,95,109,.30);
    }

    .eval-card.empty {
        color: #79aabd;
        padding: 30px;
        text-align: center;
    }
</style>
@endpush

@section('content')

    <a href="{{ route('evaluaciones.index') }}" class="section-link" style="display:inline-flex;align-items:center;gap:8px;margin-bottom:18px;">
        ← Volver a evaluaciones
    </a>

    <div class="top" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:30px;gap:20px;">

        <div class="title-area">
            <p>
                {{ $alumno->nivel?->nombre ?? 'Sin nivel asignado' }} ·
                {{ $alumno->sucursal->nombre }} ·
                Tutor: {{ $alumno->tutorUser?->name ?? 'Sin tutor' }}
            </p>
        </div>

    </div>

    @forelse ($evaluaciones as $fila)
        @php($evaluacion = $fila['evaluacion'])
        <div class="eval-card">

            <div class="eval-header">
                <div>
                    <h3>{{ $evaluacion->nivel?->nombre ?? 'Nivel' }}</h3>
                    <span>
                        {{ $evaluacion->fecha?->format('d/m/Y') }} ·
                        Evaluado por {{ $evaluacion->instructor?->user?->name ?? 'instructor' }}
                    </span>
                </div>

                <div class="progress-pct">{{ $fila['porcentaje'] }}%</div>
            </div>

            <div class="criterios">
                @foreach ($evaluacion->detalles as $detalle)
                    <div class="criterio">
                        <strong>{{ $detalle->criterio?->nombre }}</strong>
                        <span class="pill {{ $detalle->estado->value }}">
                            {{ $detalle->estado->label() }}
                        </span>
                    </div>
                @endforeach
            </div>

            @if ($evaluacion->observaciones)
                <p style="margin-top:16px;color:#c7dde6;font-size:13px;">
                    {{ $evaluacion->observaciones }}
                </p>
            @endif

        </div>
    @empty
        <div class="eval-card empty">
            Este alumno todavía no tiene evaluaciones registradas.
        </div>
    @endforelse

@endsection
