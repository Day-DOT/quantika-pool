@extends('quantika.instructor.layout')

@section('title', 'Evaluar a '.$alumno->nombreCompleto())
@section('page-title', 'Nueva evaluación')

@section('content')

    <a href="{{ route('instructor.alumnos.show', $alumno) }}" class="section-link" style="display:inline-block; margin-bottom:18px;">
        ← Volver al alumno
    </a>

    <div class="section-header">
        <h3>Nueva evaluación · {{ $alumno->nombreCompleto() }}</h3>
    </div>

    @if ($sinNivel)

        <div class="card empty-state">
            <strong>Este alumno todavía no tiene un nivel asignado</strong>
            Pide a un administrador que le asigne un nivel antes de poder evaluarlo.
        </div>

    @elseif ($criterios->isEmpty())

        <div class="card empty-state">
            <strong>El nivel {{ $alumno->nivel?->nombre }} no tiene criterios de evaluación configurados</strong>
            Pide a un administrador que configure los criterios de este nivel.
        </div>

    @else

        <form method="POST" action="{{ route('instructor.evaluaciones.store', $alumno) }}">
            @csrf

            <div class="card card-pad">

                <div class="grupo-meta" style="margin-bottom:22px; font-size:13px;">
                    Nivel actual: <strong style="color:var(--text);">{{ $alumno->nombreNivelConSubNivel() }}</strong>
                    · {{ $alumno->nivel->categoria }}
                </div>

                @include('quantika.instructor.evaluaciones._criterios', [
                    'criterios' => $criterios,
                    'estados' => $estados,
                    'detallesPorCriterio' => collect(),
                ])

                <div class="field" style="margin-top:24px;">
                    <label>Observaciones generales</label>
                    <textarea name="observaciones" placeholder="Observaciones generales de la evaluación (opcional)">{{ old('observaciones') }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="margin-top:6px;">
                    Guardar evaluación
                </button>

            </div>

        </form>

    @endif

@endsection
