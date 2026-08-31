@extends('quantika.instructor.layout')

@section('title', $alumno->nombreCompleto())
@section('page-title', $alumno->nombreCompleto())

@section('content')

    @php
        $porcentajeActual = $evaluacionNivelActual?->porcentajeAvance() ?? 0.0;
    @endphp

    <a href="{{ route('instructor.alumnos.index') }}" class="section-link" style="display:inline-block; margin-bottom:18px;">
        ← Volver a mis alumnos
    </a>

    <div class="stats-grid cols-4">

        <div class="stat-card">
            <div class="stat-top"><span class="stat-name">Nivel actual</span><div class="stat-icon">◉</div></div>
            <div class="stat-value" style="font-size:18px;">{{ $alumno->nivel?->nombre ?? 'Sin nivel' }}</div>
            <div class="stat-change">{{ $alumno->nivel?->categoria }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-top"><span class="stat-name">Avance en el nivel</span><div class="stat-icon">✓</div></div>
            <div class="stat-value">{{ $porcentajeActual }}%</div>
            <div class="stat-change">{{ $evaluacionNivelActual ? 'Última evaluación: '.$evaluacionNivelActual->fecha->format('d/m/Y') : 'Sin evaluar todavía' }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-top"><span class="stat-name">Evaluaciones registradas</span><div class="stat-icon">▣</div></div>
            <div class="stat-value">{{ $evaluaciones->count() }}</div>
            <div class="stat-change">Historial completo</div>
        </div>

        <div class="stat-card">
            <div class="stat-top"><span class="stat-name">Estado</span><div class="stat-icon">♟</div></div>
            <div class="stat-value" style="font-size:18px;">{{ $alumno->estado?->label() }}</div>
            <div class="stat-change">{{ $alumno->telefono ?? 'Sin teléfono' }}</div>
        </div>

    </div>


    @if ($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    <div class="section-header">
        <h3>Criterios del nivel actual</h3>

        <div style="display:flex; gap:10px;">
            @if ($alumno->nivel_id)
                <a href="{{ route('instructor.evaluaciones.create', $alumno) }}" class="btn btn-primary btn-sm">
                    {{ $evaluacionNivelActual ? 'Continuar evaluación' : 'Evaluar alumno' }} →
                </a>
            @endif

            @if ($siguienteNivel)
                <form method="POST" action="{{ route('instructor.alumnos.promover', $alumno) }}"
                      onsubmit="return confirm('¿Promover a {{ $alumno->nombreCompleto() }} al nivel {{ $siguienteNivel->nombre }}?');">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-sm {{ $puedePromover ? 'btn-primary' : 'btn-outline' }}"
                            {{ $puedePromover ? '' : 'disabled title="Obtén al menos el 80% en la evaluación del nivel actual para poder promoverlo."' }}>
                        Promover a {{ $siguienteNivel->nombre }} →
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if (! $alumno->nivel_id)

        <div class="card empty-state">
            <strong>Este alumno todavía no tiene un nivel asignado</strong>
            Pide a un administrador que le asigne un nivel antes de poder evaluarlo.
        </div>

    @elseif ($criterios->isEmpty())

        <div class="card empty-state">
            <strong>El nivel {{ $alumno->nivel?->nombre }} no tiene criterios de evaluación configurados</strong>
        </div>

    @else

        @php
            $detallesActuales = $evaluacionNivelActual?->detalles->keyBy('criterio_evaluacion_id') ?? collect();
        @endphp

        <div class="card card-pad">

            <div class="progress-row" style="display:flex; justify-content:space-between; margin-bottom:8px; font-size:11px; font-weight:800;">
                <span>Progreso general</span>
                <span style="color:var(--cyan);">{{ $porcentajeActual }}%</span>
            </div>

            <div class="progress" style="margin-bottom:24px;">
                <span style="width:{{ $porcentajeActual }}%"></span>
            </div>

            @foreach ($criterios as $criterio)

                @php
                    $detalle = $detallesActuales->get($criterio->id);
                    $estado = $detalle?->estado ?? \App\Enums\EstadoEvaluacionDetalle::NoIniciado;
                    $claseBadge = match ($estado) {
                        \App\Enums\EstadoEvaluacionDetalle::Logrado => 'badge-green',
                        \App\Enums\EstadoEvaluacionDetalle::EnProceso => 'badge-yellow',
                        default => 'badge-muted',
                    };
                @endphp

                <div class="criterio-row">
                    <div>
                        <div class="criterio-nombre">{{ $criterio->nombre }}</div>
                        @if ($criterio->descripcion)
                            <div class="criterio-desc">{{ $criterio->descripcion }}</div>
                        @endif
                    </div>
                    <div>
                        <span class="badge {{ $claseBadge }}">● {{ $estado->label() }}</span>
                    </div>
                    <div class="criterio-desc">
                        {{ $detalle?->observaciones ?? 'Sin observaciones' }}
                    </div>
                </div>

            @endforeach

        </div>

    @endif


    <div class="section-header">
        <h3>Historial de evaluaciones</h3>
    </div>

    @if ($evaluaciones->isEmpty())

        <div class="card empty-state">
            <strong>Este alumno todavía no tiene evaluaciones registradas</strong>
        </div>

    @else

        <div class="card">
            <div class="table-wrap">
                <table class="quantika-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Nivel</th>
                            <th>Avance</th>
                            <th>Observaciones</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($evaluaciones as $evaluacion)
                            <tr>
                                <td>{{ $evaluacion->fecha->format('d/m/Y') }}</td>
                                <td>{{ $evaluacion->nivel?->nombre }}</td>
                                <td>
                                    <span class="badge badge-cyan">{{ $evaluacion->porcentajeAvance() }}%</span>
                                </td>
                                <td>{{ $evaluacion->observaciones ?? '—' }}</td>
                                <td>
                                    <a href="{{ route('instructor.evaluaciones.edit', $evaluacion) }}" class="btn btn-sm btn-outline">Ver / editar</a>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>

    @endif

@endsection
