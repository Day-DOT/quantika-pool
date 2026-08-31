@extends('quantika.instructor.layout')

@section('title', $horario->nombre_grupo)
@section('page-title', $horario->nombre_grupo)

@section('content')

    <a href="{{ route('instructor.agenda') }}" class="section-link" style="display:inline-block; margin-bottom:18px;">
        ← Volver a mi agenda
    </a>

    <div class="stats-grid cols-4">

        <div class="stat-card">
            <div class="stat-top"><span class="stat-name">Nivel</span><div class="stat-icon">◉</div></div>
            <div class="stat-value" style="font-size:18px;">{{ $horario->nivel?->nombre ?? '—' }}</div>
            <div class="stat-change">{{ $horario->nivel?->categoria }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-top"><span class="stat-name">Horario</span><div class="stat-icon">▣</div></div>
            <div class="stat-value" style="font-size:18px;">{{ $horario->dia_semana->label() }}</div>
            <div class="stat-change">{{ substr($horario->hora_inicio, 0, 5) }} – {{ substr($horario->hora_fin, 0, 5) }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-top"><span class="stat-name">Carril / Sucursal</span><div class="stat-icon">⌂</div></div>
            <div class="stat-value" style="font-size:18px;">{{ $horario->carril?->nombre }}</div>
            <div class="stat-change">{{ $horario->sucursal?->nombre }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-top"><span class="stat-name">Alumnos</span><div class="stat-icon">♟</div></div>
            <div class="stat-value">{{ $alumnos->count() }} / {{ $horario->capacidad_maxima }}</div>
            <div class="stat-change">Inscripción vigente</div>
        </div>

    </div>


    <div class="section-header">
        <h3>Lista de asistencia · {{ $hoy->translatedFormat('d/m/Y') }}</h3>
    </div>

    @if ($alumnos->isEmpty())

        <div class="card empty-state">
            <strong>Este grupo todavía no tiene alumnos inscritos</strong>
            Cuando un administrador inscriba alumnos en este horario, aparecerán aquí.
        </div>

    @else

        <div class="card">
            <div class="table-wrap">
                <table class="quantika-table">
                    <thead>
                        <tr>
                            <th>Alumno</th>
                            <th>Nivel</th>
                            <th>Asistencia de hoy</th>
                            <th>Marcar asistencia</th>
                            <th>Evaluación</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($alumnos as $alumno)

                            @php
                                $cita = $citasHoy->get($alumno->id);
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
                                <td>{{ $alumno->nivel?->nombre ?? 'Sin nivel' }}</td>
                                <td>
                                    @if ($cita === null)
                                        <span class="badge badge-muted">Sin registrar</span>
                                    @elseif ($cita->asistio)
                                        <span class="badge badge-green">● Asistió</span>
                                    @else
                                        <span class="badge badge-red">● No asistió</span>
                                    @endif
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('instructor.grupos.asistencia', [$horario, $alumno]) }}" style="display:flex; gap:8px;">
                                        @csrf
                                        <button type="submit" name="asistio" value="1" class="btn btn-sm btn-yes">Sí</button>
                                        <button type="submit" name="asistio" value="0" class="btn btn-sm btn-no">No</button>
                                    </form>
                                </td>
                                <td>
                                    @if ($alumno->nivel_id)
                                        <a href="{{ route('instructor.evaluaciones.create', $alumno) }}" class="btn btn-sm btn-outline">Evaluar</a>
                                    @else
                                        <span class="badge badge-muted">Sin nivel</span>
                                    @endif
                                </td>
                            </tr>

                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>

    @endif

@endsection
