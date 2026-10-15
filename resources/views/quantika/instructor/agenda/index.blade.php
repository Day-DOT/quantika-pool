@extends('quantika.instructor.layout')

@section('title', 'Mi Agenda')
@section('page-title', 'Mi Agenda')

@section('content')

    @php
        $diaHoyValor = today()->dayOfWeekIso;
        $hayGrupos = $horariosPorDia->flatten(1)->isNotEmpty();
    @endphp

    <div class="section-header">
        <h3>Agenda semanal</h3>
        <span class="section-link">{{ $horariosPorDia->flatten(1)->count() }} grupo(s) en total</span>
    </div>

    @unless ($hayGrupos)

        <div class="card empty-state">
            <strong>Todavía no tienes grupos asignados</strong>
            Cuando un administrador te asigne horarios, aparecerán aquí organizados por día de la semana.
        </div>

    @else

        @foreach (\App\Enums\DiaSemana::cases() as $dia)

            @php
                $horariosDelDia = $horariosPorDia->get($dia->value, collect());
            @endphp

            <div class="dia-block">

                <div class="dia-header {{ $dia->value === $diaHoyValor ? 'today' : '' }}">
                    <h4>{{ $dia->label() }}{{ $dia->value === $diaHoyValor ? ' · Hoy' : '' }}</h4>
                    <span class="count">{{ $horariosDelDia->count() }} grupo(s)</span>
                </div>

                @if ($horariosDelDia->isEmpty())

                    <div class="card empty-state" style="padding:24px;">
                        Sin clases este día.
                    </div>

                @else

                    <div class="grupos-grid">

                        @foreach ($horariosDelDia as $horario)

                            <div class="grupo-card">
                                <a href="{{ route('instructor.grupos.show', $horario) }}">
                                <div class="grupo-nombre">{{ $horario->nombre_grupo }}</div>
                                <div class="grupo-meta">{{ $horario->nivel?->nombre }} · Carril {{ $horario->carril?->nombre }}</div>
                                <div class="grupo-meta">{{ $horario->sucursal?->nombre }}</div>
                                <div class="grupo-meta">{{ substr($horario->hora_inicio, 0, 5) }} – {{ substr($horario->hora_fin, 0, 5) }}</div>
                                <div class="grupo-meta">{{ $alumnosPorHorario->get($horario->id, 0) }} / {{ $horario->capacidad_maxima }} alumno(s)</div>
                                </a>
                                <details style="margin-top:10px;">
                                    <summary style="cursor:pointer;color:var(--cyan);font-size:12px;">Ver alumnos inscritos</summary>
                                    <div style="margin-top:7px;font-size:12px;">
                                        @forelse ($horario->inscripciones as $inscripcion)
                                            <div>
                                                {{ $inscripcion->alumno?->nombreCompleto() }}
                                                @foreach ($cambiosSemana[$horario->id]['noVienen']->where('cita.alumno_id', $inscripcion->alumno_id) as $cambio)
                                                    <span style="color:var(--yellow);font-weight:800;">· {{ $cambio['motivo'] }}</span>
                                                @endforeach
                                            </div>
                                        @empty
                                            <div>Sin alumnos inscritos.</div>
                                        @endforelse
                                    </div>
                                </details>
                                @if ($cambiosSemana[$horario->id]['vienen']->isNotEmpty())
                                    <div style="margin-top:8px;font-size:12px;">
                                        <strong style="color:var(--cyan);">Vienen solo esta semana ({{ \App\Support\ClasesDelAlumno::fechaCorta($cambiosSemana[$horario->id]['fecha']) }}):</strong>
                                        @foreach ($cambiosSemana[$horario->id]['vienen'] as $visita)
                                            <div>{{ $visita['cita']->alumno?->nombreCompleto() }} · {{ $visita['motivo'] }}</div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                        @endforeach

                    </div>

                @endif

            </div>

        @endforeach

    @endunless

@endsection
