<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservar clase · QUANTIKA POOL</title>
    @include('quantika.portal.partials.styles')
</head>
<body>

<div class="quantika-app">

    @include('quantika.portal.partials.sidebar', ['activo' => 'reservar'])

    <main class="main">

        @include('quantika.portal.partials.topbar', [
            'titulo' => 'Reservar clase',
            'alumnos' => $alumnos,
            'alumno' => $alumno,
        ])

        <div class="content">

            @if (! $alumno)

                <div class="empty-state">
                    <h3>Aún no tienes alumnos registrados</h3>
                    <p>No es posible reservar clases hasta que la escuela vincule un alumno a tu cuenta.</p>
                </div>

            @elseif ($sucursales->isEmpty())

                <div class="empty-state">
                    <h3>No hay sucursales disponibles</h3>
                    <p>Contacta a la escuela para más información.</p>
                </div>

            @else

                <div class="section-header">
                    <h3>Reservar clase para {{ $alumno->nombreCompleto() }}</h3>
                </div>

                <form method="GET" action="{{ route('portal.reservar.index') }}" style="max-width:340px; margin-bottom:26px;">
                    <input type="hidden" name="alumno" value="{{ $alumno->id }}">
                    <label class="field-label" for="sucursal">Sucursal</label>
                    <select name="sucursal" id="sucursal" class="field-select" onchange="this.form.submit()">
                        @foreach ($sucursales as $sucursal)
                            <option value="{{ $sucursal->id }}" @selected($sucursalId === $sucursal->id)>{{ $sucursal->nombre }}</option>
                        @endforeach
                    </select>
                </form>

                <p style="color:var(--muted); font-size:12px; margin-bottom:20px; max-width:640px;">
                    Se muestran los grupos del nivel actual de {{ $alumno->nombreCompleto() }}
                    (<strong style="color:var(--text);">{{ $alumno->nivel?->nombre ?? 'sin nivel asignado' }}</strong>)
                    y de los niveles más cercanos, con su cupo disponible en tiempo real.
                </p>

                <div class="schedule-grid">

                    @forelse ($horarios as $horario)
                        @php
                            $cupoClase = match (true) {
                                $horario->cupo_disponible <= 0 => 'full',
                                $horario->cupo_disponible <= 2 => 'low',
                                default => 'ok',
                            };
                            $mismoNivel = $alumno->nivel_id === $horario->nivel_id;
                        @endphp
                        <div class="schedule-card" style="--level-color: {{ $horario->nivel?->color_hex ?? '#42d8ef' }}">

                            <div class="schedule-top">
                                <div>
                                    <div class="schedule-name">{{ $horario->nombre_grupo }}</div>
                                    <div class="schedule-meta">
                                        {{ $horario->dia_semana->label() }} ·
                                        {{ \Illuminate\Support\Carbon::parse($horario->hora_inicio)->format('H:i') }}
                                        - {{ \Illuminate\Support\Carbon::parse($horario->hora_fin)->format('H:i') }}
                                        <br>
                                        Nivel: {{ $horario->nivel?->nombre }} {{ $mismoNivel ? '' : '(nivel cercano)' }}
                                        <br>
                                        Instructor: {{ $horario->instructor?->user?->name ?? '—' }}
                                        <br>
                                        Carril: {{ $horario->carril?->nombre ?? '—' }}
                                    </div>
                                </div>
                                <div class="animal" style="width:44px; height:44px; min-width:44px;">
                                    @if ($horario->nivel?->imagen)
                                        <img src="{{ asset($horario->nivel->imagen) }}" alt="{{ $horario->nivel->nombre }}" style="width:26px; height:26px;">
                                    @endif
                                </div>
                            </div>

                            <div class="schedule-cupo {{ $cupoClase }}">
                                @if ($horario->cupo_disponible <= 0)
                                    ● Sin cupo disponible
                                @else
                                    ● {{ $horario->cupo_disponible }} de {{ $horario->capacidad_maxima }} lugares disponibles
                                @endif
                            </div>

                            @if ($horario->ya_inscrito)
                                <span class="badge badge-cyan" style="align-self:flex-start;">✓ Ya inscrito en este grupo</span>
                            @else
                                <form method="POST" action="{{ route('portal.reservar.store') }}">
                                    @csrf
                                    <input type="hidden" name="alumno_id" value="{{ $alumno->id }}">
                                    <input type="hidden" name="horario_id" value="{{ $horario->id }}">
                                    <button type="submit" class="btn btn-primary btn-block" @disabled($horario->cupo_disponible <= 0)>
                                        {{ $horario->cupo_disponible <= 0 ? 'Sin cupo' : 'Reservar esta clase' }}
                                    </button>
                                </form>
                            @endif

                        </div>
                    @empty
                        <div class="empty-state" style="grid-column: 1 / -1;">
                            <h3>No hay grupos disponibles</h3>
                            <p>No encontramos horarios activos para esta sucursal en el nivel actual o niveles cercanos.</p>
                        </div>
                    @endforelse

                </div>

            @endif

        </div>

    </main>

</div>

</body>
</html>
