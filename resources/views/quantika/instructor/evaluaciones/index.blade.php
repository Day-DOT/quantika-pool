@extends('quantika.instructor.layout')

@section('title', 'Evaluaciones')
@section('page-title', 'Evaluaciones')

@section('content')

    <div class="section-header">
        <h3>Historial de evaluaciones</h3>
        <a href="{{ route('instructor.alumnos.index') }}" class="section-link">Ir a mis alumnos →</a>
    </div>

    @if ($evaluaciones->isEmpty())

        <div class="card empty-state">
            <strong>Todavía no has registrado ninguna evaluación</strong>
            Entra al detalle de un alumno desde "Mis Alumnos" para comenzar a evaluarlo.
        </div>

    @else

        <div class="card">
            <div class="table-wrap">
                <table class="quantika-table">
                    <thead>
                        <tr>
                            <th>Alumno</th>
                            <th>Nivel</th>
                            <th>Fecha</th>
                            <th>Avance</th>
                            <th>Observaciones</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($evaluaciones as $evaluacion)

                            @php
                                $iniciales = collect(preg_split('/\s+/', trim($evaluacion->alumno?->nombreCompleto() ?? '')))
                                    ->filter()
                                    ->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))
                                    ->take(2)
                                    ->implode('');
                                $porcentaje = $evaluacion->porcentajeAvance();
                                $claseBadge = match (true) {
                                    $porcentaje >= 100 => 'badge-green',
                                    $porcentaje > 0 => 'badge-yellow',
                                    default => 'badge-muted',
                                };
                            @endphp

                            <tr>
                                <td>
                                    <div class="person">
                                        <div class="avatar">{{ $iniciales ?: '—' }}</div>
                                        <div class="person-name">{{ $evaluacion->alumno?->nombreCompleto() ?? 'Alumno eliminado' }}</div>
                                    </div>
                                </td>
                                <td>{{ $evaluacion->nivel?->nombre }}</td>
                                <td>{{ $evaluacion->fecha->format('d/m/Y') }}</td>
                                <td><span class="badge {{ $claseBadge }}">{{ $porcentaje }}%</span></td>
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
