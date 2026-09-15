<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Portal · QUANTIKA POOL</title>
    @include('quantika.portal.partials.styles')
</head>
<body>

<div class="quantika-app">

    @include('quantika.portal.partials.sidebar', ['activo' => 'dashboard'])

    <main class="main">

        @include('quantika.portal.partials.topbar', [
            'titulo' => 'Dashboard',
            'alumnos' => $alumnos,
            'alumno' => $alumno,
        ])

        <div class="content">

            @if ($alumnos->isEmpty())

                <div class="empty-state">
                    <h3>Aún no tienes alumnos registrados</h3>
                    <p>Cuando la escuela registre a tu hijo o hija vinculado a tu cuenta, aparecerá aquí.</p>
                </div>

            @else

                @if ($alumnos->count() > 1)

                    <div class="section-header">
                        <h3>Mis alumnos</h3>
                    </div>

                    <div class="kids-grid">
                        @foreach ($alumnos as $unAlumno)
                            @php $resumenTarjeta = $resumenes->get($unAlumno->id); @endphp
                            <a
                                href="{{ route('portal.dashboard', ['alumno' => $unAlumno->id]) }}"
                                class="kid-card {{ $alumno->id === $unAlumno->id ? 'active' : '' }}"
                            >
                                <div class="animal" style="--level-color: {{ $unAlumno->nivel?->color_hex ?? '#42d8ef' }}">
                                    @if ($unAlumno->foto_path)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($unAlumno->foto_path) }}" alt="Foto de {{ $unAlumno->nombreCompleto() }}" style="width:100%;height:100%;object-fit:cover;border-radius:inherit;">
                                    @elseif ($unAlumno->nivel?->imagen)
                                        <img src="{{ asset($unAlumno->nivel->imagen) }}" alt="{{ $unAlumno->nivel->nombre }}">
                                    @endif
                                </div>
                                <div>
                                    <div class="kid-name">{{ $unAlumno->nombreCompleto() }}</div>
                                    <div class="kid-meta">{{ $unAlumno->nombreNivelConSubNivel() ?? 'Sin nivel asignado' }} · {{ round($resumenTarjeta['porcentaje']) }}% de avance</div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                @endif

                @php
                    $nivelActivo = $alumno->nivel;
                    $pago = $resumenActivo['pago'];
                    $badgePago = match (true) {
                        $pago === null => ['badge-muted', 'Sin registros'],
                        $pago->estado === \App\Enums\EstadoPago::Pagado => ['badge-green', $pago->estado->label()],
                        $pago->estado === \App\Enums\EstadoPago::Vencido => ['badge-red', $pago->estado->label()],
                        $pago->estado === \App\Enums\EstadoPago::EnRevision => ['badge-purple', $pago->estado->label()],
                        default => ['badge-yellow', $pago->estado->label()],
                    };
                @endphp

                <div class="section-header">
                    <h3>Resumen de {{ $alumno->nombreCompleto() }}</h3>
                </div>

                <div class="level-block" style="--level-color: {{ $nivelActivo?->color_hex ?? '#42d8ef' }}">
                    <div class="animal">
                        @if ($alumno->foto_path)
                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($alumno->foto_path) }}" alt="Foto de {{ $alumno->nombreCompleto() }}" style="width:100%;height:100%;object-fit:cover;border-radius:inherit;">
                        @elseif ($nivelActivo?->imagen)
                            <img src="{{ asset($nivelActivo->imagen) }}" alt="{{ $nivelActivo->nombre }}">
                        @endif
                    </div>
                    <div style="flex:1;">
                        <div class="level-number">NIVEL {{ $nivelActivo?->orden ?? '—' }} · {{ \App\Models\ConfiguracionSistema::categoriasEdad()[$nivelActivo?->categoria_edad] ?? $nivelActivo?->categoria_edad ?? '' }}</div>
                        <div class="level-name">{{ $nivelActivo?->nombre ?? 'Sin nivel asignado' }}</div>
                        @if ($nivelActivo?->subtitulo)
                            <div class="level-description">{{ $nivelActivo->subtitulo }}</div>
                        @endif
                        <div class="level-description">{{ $nivelActivo?->descripcion ?? 'Este alumno aún no tiene un nivel asignado.' }}</div>

                        <div class="progress-row">
                            <span>Avance en el nivel</span>
                            <span>{{ round($resumenActivo['porcentaje']) }}%</span>
                        </div>
                        <div class="progress">
                            <span style="width:{{ round($resumenActivo['porcentaje']) }}%"></span>
                        </div>
                    </div>

                    <div class="data-card" style="margin-top:18px;padding:18px;">
                        <div class="section-header" style="margin:0 0 10px;">
                            <h3>Foto de identificación</h3>
                        </div>
                        <p class="card-description">Toma o selecciona una foto clara para identificar fácilmente a este alumno en el portal.</p>
                        <form method="POST" action="{{ route('portal.foto.update', ['alumno' => $alumno->id]) }}" enctype="multipart/form-data" style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
                            @csrf
                            <input type="file" name="foto" accept="image/jpeg,image/png" capture="user" required>
                            <button type="submit" class="btn btn-primary">Guardar foto</button>
                        </form>
                    </div>
                </div>

                <div class="stats-grid">

                    <div class="stat-card">
                        <div class="stat-top">
                            <span class="stat-name">Próximas clases</span>
                            <div class="stat-icon">≋</div>
                        </div>
                        <div class="stat-value">{{ $resumenActivo['proximasClases']->count() }}</div>
                        <div class="stat-sub">agendadas</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-top">
                            <span class="stat-name">Clases restantes del mes</span>
                            <div class="stat-icon">◷</div>
                        </div>
                        <div class="stat-value">{{ $resumenActivo['clasesRestantes'] ?? '—' }}</div>
                        <div class="stat-sub">según tu plan</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-top">
                            <span class="stat-name">Sucursal</span>
                            <div class="stat-icon">⌂</div>
                        </div>
                        <div class="stat-value" style="font-size:18px;">{{ $alumno->sucursal?->nombre ?? '—' }}</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-top">
                            <span class="stat-name">Estatus de pago</span>
                            <div class="stat-icon">$</div>
                        </div>
                        <div style="margin-top:10px;">
                            <span class="badge {{ $badgePago[0] }}">● {{ $badgePago[1] }}</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-top">
                            <span class="stat-name">Estado del alumno</span>
                            <div class="stat-icon">✓</div>
                        </div>
                        <div style="margin-top:10px;">
                            <span class="badge {{ $alumno->estado === \App\Enums\EstadoAlumno::Activo ? 'badge-green' : 'badge-muted' }}">
                                ● {{ $alumno->estado->label() }}
                            </span>
                        </div>
                    </div>

                </div>

                <div class="section-header">
                    <h3>Próximas clases</h3>
                    <a href="{{ route('portal.cuenta', ['alumno' => $alumno->id]) }}" class="section-link">Ver todas →</a>
                </div>

                <div class="data-card">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Horario</th>
                                <th>Grupo</th>
                                <th>Carril</th>
                                <th>Instructor</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($resumenActivo['proximasClases'] as $cita)
                                <tr>
                                    <td>{{ $cita->fecha->translatedFormat('d M Y') }}</td>
                                    <td>{{ \Illuminate\Support\Carbon::parse($cita->hora_inicio)->format('H:i') }} - {{ \Illuminate\Support\Carbon::parse($cita->hora_fin)->format('H:i') }}</td>
                                    <td>{{ $cita->horario?->nombre_grupo ?? '—' }}</td>
                                    <td>{{ $cita->horario?->carril?->nombre ?? '—' }}</td>
                                    <td>{{ $cita->horario?->instructor?->user?->name ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="empty-row">Sin clases agendadas próximamente. Recupera una desde "Recuperar clase".</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div style="margin-top:26px; display:flex; gap:12px; flex-wrap:wrap;">
                    <a href="{{ route('portal.reservar.index', ['alumno' => $alumno->id]) }}" class="btn btn-primary">+ Recuperar una clase</a>
                    <a href="{{ route('portal.progreso', ['alumno' => $alumno->id]) }}" class="btn btn-outline">Ver boleta de progreso</a>
                </div>

            @endif

        </div>

    </main>

</div>

</body>
</html>
