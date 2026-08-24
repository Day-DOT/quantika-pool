<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagos y clases · QUANTIKA POOL</title>
    @include('quantika.portal.partials.styles')
</head>
<body>

<div class="quantika-app">

    @include('quantika.portal.partials.sidebar', ['activo' => 'cuenta'])

    <main class="main">

        @include('quantika.portal.partials.topbar', [
            'titulo' => 'Pagos y clases',
            'alumnos' => $alumnos,
            'alumno' => $alumno,
        ])

        <div class="content">

            @if (! $alumno)

                <div class="empty-state">
                    <h3>Aún no tienes alumnos registrados</h3>
                    <p>Cuando la escuela registre a tu hijo o hija, podrás consultar aquí sus clases y pagos.</p>
                </div>

            @else

                <div class="section-header">
                    <h3>Estado de cuenta de {{ $alumno->nombreCompleto() }}</h3>
                </div>

                <div class="stats-grid" style="grid-template-columns: repeat(3, minmax(0,1fr));">
                    <div class="stat-card">
                        <div class="stat-top">
                            <span class="stat-name">Próximas clases</span>
                            <div class="stat-icon">≋</div>
                        </div>
                        <div class="stat-value">{{ $proximasClases->count() }}</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-top">
                            <span class="stat-name">Pagos registrados</span>
                            <div class="stat-icon">$</div>
                        </div>
                        <div class="stat-value">{{ $pagos->count() }}</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-top">
                            <span class="stat-name">Pendientes / vencidos</span>
                            <div class="stat-icon">!</div>
                        </div>
                        <div class="stat-value" style="color: {{ $pendientesCount > 0 ? 'var(--red)' : 'var(--green)' }}">{{ $pendientesCount }}</div>
                    </div>
                </div>

                <div class="section-header">
                    <h3>Próximas clases agendadas</h3>
                </div>

                <div class="data-card">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Horario</th>
                                <th>Grupo</th>
                                <th>Instructor</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($proximasClases as $cita)
                                <tr>
                                    <td>{{ $cita->fecha->translatedFormat('d M Y') }}</td>
                                    <td>{{ \Illuminate\Support\Carbon::parse($cita->hora_inicio)->format('H:i') }} - {{ \Illuminate\Support\Carbon::parse($cita->hora_fin)->format('H:i') }}</td>
                                    <td>{{ $cita->horario?->nombre_grupo ?? '—' }}</td>
                                    <td>{{ $cita->horario?->instructor?->user?->name ?? '—' }}</td>
                                    <td><span class="badge badge-cyan">● {{ $cita->estado->label() }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="empty-row">No hay clases agendadas próximamente.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="section-header">
                    <h3>Historial de pagos</h3>
                </div>

                <div class="data-card">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Periodo</th>
                                <th>Concepto</th>
                                <th>Monto</th>
                                <th>Vencimiento</th>
                                <th>Fecha de pago</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pagos as $pago)
                                @php
                                    $claseBadge = match ($pago->estado) {
                                        \App\Enums\EstadoPago::Pagado => 'badge-green',
                                        \App\Enums\EstadoPago::Vencido => 'badge-red',
                                        \App\Enums\EstadoPago::EnRevision => 'badge-purple',
                                        default => 'badge-yellow',
                                    };
                                @endphp
                                <tr>
                                    <td>{{ $pago->periodo ?? '—' }}</td>
                                    <td>{{ $pago->concepto->label() }}</td>
                                    <td>${{ number_format($pago->monto, 2) }}</td>
                                    <td>{{ $pago->fecha_vencimiento?->translatedFormat('d M Y') ?? '—' }}</td>
                                    <td>{{ $pago->fecha_pago?->translatedFormat('d M Y') ?? '—' }}</td>
                                    <td><span class="badge {{ $claseBadge }}">● {{ $pago->estado->label() }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="empty-row">No hay pagos registrados todavía.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <p style="color:var(--muted); font-size:11px; margin-top:16px;">
                    Este portal es solo de consulta. Para realizar un pago acude a tu sucursal o comunícate con la escuela.
                </p>

            @endif

        </div>

    </main>

</div>

</body>
</html>
