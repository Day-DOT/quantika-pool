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

                @if (! $alumno->plan_id)

                    <div class="empty-state">
                        <h3>Sin plan de mensualidad asignado</h3>
                        <p>{{ $alumno->nombreCompleto() }} no tiene un plan asignado todavía. Contacta a la escuela para que le asignen uno antes de reservar clases.</p>
                    </div>

                @else

                    <div class="data-card" style="padding:16px 20px; margin-bottom:20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
                        <div>
                            <strong>{{ $alumno->plan->nombre }}</strong>
                            <span style="color:var(--muted); font-size:12px;">({{ $alumno->plan->clases_por_semana }} clases/semana)</span>
                        </div>
                        <div id="contadorCupos" style="font-weight:800;" data-cupos-disponibles="{{ $cuposDisponibles }}">
                            @if ($cuposDisponibles > 0)
                                Te faltan <span id="cuposRestantes">{{ $cuposDisponibles }}</span> clase(s) por elegir esta semana
                            @else
                                Ya reservaste/tienes activas todas tus clases de la semana ({{ $cuposUsados }}/{{ $alumno->plan->clases_por_semana }})
                            @endif
                        </div>
                    </div>

                    <form method="POST" action="{{ route('portal.reservar.store') }}" id="formReservar">
                        @csrf
                        <input type="hidden" name="alumno_id" value="{{ $alumno->id }}">

                        <div class="schedule-grid">

                            @forelse ($horarios as $horario)
                                @php
                                    $cupoClase = match (true) {
                                        $horario->cupo_disponible <= 0 => 'full',
                                        $horario->cupo_disponible <= 2 => 'low',
                                        default => 'ok',
                                    };
                                    $mismoNivel = $alumno->nivel_id === $horario->nivel_id;
                                    $seleccionable = ! $horario->ya_inscrito && ! $horario->ya_pendiente && $horario->cupo_disponible > 0;
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
                                    @elseif ($horario->ya_pendiente)
                                        <span class="badge badge-yellow" style="align-self:flex-start;">⏳ Reserva pendiente de aprobación</span>
                                    @else
                                        <label class="btn btn-outline btn-block horario-checkbox" style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                                            <input
                                                type="checkbox"
                                                name="horario_ids[]"
                                                value="{{ $horario->id }}"
                                                class="check-horario"
                                                {{ $seleccionable ? '' : 'disabled data-sin-cupo="1"' }}>
                                            {{ $horario->cupo_disponible <= 0 ? 'Sin cupo' : 'Elegir esta clase' }}
                                        </label>
                                    @endif

                                </div>
                            @empty
                                <div class="empty-state" style="grid-column: 1 / -1;">
                                    <h3>No hay grupos disponibles</h3>
                                    <p>No encontramos horarios activos para esta sucursal en el nivel actual o niveles cercanos.</p>
                                </div>
                            @endforelse

                        </div>

                        @if ($cuposDisponibles > 0 && $horarios->isNotEmpty())
                            <div style="margin-top:20px; max-width:340px;">
                                <button type="submit" class="btn btn-primary btn-block" id="btnReservar" disabled>
                                    Reservar clases seleccionadas
                                </button>
                            </div>
                        @endif
                    </form>

                @endif

            @endif

        </div>

    </main>

</div>

<script>
    (function () {
        const contador = document.getElementById('contadorCupos');
        const boton = document.getElementById('btnReservar');

        if (! contador || ! boton) {
            return;
        }

        const limite = parseInt(contador.dataset.cuposDisponibles, 10) || 0;
        const checks = Array.from(document.querySelectorAll('.check-horario'));
        const restantesSpan = document.getElementById('cuposRestantes');

        function actualizar() {
            const seleccionados = checks.filter((c) => c.checked);

            checks.forEach((c) => {
                if (! c.checked && ! c.dataset.sinCupo) {
                    c.disabled = seleccionados.length >= limite;
                }
            });

            if (restantesSpan) {
                restantesSpan.textContent = Math.max(0, limite - seleccionados.length);
            }

            boton.disabled = seleccionados.length === 0 || seleccionados.length > limite;
        }

        checks.forEach((c) => c.addEventListener('change', actualizar));
        actualizar();
    })();
</script>

</body>
</html>
