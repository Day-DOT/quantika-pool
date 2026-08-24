@extends('quantika.instructor.layout')

@section('title', 'Evaluación de '.$evaluacion->alumno->nombreCompleto())
@section('page-title', 'Evaluación')

@section('content')

    <a href="{{ route('instructor.alumnos.show', $evaluacion->alumno) }}" class="section-link" style="display:inline-block; margin-bottom:18px;">
        ← Volver al alumno
    </a>

    <div class="section-header">
        <h3>{{ $evaluacion->alumno->nombreCompleto() }} · {{ $evaluacion->nivel?->nombre }}</h3>
        <span class="section-link">Iniciada el {{ $evaluacion->fecha->format('d/m/Y') }}</span>
    </div>

    <div class="card card-pad" style="margin-bottom:24px;">

        <div class="progress-row" style="display:flex; justify-content:space-between; margin-bottom:8px; font-size:11px; font-weight:800;">
            <span>Progreso de esta evaluación</span>
            <span style="color:var(--cyan);">{{ $porcentaje }}%</span>
        </div>

        <div class="progress">
            <span style="width:{{ $porcentaje }}%"></span>
        </div>

    </div>

    <form method="POST" action="{{ route('instructor.evaluaciones.update', $evaluacion) }}">
        @csrf
        @method('PUT')

        <div class="card card-pad">

            @include('quantika.instructor.evaluaciones._criterios', [
                'criterios' => $criterios,
                'estados' => $estados,
                'detallesPorCriterio' => $detallesPorCriterio,
            ])

            <div class="field" style="margin-top:24px;">
                <label>Observaciones generales</label>
                <textarea name="observaciones" placeholder="Observaciones generales de la evaluación (opcional)">{{ old('observaciones', $evaluacion->observaciones) }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary" style="margin-top:6px;">
                Guardar cambios
            </button>

        </div>

    </form>

@endsection
