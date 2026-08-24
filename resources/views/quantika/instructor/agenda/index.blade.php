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

                            <a href="{{ route('instructor.grupos.show', $horario) }}" class="grupo-card">
                                <div class="grupo-nombre">{{ $horario->nombre_grupo }}</div>
                                <div class="grupo-meta">{{ $horario->nivel?->nombre }} · Carril {{ $horario->carril?->nombre }}</div>
                                <div class="grupo-meta">{{ $horario->sucursal?->nombre }}</div>
                                <div class="grupo-meta">{{ substr($horario->hora_inicio, 0, 5) }} – {{ substr($horario->hora_fin, 0, 5) }}</div>
                                <div class="grupo-meta">{{ $alumnosPorHorario->get($horario->id, 0) }} / {{ $horario->capacidad_maxima }} alumno(s)</div>
                            </a>

                        @endforeach

                    </div>

                @endif

            </div>

        @endforeach

    @endunless

@endsection
