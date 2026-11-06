@extends('quantika.instructor.layout')

@section('title', 'Mi Agenda')
@section('page-title', 'Mi Agenda')

@push('styles')
<style>
    .agenda-alumnos { margin-top: 10px; padding-top: 10px; border-top: 1px solid rgba(66, 216, 239, .12); display: flex; flex-direction: column; gap: 6px; }
    .agenda-alumno { display: flex; flex-direction: column; gap: 1px; font-size: 12px; line-height: 1.35; }
    .agenda-alumno-nombre { color: #fff; font-weight: 800; }
    .agenda-alumno-datos { color: var(--muted); font-size: 11.5px; }
    .agenda-alumno-cambio { color: var(--yellow); font-size: 11.5px; font-weight: 800; margin-top: -3px; }
    .agenda-alumno-vacio { color: var(--muted); font-size: 12px; }
</style>
@endpush

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
                                <div class="agenda-alumnos">
                                    @forelse ($horario->inscripciones->sortBy(fn ($inscripcion) => $inscripcion->alumno?->nombreCompleto()) as $inscripcion)
                                        @include('quantika.instructor.partials.alumno-agenda', ['alumno' => $inscripcion->alumno])
                                        @foreach ($cambiosSemana[$horario->id]['noVienen']->where('cita.alumno_id', $inscripcion->alumno_id) as $cambio)
                                            <div class="agenda-alumno-cambio">{{ $cambio['motivo'] }}</div>
                                        @endforeach
                                    @empty
                                        <div class="agenda-alumno-vacio">Sin alumnos inscritos.</div>
                                    @endforelse
                                </div>
                                @if ($cambiosSemana[$horario->id]['vienen']->isNotEmpty())
                                    <div class="agenda-alumnos">
                                        <strong style="color:var(--cyan);font-size:12px;">Vienen solo esta semana ({{ \App\Support\ClasesDelAlumno::fechaCorta($cambiosSemana[$horario->id]['fecha']) }}):</strong>
                                        @foreach ($cambiosSemana[$horario->id]['vienen'] as $visita)
                                            @include('quantika.instructor.partials.alumno-agenda', ['alumno' => $visita['cita']->alumno, 'motivo' => $visita['motivo']])
                                        @endforeach
                                    </div>
                                @endif
                                @if ($cambiosSemana[$horario->id]['muestras']->isNotEmpty())
                                    <div style="margin-top:8px;font-size:12px;">
                                        <strong style="color:var(--yellow);">★ Clase muestra ({{ \App\Support\ClasesDelAlumno::fechaCorta($cambiosSemana[$horario->id]['fecha']) }}):</strong>
                                        @foreach ($cambiosSemana[$horario->id]['muestras'] as $muestra)
                                            <div>{{ $muestra->descripcionCorta() }} · Clase muestra</div>
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
