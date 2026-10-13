@extends('quantika.instructor.layout')

@section('title', 'Mis Alumnos')
@section('page-title', 'Mis Alumnos')

@section('content')

    <div class="section-header">
        <h3>{{ $soloPendientes ? 'Alumnos pendientes de evaluar' : 'Alumnos inscritos en mis grupos' }}</h3>
        <span class="section-link">{{ $alumnos->count() }} alumno(s)</span>
    </div>

    <div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:18px;">
        <a href="{{ route('instructor.alumnos.index') }}" class="btn btn-sm {{ $soloPendientes ? 'btn-outline' : 'btn-primary' }}">Todos mis alumnos</a>
        <a href="{{ route('instructor.alumnos.index', ['evaluacion' => 'pendientes']) }}" class="btn btn-sm {{ $soloPendientes ? 'btn-primary' : 'btn-outline' }}">
            Pendientes de evaluar ({{ $pendientesIds->count() }})
        </a>
    </div>

    @if ($alumnos->isEmpty() && $soloPendientes)

        <div class="card empty-state">
            <strong>No tienes evaluaciones pendientes</strong>
            Ya evaluaste a todos tus alumnos en su nivel actual.
        </div>

    @elseif ($alumnos->isEmpty())

        <div class="card empty-state">
            <strong>Todavía no tienes alumnos asignados</strong>
            En cuanto tengas grupos con inscripciones vigentes, tus alumnos aparecerán aquí.
        </div>

    @else

        <div class="card">
            <div class="table-wrap">
                <table class="quantika-table">
                    <thead>
                        <tr>
                            <th>Alumno</th>
                            <th>Nivel actual</th>
                            <th>Grupo(s)</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($alumnos as $alumno)

                            @php
                                $iniciales = collect(preg_split('/\s+/', trim($alumno->nombreCompleto())))
                                    ->filter()
                                    ->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))
                                    ->take(2)
                                    ->implode('');
                            @endphp

                            <tr>
                                <td>
                                    <div class="person">
                                        <div class="avatar">{{ $iniciales ?: '—' }}</div>
                                        <div>
                                            <div class="person-name">{{ $alumno->nombreCompleto() }}</div>
                                            <div class="person-sub">{{ $alumno->telefono ?? 'Sin teléfono' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    {{ $alumno->nivel?->nombre ?? 'Sin nivel asignado' }}
                                    @if ($pendientesIds->contains($alumno->id))
                                        <div><span class="badge badge-yellow">Pendiente de evaluar</span></div>
                                    @endif
                                </td>
                                <td>{{ $alumno->gruposNombres ?: '—' }}</td>
                                <td>
                                    <span class="badge {{ $alumno->estado?->value === 'activo' ? 'badge-green' : 'badge-muted' }}">
                                        ● {{ $alumno->estado?->label() }}
                                    </span>
                                </td>
                                <td>
                                    @if ($pendientesIds->contains($alumno->id))
                                        <a href="{{ route('instructor.evaluaciones.create', $alumno) }}" class="btn btn-sm btn-primary">Evaluar</a>
                                    @endif
                                    <a href="{{ route('instructor.alumnos.show', $alumno) }}" class="btn btn-sm btn-outline">Ver detalle</a>
                                </td>
                            </tr>

                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>

    @endif

@endsection
