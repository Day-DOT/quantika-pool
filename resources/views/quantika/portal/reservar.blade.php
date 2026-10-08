<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar clase · QUANTIKA POOL</title>
    @include('quantika.portal.partials.styles')
</head>
<body>

<div class="quantika-app">

    @include('quantika.portal.partials.sidebar', ['activo' => 'reservar'])

    <main class="main">

        @include('quantika.portal.partials.topbar', [
            'titulo' => 'Recuperar clase',
            'alumnos' => $alumnos,
            'alumno' => $alumno,
        ])

        <div class="content">

            @if (! $alumno)

                <div class="empty-state">
                    <h3>Aún no tienes alumnos registrados</h3>
                    <p>No es posible recuperar clases hasta que la escuela vincule un alumno a tu cuenta.</p>
                </div>

            @else

                <div class="section-header">
                    <h3>Recuperar clase para {{ $alumno->nombreCompleto() }}</h3>
                </div>

                <p style="color:var(--muted); font-size:13px; line-height:1.6; margin-bottom:20px; max-width:680px;">
                    Si {{ $alumno->nombreCompleto() }} faltó a una clase, puedes pedir reponerla en otro grupo.
                    La reposición es de una sola clase, debe tomarse dentro del mismo mes de la falta y
                    se permiten hasta {{ $maximoPorMes }} por mes. La escuela confirma cada solicitud.
                </p>

                {{-- SOLICITUDES ENVIADAS --}}
                @if ($solicitudes->isNotEmpty())
                    <div class="data-card" style="padding:16px 20px; margin-bottom:20px;">
                        <strong style="display:block; margin-bottom:10px;">Tus solicitudes</strong>
                        @foreach ($solicitudes as $solicitud)
                            @php
                                $claseEstado = match ($solicitud->estado) {
                                    \App\Enums\EstadoInscripcion::Aprobada => 'badge-green',
                                    \App\Enums\EstadoInscripcion::Rechazada => 'badge-red',
                                    default => 'badge-yellow',
                                };
                            @endphp
                            <div style="display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap; padding:8px 0; border-top:1px solid var(--border);">
                                <span style="font-size:13px;">
                                    {{ $solicitud->horario?->nombre_grupo }} ·
                                    {{ $solicitud->fecha->translatedFormat('l d \d\e F') }}
                                    <span style="color:var(--muted);">(por la falta del {{ $solicitud->falta?->fecha?->format('d/m') }})</span>
                                </span>
                                <span class="badge {{ $claseEstado }}">{{ $solicitud->estado->label() }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if (! $admiteReposicion)

                    <div class="empty-state">
                        <h3>Las clases de bebés no tienen reposición</h3>
                        <p>Si tienes dudas, contacta a la escuela.</p>
                    </div>

                @elseif ($faltas->isEmpty())

                    <div class="empty-state">
                        <h3>No hay faltas por reponer</h3>
                        <p>{{ $alumno->nombreCompleto() }} no tiene faltas registradas este mes. Solo se pueden reponer clases a las que no asistió.</p>
                    </div>

                @elseif ($faltasLibres->isEmpty())

                    <div class="empty-state">
                        <h3>Tus solicitudes están en revisión</h3>
                        <p>Ya pediste reponer todas las faltas de este mes. La escuela te confirmará cada una.</p>
                    </div>

                @elseif ($restantesDelMes <= 0)

                    <div class="empty-state">
                        <h3>Ya usaste las reposiciones de este mes</h3>
                        <p>Se permiten hasta {{ $maximoPorMes }} reposiciones por mes, contando las solicitudes en espera.</p>
                    </div>

                @else

                    {{-- ELEGIR FECHA --}}
                    <form method="GET" action="{{ route('portal.reservar.index') }}" style="max-width:340px; margin-bottom:22px;">
                        <input type="hidden" name="alumno" value="{{ $alumno->id }}">
                        <label class="field-label" for="fecha">¿Qué día quieres reponer?</label>
                        <input type="date" name="fecha" id="fecha" class="field-input"
                               value="{{ $fecha->toDateString() }}"
                               min="{{ today()->toDateString() }}"
                               max="{{ $finDeMes->toDateString() }}"
                               onchange="this.form.submit()">
                        <span style="display:block; color:var(--muted); font-size:11px; margin-top:6px;">
                            Solo fechas de este mes (hasta el {{ $finDeMes->format('d/m/Y') }}).
                        </span>
                    </form>

                    <form method="POST" action="{{ route('portal.reservar.store') }}" id="formReponer">
                        @csrf
                        <input type="hidden" name="alumno_id" value="{{ $alumno->id }}">
                        <input type="hidden" name="fecha" value="{{ $fecha->toDateString() }}">

                        <div style="max-width:340px; margin-bottom:22px;">
                            <label class="field-label" for="cita_id">Falta que repones</label>
                            <select name="cita_id" id="cita_id" class="field-select" required>
                                @foreach ($faltasLibres as $falta)
                                    <option value="{{ $falta->id }}" @selected((int) old('cita_id') === $falta->id)>
                                        {{ $falta->fecha->translatedFormat('l d \d\e F') }} · {{ $falta->horario?->nombre_grupo }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="section-header">
                            <h3>Grupos del {{ $fecha->translatedFormat('l d \d\e F') }}</h3>
                        </div>

                        <div class="schedule-grid">

                            @forelse ($grupos as $grupo)
                                @php
                                    $cupoClase = match (true) {
                                        $grupo->cupo_disponible <= 0 => 'full',
                                        $grupo->cupo_disponible <= 2 => 'low',
                                        default => 'ok',
                                    };
                                @endphp
                                <div class="schedule-card" style="--level-color: {{ $grupo->nivel?->color_hex ?? '#42d8ef' }}">

                                    <div class="schedule-top">
                                        <div>
                                            <div class="schedule-name">{{ $grupo->nombre_grupo }}</div>
                                            <div class="schedule-meta">
                                                {{ substr($grupo->hora_inicio, 0, 5) }} - {{ substr($grupo->hora_fin, 0, 5) }}
                                                @if ($grupo->nivel)
                                                    <br>
                                                    Nivel: {{ $grupo->nivel->nombre }}
                                                @endif
                                                <br>
                                                Instructor: {{ $grupo->instructor?->user?->name ?? '—' }}
                                                <br>
                                                Carril: {{ $grupo->carril?->nombre ?? '—' }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="schedule-cupo {{ $cupoClase }}">
                                        @if ($grupo->cupo_disponible <= 0)
                                            ● Sin cupo ese día
                                        @else
                                            ● {{ $grupo->cupo_disponible }} lugar(es) disponible(s) ese día
                                        @endif
                                    </div>

                                    <label class="btn btn-outline btn-block" style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                                        <input type="radio" name="horario_id" value="{{ $grupo->id }}" class="radio-grupo"
                                               @checked((int) old('horario_id') === $grupo->id)
                                               @disabled($grupo->cupo_disponible <= 0)>
                                        {{ $grupo->cupo_disponible <= 0 ? 'Sin cupo' : 'Reponer en este grupo' }}
                                    </label>

                                </div>
                            @empty
                                <div class="empty-state" style="grid-column: 1 / -1;">
                                    <h3>No hay grupos ese día</h3>
                                    <p>Ese día no hay clases para la edad de {{ $alumno->nombreCompleto() }} en su sucursal. Prueba con otra fecha.</p>
                                </div>
                            @endforelse

                        </div>

                        @if ($grupos->isNotEmpty())
                            <div style="margin-top:20px; max-width:340px;">
                                <button type="submit" class="btn btn-primary btn-block" id="btnReponer" disabled>
                                    Solicitar reposición
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
        const boton = document.getElementById('btnReponer');
        const radios = Array.from(document.querySelectorAll('.radio-grupo'));

        if (! boton) {
            return;
        }

        function actualizar() {
            boton.disabled = ! radios.some((r) => r.checked);
        }

        radios.forEach((r) => r.addEventListener('change', actualizar));
        actualizar();
    })();
</script>

</body>
</html>
