@extends('quantika.super-admin.layout')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@php
    $sucursalActual = auth()->user()->sucursalActual();
@endphp

@section('content')

    {{-- HERO --}}
    <section class="hero">

        <div class="hero-content">

            <div class="hero-text">

                <div class="status">
                    <span></span>
                    SISTEMA ACTIVO
                </div>

                <h2>
                    Control total<br>
                    de <span class="cyan">{{ $sucursalActual?->nombre ?? 'Quantika Pool' }}.</span>
                </h2>

                <p class="hero-description">
                    @if ($sucursalActual)
                        Administra los alumnos, usuarios, niveles y el estatus financiero de esta sucursal.
                    @else
                        Administra ambas sucursales, usuarios, niveles y el estatus financiero
                        desde un solo lugar.
                    @endif
                </p>

                <div class="hero-buttons">

                    <a href="{{ route('alumnos.index', ['crear' => 1]) }}" class="btn btn-primary">
                        + Registrar alumno
                        <span>→</span>
                    </a>

                </div>

            </div>

            <div class="hero-logo-box">
                <img src="{{ auth()->user()->logoUrl() }}" alt="{{ $sucursalActual?->nombre ?? 'Quantika Pool' }}">
            </div>

        </div>

    </section>


    {{-- ACCIONES RÁPIDAS DE CONSULTA --}}
    <section class="section" style="margin-top:22px;">

        <div class="section-header">
            <h3>Acciones rápidas</h3>
        </div>

        <div class="quick-links-grid">

            <a href="{{ route('asistencia.escanear') }}" class="quick-link-card">
                <div class="quick-link-icon">▦</div>
                <div>
                    <div class="quick-link-title">Escanear asistencia</div>
                    <div class="quick-link-desc">Registrar entrada con el código QR del alumno</div>
                </div>
            </a>

            <a href="{{ route('reservas.index') }}" class="quick-link-card">
                @if ($reservasPendientesCount > 0)
                    <span class="quick-link-badge">{{ $reservasPendientesCount }}</span>
                @endif
                <div class="quick-link-icon">✓</div>
                <div>
                    <div class="quick-link-title">Reservas pendientes</div>
                    <div class="quick-link-desc">Aprobar o rechazar reservas de alumnos</div>
                </div>
            </a>

            <a href="{{ route('pagos.deudores') }}" class="quick-link-card">
                @if ($deudoresCount > 0)
                    <span class="quick-link-badge">{{ $deudoresCount }}</span>
                @endif
                <div class="quick-link-icon">$</div>
                <div>
                    <div class="quick-link-title">Deudores</div>
                    <div class="quick-link-desc">Alumnos con saldo vencido</div>
                </div>
            </a>

            <a href="{{ route('horarios.index') }}" class="quick-link-card">
                <div class="quick-link-icon">▣</div>
                <div>
                    <div class="quick-link-title">Horarios</div>
                    <div class="quick-link-desc">Clases, cupos e instructores</div>
                </div>
            </a>

            <a href="{{ route('evaluaciones.index') }}" class="quick-link-card">
                <div class="quick-link-icon">📈</div>
                <div>
                    <div class="quick-link-title">Evaluaciones</div>
                    <div class="quick-link-desc">Progreso de los alumnos por instructor</div>
                </div>
            </a>

            <a href="{{ url('/alumnos') }}" class="quick-link-card">
                <div class="quick-link-icon">👥</div>
                <div>
                    <div class="quick-link-title">Alumnos</div>
                    <div class="quick-link-desc">Consultar o editar un alumno</div>
                </div>
            </a>

            <a href="{{ route('super-admin.sucursales.index') }}" class="quick-link-card">
                <div class="quick-link-icon">🏢</div>
                <div>
                    <div class="quick-link-title">Sucursales</div>
                    <div class="quick-link-desc">Gestionar sedes de Quantika Pool</div>
                </div>
            </a>

            <a href="{{ route('super-admin.usuarios.index') }}" class="quick-link-card">
                <div class="quick-link-icon">👤</div>
                <div>
                    <div class="quick-link-title">Usuarios</div>
                    <div class="quick-link-desc">Admins, instructores y tutores</div>
                </div>
            </a>

            <a href="{{ route('super-admin.planes.index') }}" class="quick-link-card">
                <div class="quick-link-icon">$</div>
                <div>
                    <div class="quick-link-title">Planes de mensualidad</div>
                    <div class="quick-link-desc">Catálogo de planes por semana</div>
                </div>
            </a>

            <a href="{{ route('niveles.index') }}" class="quick-link-card">
                <div class="quick-link-icon">◉</div>
                <div>
                    <div class="quick-link-title">Niveles</div>
                    <div class="quick-link-desc">Avance por nivel de natación</div>
                </div>
            </a>

            <a href="{{ route('pagos.index') }}" class="quick-link-card">
                <div class="quick-link-icon">📊</div>
                <div>
                    <div class="quick-link-title">Pagos</div>
                    <div class="quick-link-desc">Mensualidades y próximos cobros</div>
                </div>
            </a>

        </div>

    </section>


    {{-- CALENDARIO SEMANAL DE CLASES --}}
    <section class="section">

        <div class="section-header">
            <h3>Calendario de clases de la semana</h3>
            <a href="{{ route('horarios.index') }}" class="section-link">Ver tablero de horarios →</a>
        </div>

        <div class="week-calendar">

            @foreach ($calendarioSemana as $dia)
                <div class="week-day {{ $dia['esHoy'] ? 'es-hoy' : '' }}">

                    <div class="week-day-head">
                        <div class="week-day-label">{{ mb_strtoupper($dia['dia']->label()) }}</div>
                        <div class="week-day-fecha">{{ $dia['fecha']->format('d/m') }}</div>
                    </div>

                    @forelse ($dia['clases'] as $fila)
                        <div class="week-class">
                            <div class="week-class-nombre">{{ $fila['horario']->nombre_grupo }}</div>
                            <div class="week-class-hora">
                                {{ substr($fila['horario']->hora_inicio, 0, 5) }}–{{ substr($fila['horario']->hora_fin, 0, 5) }}
                            </div>
                            <div class="week-class-instructor">
                                {{ $fila['horario']->instructor?->user?->name ?? 'Sin instructor' }}
                            </div>
                            <span class="week-class-cupo {{ $fila['cupoDisponible'] <= 0 ? 'full' : ($fila['cupoDisponible'] <= 2 ? 'low' : 'ok') }}">
                                {{ $fila['cupoDisponible'] <= 0 ? 'Sin cupo' : $fila['cupoDisponible'].'/'.$fila['horario']->capacidad_maxima.' lugares' }}
                            </span>
                        </div>
                    @empty
                        <div class="week-day-empty">Sin clases</div>
                    @endforelse

                </div>
            @endforeach

        </div>

    </section>


    <section class="panel" style="margin: 24px 0;">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px;">
            <div>
                <div style="color:var(--cyan); font-size:11px; font-weight:900; letter-spacing:2px; margin-bottom:6px;">
                    {{ $esGlobal ? 'VISTA CONSOLIDADA' : 'VISTA FILTRADA' }}
                </div>
                <h2 style="font-family:'Outfit',sans-serif; font-size:24px; font-weight:900;">
                    @if($esGlobal)
                        Todas las sucursales
                    @else
                        {{ $sucursalActual->nombre ?? 'Sucursal' }}
                    @endif
                </h2>
                <p style="color:var(--muted); font-size:13px; margin-top:6px;">
                    Usa el selector de sucursal en la esquina superior derecha para alternar entre el consolidado
                    de Quantika Pool y el detalle de cada sede.
                </p>
            </div>
            <a href="{{ route('alumnos.index', ['crear' => 1]) }}" class="btn btn-primary">
                + Registrar alumno
            </a>
        </div>
    </section>

    {{-- ESTADÍSTICAS GENERALES --}}
    <section class="stats-grid">

        <div class="stat-card">
            <div class="stat-top">
                <span class="stat-name">Alumnos activos</span>
                <div class="stat-icon">♟</div>
            </div>
            <div class="stat-value">{{ $stats['alumnos_activos'] }}</div>
            <div class="stat-change">{{ $stats['alumnos_total'] }} en total</div>
        </div>

        <div class="stat-card">
            <div class="stat-top">
                <span class="stat-name">Instructores</span>
                <div class="stat-icon">🏊</div>
            </div>
            <div class="stat-value">{{ $stats['instructores'] }}</div>
            <div class="stat-change">Activos en plantilla</div>
        </div>

        <div class="stat-card">
            <div class="stat-top">
                <span class="stat-name">Citas de hoy</span>
                <div class="stat-icon">▣</div>
            </div>
            <div class="stat-value">{{ $stats['citas_hoy'] }}</div>
            <div class="stat-change">Clases programadas</div>
        </div>

        <div class="stat-card">
            <div class="stat-top">
                <span class="stat-name">Ingresos del mes</span>
                <div class="stat-icon">$</div>
            </div>
            <div class="stat-value">${{ number_format($stats['ingresos_mes'], 2) }}</div>
            <div class="stat-change">Pagos cobrados este mes</div>
        </div>

    </section>

    {{-- CONTROL FINANCIERO --}}
    <div class="section-header">
        <h3>Control financiero</h3>
        <a href="{{ route('pagos.index') }}" class="section-link">Ver detalle de pagos →</a>
    </div>

    <section class="stats-grid">

        <div class="stat-card">
            <div class="stat-top">
                <span class="stat-name">Pendientes</span>
                <div class="stat-icon" style="color:var(--yellow); background:rgba(255,194,41,.12);">●</div>
            </div>
            <div class="stat-value">{{ $stats['pagos_pendientes'] }}</div>
            <div class="stat-change" style="color:var(--yellow);">${{ number_format($stats['monto_pendiente'], 2) }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-top">
                <span class="stat-name">Pagados</span>
                <div class="stat-icon">✓</div>
            </div>
            <div class="stat-value">{{ $stats['pagos_pagados'] }}</div>
            <div class="stat-change">Este periodo</div>
        </div>

        <div class="stat-card">
            <div class="stat-top">
                <span class="stat-name">En revisión</span>
                <div class="stat-icon" style="color:var(--blue); background:rgba(22,207,255,.12);">◔</div>
            </div>
            <div class="stat-value">{{ $stats['pagos_en_revision'] }}</div>
            <div class="stat-change">Pendientes de validar</div>
        </div>

        <div class="stat-card">
            <div class="stat-top">
                <span class="stat-name">Vencidos</span>
                <div class="stat-icon" style="color:var(--red); background:rgba(255,107,107,.12);">!</div>
            </div>
            <div class="stat-value">{{ $stats['pagos_vencidos'] }}</div>
            <div class="stat-change" style="color:var(--red);">${{ number_format($stats['monto_vencido'], 2) }}</div>
        </div>

    </section>

    @if($esGlobal)
        <div class="section-header">
            <h3>Desglose por sucursal</h3>
        </div>

        <div class="panel">
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Sucursal</th>
                            <th>Alumnos activos</th>
                            <th>Instructores</th>
                            <th>Ingresos del mes</th>
                            <th>Pendientes</th>
                            <th>Vencidos</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sucursales as $s)
                            @php $st = $statsPorSucursal[$s->id]; @endphp
                            <tr>
                                <td><strong>{{ $s->nombre }}</strong></td>
                                <td>{{ $st['alumnos_activos'] }}</td>
                                <td>{{ $st['instructores'] }}</td>
                                <td>${{ number_format($st['ingresos_mes'], 2) }}</td>
                                <td>{{ $st['pagos_pendientes'] }}</td>
                                <td>{{ $st['pagos_vencidos'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- NIVELES --}}
    <div class="section-header">
        <h3>Niveles de aprendizaje</h3>
        <a href="{{ route('niveles.index') }}" class="section-link">Gestionar niveles →</a>
    </div>

    <div class="panel">
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nivel</th>
                        <th>Categoría</th>
                        <th>Alumnos</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($niveles as $nivel)
                        <tr>
                            <td>{{ str_pad((string) $nivel->orden, 2, '0', STR_PAD_LEFT) }}</td>
                            <td>
                                <div style="display:flex; align-items:center; gap:10px;">
                                    @if($nivel->imagen)
                                        <img src="{{ asset($nivel->imagen) }}" alt="" style="width:26px; height:26px; object-fit:contain;">
                                    @endif
                                    <strong>{{ $nivel->nombre }}</strong>
                                </div>
                            </td>
                            <td>{{ $nivel->categoria }}</td>
                            <td>{{ $nivel->alumnos_count ?? $nivel->alumnos()->count() }}</td>
                            <td>
                                @if($nivel->activo)
                                    <span class="badge">● Activo</span>
                                @else
                                    <span class="badge badge-muted">● Inactivo</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ALUMNOS RECIENTES --}}
    <div class="section-header">
        <h3>Alumnos recientes</h3>
        <a href="{{ route('alumnos.index') }}" class="section-link">Ver todos los alumnos →</a>
    </div>

    <div class="panel">
        <div class="table-wrap">
            @if($alumnosRecientes->isEmpty())
                <div class="empty-state">Aún no hay alumnos registrados.</div>
            @else
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Alumno</th>
                            <th>Nivel</th>
                            <th>Sucursal</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($alumnosRecientes as $alumno)
                            <tr>
                                <td><strong>{{ $alumno->nombreCompleto() }}</strong></td>
                                <td>{{ $alumno->nombreNivelConSubNivel() ?? '—' }}</td>
                                <td>{{ $alumno->sucursal->nombre ?? '—' }}</td>
                                <td>
                                    <span class="badge {{ $alumno->estado->value !== 'activo' ? 'badge-muted' : '' }}">
                                        ● {{ $alumno->estado->label() }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('alumnos.show', $alumno) }}" class="section-link">Ver →</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

@endsection
