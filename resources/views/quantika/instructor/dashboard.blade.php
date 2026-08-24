@extends('quantika.instructor.layout')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

    @if ($sinPerfil)

        <div class="card empty-state">
            <strong>Tu perfil de instructor aún no ha sido configurado</strong>
            Contacta a un administrador de tu sucursal para que te asigne como instructor.
            En cuanto tengas un perfil asignado, aquí verás tus grupos, tu agenda y tus alumnos.
        </div>

    @else

        {{-- ESTADÍSTICAS --}}
        <section class="stats-grid">

            <div class="stat-card">
                <div class="stat-top">
                    <span class="stat-name">Grupos activos</span>
                    <div class="stat-icon">▣</div>
                </div>
                <div class="stat-value">{{ $stats['grupos'] }}</div>
                <div class="stat-change">A tu cargo</div>
            </div>

            <div class="stat-card">
                <div class="stat-top">
                    <span class="stat-name">Alumnos</span>
                    <div class="stat-icon">♟</div>
                </div>
                <div class="stat-value">{{ $stats['alumnos'] }}</div>
                <div class="stat-change">Inscritos vigentes</div>
            </div>

            <div class="stat-card">
                <div class="stat-top">
                    <span class="stat-name">Clases de hoy</span>
                    <div class="stat-icon">≋</div>
                </div>
                <div class="stat-value">{{ $stats['clasesHoy'] }}</div>
                <div class="stat-change">{{ $diaHoy->label() }}</div>
            </div>

            <div class="stat-card">
                <div class="stat-top">
                    <span class="stat-name">Evaluaciones pendientes</span>
                    <div class="stat-icon">✓</div>
                </div>
                <div class="stat-value">{{ $stats['pendientesEvaluacion'] }}</div>
                <div class="stat-change">En el nivel actual</div>
            </div>

        </section>


        {{-- CLASES DE HOY --}}
        <section>

            <div class="section-header">
                <h3>Clases de hoy · {{ ucfirst($hoy->translatedFormat('l d \d\e F')) }}</h3>
                <a href="{{ route('instructor.agenda') }}" class="section-link">Ver mi agenda completa →</a>
            </div>

            @if ($horariosHoy->isEmpty())

                <div class="card empty-state">
                    <strong>No tienes clases programadas para hoy</strong>
                    Aprovecha para revisar tus evaluaciones pendientes o consultar a tus alumnos.
                </div>

            @else

                <div class="grupos-grid">

                    @foreach ($horariosHoy as $horario)

                        @php
                            $citas = $citasHoy->get($horario->id, collect());
                            $totalAlumnos = $alumnosPorHorario->get($horario->id, 0);
                            $registradas = $citas->count();
                            $completa = $totalAlumnos > 0 && $registradas >= $totalAlumnos;
                        @endphp

                        <a href="{{ route('instructor.grupos.show', $horario) }}" class="grupo-card">
                            <div class="grupo-nombre">{{ $horario->nombre_grupo }}</div>
                            <div class="grupo-meta">{{ $horario->nivel?->nombre }} · Carril {{ $horario->carril?->nombre }}</div>
                            <div class="grupo-meta">{{ substr($horario->hora_inicio, 0, 5) }} – {{ substr($horario->hora_fin, 0, 5) }}</div>
                            <div class="grupo-meta">{{ $totalAlumnos }} alumno(s) · {{ $registradas }} asistencia(s) registrada(s)</div>
                            <span class="badge {{ $completa ? 'badge-green' : 'badge-yellow' }}" style="margin-top:10px;">
                                {{ $completa ? '● Asistencia completa' : '● Pendiente de asistencia' }}
                            </span>
                        </a>

                    @endforeach

                </div>

            @endif

        </section>


        {{-- AGENDA SEMANAL (RESUMEN) --}}
        <section>

            <div class="section-header">
                <h3>Mis grupos por día</h3>
                <a href="{{ route('instructor.agenda') }}" class="section-link">Ver agenda completa →</a>
            </div>

            @php
                $hayGrupos = false;
            @endphp

            @foreach (\App\Enums\DiaSemana::cases() as $dia)

                @php
                    $horariosDelDia = $horariosPorDia->get($dia->value, collect());
                @endphp

                @continue($horariosDelDia->isEmpty())
                @php
                    $hayGrupos = true;
                @endphp

                <div class="dia-block">

                    <div class="dia-header {{ $dia->value === $diaHoy->value ? 'today' : '' }}">
                        <h4>{{ $dia->label() }}</h4>
                        <span class="count">{{ $horariosDelDia->count() }} grupo(s)</span>
                    </div>

                    <div class="grupos-grid">

                        @foreach ($horariosDelDia as $horario)
                            <a href="{{ route('instructor.grupos.show', $horario) }}" class="grupo-card">
                                <div class="grupo-nombre">{{ $horario->nombre_grupo }}</div>
                                <div class="grupo-meta">{{ $horario->nivel?->nombre }}</div>
                                <div class="grupo-meta">{{ substr($horario->hora_inicio, 0, 5) }} – {{ substr($horario->hora_fin, 0, 5) }}</div>
                                <div class="grupo-meta">{{ $alumnosPorHorario->get($horario->id, 0) }} alumno(s)</div>
                            </a>
                        @endforeach

                    </div>

                </div>

            @endforeach

            @unless ($hayGrupos)
                <div class="card empty-state">
                    <strong>Todavía no tienes grupos asignados</strong>
                    Cuando un administrador te asigne horarios, aparecerán aquí organizados por día.
                </div>
            @endunless

        </section>

    @endif

@endsection
