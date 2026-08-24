@extends('quantika.instructor.layout')

@section('title', 'Mis Alumnos')
@section('page-title', 'Mis Alumnos')

@section('content')

    <div class="section-header">
        <h3>Alumnos inscritos en mis grupos</h3>
        <span class="section-link">{{ $alumnos->count() }} alumno(s)</span>
    </div>

    @if ($alumnos->isEmpty())

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
                                <td>{{ $alumno->nivel?->nombre ?? 'Sin nivel asignado' }}</td>
                                <td>{{ $alumno->gruposNombres ?: '—' }}</td>
                                <td>
                                    <span class="badge {{ $alumno->estado?->value === 'activo' ? 'badge-green' : 'badge-muted' }}">
                                        ● {{ $alumno->estado?->label() }}
                                    </span>
                                </td>
                                <td>
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
