@extends('quantika.instructor.layout')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
<style>
    .levels-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 17px;
        margin-top: 20px;
    }

    .level-card {
        position: relative;
        min-height: 210px;
        overflow: hidden;
        padding: 20px;
        border-radius: 21px;
        background: linear-gradient(145deg, rgba(7,54,74,.98), rgba(3,35,51,.98));
        border: 1px solid rgba(66,213,238,.17);
        transition: .25s ease;
    }

    .level-card:hover {
        transform: translateY(-5px);
        border-color: rgba(66,213,238,.45);
    }

    .level-head {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .animal-circle {
        width: 70px;
        height: 70px;
        min-width: 70px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(2,27,41,.88);
        border: 2px solid var(--level-color);
        box-shadow: 0 0 18px color-mix(in srgb, var(--level-color) 30%, transparent);
    }

    .animal-circle img {
        width: 49px;
        height: 49px;
        object-fit: contain;
    }

    .level-number {
        color: var(--level-color);
        font-size: 10px;
        font-weight: 950;
        letter-spacing: 1.5px;
        margin-bottom: 4px;
    }

    .level-name {
        font-size: 19px;
        font-weight: 950;
        margin-bottom: 3px;
    }

    .level-description {
        color: #79a5b7;
        font-size: 11px;
    }

    .progress-info {
        display: flex;
        justify-content: space-between;
        margin-top: 24px;
        margin-bottom: 8px;
        font-size: 10px;
        font-weight: 900;
    }

    .progress-info span:last-child {
        color: var(--level-color);
    }

    .progress {
        width: 100%;
        height: 7px;
        border-radius: 20px;
        background: rgba(255,255,255,.07);
        overflow: hidden;
    }

    .progress span {
        display: block;
        height: 100%;
        width: var(--progress);
        border-radius: inherit;
        background: var(--level-color);
        box-shadow: 0 0 12px color-mix(in srgb, var(--level-color) 40%, transparent);
    }

    @media (max-width: 1200px) {
        .levels-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (max-width: 700px) {
        .levels-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

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
        <section class="section">

            <div class="section-header">
                <h3>Progreso de mis alumnos</h3>
            </div>

            @if ($nivelesPreview->isEmpty())
                <div class="card empty-state">
                    <strong>Aún no hay alumnos evaluados en tus grupos</strong>
                    Cuando tengas estudiantes con nivel asignado, aquí verás su avance por nivel.
                </div>
            @else
                <div class="levels-grid">
                    @foreach ($nivelesPreview as $fila)
                        @php($nivel = $fila['nivel'])
                        <article class="level-card" style="--level-color:{{ $nivel->color_hex }}; --progress:{{ $fila['progreso'] }}%;">
                            <div class="level-head">
                                <div class="animal-circle">
                                    <img src="{{ asset($nivel->imagen) }}" alt="{{ $nivel->nombre }}">
                                </div>
                                <div>
                                    <div class="level-number">NIVEL {{ str_pad((string) $nivel->orden, 2, '0', STR_PAD_LEFT) }}</div>
                                    <div class="level-name">{{ $nivel->nombre }}</div>
                                    <div class="level-description">{{ \App\Models\ConfiguracionSistema::categoriasEdad()[$nivel->categoria_edad] ?? $nivel->categoria_edad }}</div>
                                </div>
                            </div>

                            <div class="progress-info">
                                <span>Progreso</span>
                                <span>{{ $fila['progreso'] }}%</span>
                            </div>

                            <div class="progress">
                                <span></span>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif

        </section>

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
