<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi progreso · QUANTIKA POOL</title>
    @include('quantika.portal.partials.styles')
</head>
<body>

<div class="quantika-app">

    @include('quantika.portal.partials.sidebar', ['activo' => 'progreso'])

    <main class="main">

        @include('quantika.portal.partials.topbar', [
            'titulo' => 'Mi progreso',
            'alumnos' => $alumnos,
            'alumno' => $alumno,
        ])

        <div class="content">

            @if (! $alumno)

                <div class="empty-state">
                    <h3>Aún no tienes alumnos registrados</h3>
                    <p>Cuando la escuela registre a tu hijo o hija, podrás ver aquí su progreso.</p>
                </div>

            @else

                <div class="section-header">
                    <h3>Boleta digital de {{ $alumno->nombreCompleto() }}</h3>
                </div>

                <div class="level-block" style="--level-color: {{ $nivelActual?->color_hex ?? '#42d8ef' }}">
                    <div class="animal">
                        @if ($nivelActual?->imagen)
                            <img src="{{ asset($nivelActual->imagen) }}" alt="{{ $nivelActual->nombre }}">
                        @endif
                    </div>
                    <div style="flex:1;">
                        <div class="level-number">NIVEL {{ $nivelActual?->orden ?? '—' }} · {{ $nivelActual?->categoria ?? '' }}</div>
                        <div class="level-name">{{ $nivelActual?->nombre ?? 'Sin nivel asignado' }}</div>
                        <div class="level-description">
                            @if ($ultimaEvaluacion)
                                Última evaluación: {{ $ultimaEvaluacion->fecha->translatedFormat('d M Y') }}
                                @if ($ultimaEvaluacion->instructor?->user)
                                    · por {{ $ultimaEvaluacion->instructor->user->name }}
                                @endif
                            @else
                                Todavía no se ha registrado una evaluación para este nivel.
                            @endif
                        </div>

                        <div class="progress-row">
                            <span>Dominio global del nivel</span>
                            <span>{{ round($porcentaje) }}%</span>
                        </div>
                        <div class="progress">
                            <span style="width:{{ round($porcentaje) }}%"></span>
                        </div>
                    </div>
                </div>

                <div class="section-header">
                    <h3>Desglose por criterio</h3>
                </div>

                <div class="data-card">
                    @forelse ($criterios as $criterio)
                        @php
                            $detalle = $detallesPorCriterio->get($criterio->id);
                            [$claseBadge, $etiqueta] = match ($detalle?->estado) {
                                \App\Enums\EstadoEvaluacionDetalle::Logrado => ['badge-green', 'Logrado'],
                                \App\Enums\EstadoEvaluacionDetalle::EnProceso => ['badge-yellow', 'En proceso'],
                                default => ['badge-muted', 'No iniciado'],
                            };
                        @endphp
                        <div class="criterio-row">
                            <div>
                                <div class="criterio-nombre">{{ $criterio->nombre }}</div>
                                @if ($criterio->descripcion)
                                    <div class="criterio-obs">{{ $criterio->descripcion }}</div>
                                @endif
                                @if ($detalle?->observaciones)
                                    <div class="criterio-obs">"{{ $detalle->observaciones }}"</div>
                                @endif
                            </div>
                            <span class="badge {{ $claseBadge }}">● {{ $etiqueta }}</span>
                        </div>
                    @empty
                        <div class="empty-row">Este nivel aún no tiene criterios de evaluación configurados.</div>
                    @endforelse
                </div>

                @if ($ultimaEvaluacion?->observaciones)
                    <div class="section-header"><h3>Observaciones generales del instructor</h3></div>
                    <div class="data-card" style="padding:20px;">
                        <p style="color:var(--muted); font-size:13px; line-height:1.6;">{{ $ultimaEvaluacion->observaciones }}</p>
                    </div>
                @endif

                @if ($historial->count() > 1)
                    <div class="section-header"><h3>Historial de evaluaciones</h3></div>
                    <div class="data-card">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Nivel</th>
                                    <th>Avance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($historial as $evaluacion)
                                    <tr>
                                        <td>{{ $evaluacion->fecha->translatedFormat('d M Y') }}</td>
                                        <td>{{ $evaluacion->nivel?->nombre ?? '—' }}</td>
                                        <td>{{ round($evaluacion->porcentajeAvance()) }}%</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

            @endif

        </div>

    </main>

</div>

</body>
</html>
