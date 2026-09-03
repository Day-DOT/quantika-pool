@extends('quantika.super-admin.layout')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
<style>
    /* =========================================================
       ALERTA DE PAGOS PRÓXIMOS A VENCER
    ========================================================= */

    .payment-alert {
        margin-top: 22px;
        padding: 20px 24px;
        border-radius: 20px;
        background: rgba(255, 189, 32, .08);
        border: 1px solid rgba(255, 189, 32, .35);
    }

    .payment-alert-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        margin-bottom: 14px;
    }

    .payment-alert-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 15px;
        font-weight: 900;
        color: var(--yellow);
    }

    .payment-alert-title .icon {
        font-size: 18px;
    }

    .payment-alert-header a {
        color: var(--cyan);
        font-size: 11px;
        font-weight: 800;
    }

    .payment-alert-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .payment-alert-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 10px 14px;
        border-radius: 13px;
        background: rgba(2, 28, 42, .45);
        font-size: 12px;
    }

    .payment-alert-row strong {
        font-weight: 900;
    }

    .payment-alert-dias {
        padding: 4px 10px;
        border-radius: 30px;
        font-size: 10px;
        font-weight: 900;
        background: rgba(255, 189, 32, .15);
        color: var(--yellow);
        white-space: nowrap;
    }

    .payment-alert-dias.hoy {
        background: rgba(255, 107, 107, .15);
        color: #ff6b6b;
    }

    /* stats específicos de este dashboard (nombres únicos, no chocan con el layout) */

    .stats-grid {
        grid-template-columns: repeat(3, 1fr);
        margin-top: 22px;
    }

    .stat-title {
        color: #82aebf;
        font-size: 12px;
        font-weight: 800;
    }

    .stat-number {
        margin-top: 15px;
        font-size: 31px;
        font-weight: 950;
        line-height: 1;
    }

    .stat-extra {
        margin-top: 7px;
        color: var(--green);
        font-size: 11px;
        font-weight: 800;
    }

    .money .stat-icon {
        color: var(--green);
        background: rgba(22,224,164,.10);
    }

    .money .stat-extra {
        color: var(--green);
    }

    /* =========================================================
       GRAFICAS
    ========================================================= */

    .charts-grid {
        display: grid;
        grid-template-columns: 1.4fr .8fr;
        gap: 20px;
        margin-top: 24px;
    }

    .panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        margin-bottom: 25px;
    }

    .panel-header h3 {
        font-size: 16px;
        font-weight: 900;
    }

    .panel-header span {
        color: var(--cyan);
        font-size: 11px;
        font-weight: 800;
    }

    .chart-bars {
        height: 180px;
        display: flex;
        align-items: end;
        justify-content: space-around;
        gap: 16px;
        padding: 0 10px;
        border-bottom: 1px solid rgba(255,255,255,.08);
    }

    .bar-column {
        flex: 1;
        max-width: 65px;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: end;
        align-items: center;
        gap: 8px;
    }

    .bar-value {
        font-size: 10px;
        color: #b5d3de;
        font-weight: 800;
    }

    .bar {
        width: 100%;
        max-width: 38px;
        min-height: 15px;
        border-radius: 9px 9px 3px 3px;
        background: linear-gradient(180deg, var(--cyan), rgba(66,213,238,.25));
        box-shadow: 0 0 18px rgba(66,213,238,.08);
    }

    .bar-label {
        font-size: 10px;
        color: #7198aa;
        margin-top: 6px;
    }

    .attendance {
        display: flex;
        align-items: center;
        gap: 24px;
    }

    .circle-chart {
        width: 145px;
        height: 145px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
    }

    .circle-chart::before {
        content: "";
        position: absolute;
        width: 105px;
        height: 105px;
        border-radius: 50%;
        background: #07364a;
    }

    .circle-value {
        position: relative;
        z-index: 1;
        font-size: 23px;
        font-weight: 950;
    }

    .attendance-info strong {
        display: block;
        font-size: 25px;
        margin-bottom: 5px;
    }

    .attendance-info p {
        color: var(--muted);
        font-size: 12px;
        line-height: 1.5;
    }

    /* =========================================================
       NIVELES
    ========================================================= */

    .section {
        margin-top: 28px;
    }

    .levels-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 17px;
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

    /* =========================================================
       ALUMNOS RECIENTES
    ========================================================= */

    .students-panel {
        overflow-x: auto;
    }

    .students-table {
        width: 100%;
        min-width: 750px;
        border-collapse: collapse;
    }

    .students-table th {
        padding: 12px 10px;
        text-align: left;
        color: #9db5c2;
        font-size: 9px;
        letter-spacing: 1px;
        text-transform: uppercase;
        border-bottom: 1px solid rgba(255,255,255,.07);
    }

    .students-table td {
        padding: 13px 10px;
        border-bottom: 1px solid rgba(255,255,255,.055);
        font-size: 11px;
    }

    .student {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .row-avatar {
        width: 36px;
        height: 36px;
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 10px;
        background: #a7cad7;
        color: #123749;
        font-weight: 950;
    }

    .student-name {
        font-weight: 800;
    }

    .student-email {
        color: #9db5c2;
        font-size: 9px;
        margin-top: 3px;
    }

    .level-mini {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        color: #dcecf2;
    }

    .level-mini img {
        width: 30px;
        height: 30px;
        object-fit: contain;
    }

    .attendance-line {
        width: 100px;
        height: 5px;
        border-radius: 20px;
        background: rgba(255,255,255,.07);
        overflow: hidden;
    }

    .attendance-line span {
        display: block;
        height: 100%;
        background: var(--cyan);
    }

    @media (max-width: 1250px) {
        .stats-grid { grid-template-columns: repeat(3, 1fr); }
        .levels-grid { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 1000px) {
        .charts-grid { grid-template-columns: 1fr; }
    }

    @media (max-width: 700px) {
        .stats-grid { grid-template-columns: 1fr 1fr; gap: 10px; }
        .stat-number { font-size: 25px; }
        .levels-grid { grid-template-columns: 1fr; }
        .attendance { flex-direction: column; align-items: flex-start; }
        .circle-chart { width: 120px; height: 120px; }
        .circle-chart::before { width: 88px; height: 88px; }
    }

    @media (max-width: 480px) {
        .stats-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

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
                    El progreso<br>
                    comienza<br>
                    <span class="cyan">en el agua.</span>
                </h2>

                <p class="hero-description">
                    Administra alumnos, clases, evaluaciones y niveles
                    desde un solo lugar.
                </p>

                <div class="hero-buttons">

                    <a href="{{ url('/alumnos/crear') }}" class="btn btn-primary">
                        + Registrar alumno
                        <span>→</span>
                    </a>

                </div>

            </div>

            <div class="hero-logo-box">
                <img src="{{ auth()->user()->logoUrl() }}" alt="Quantika Pool">
            </div>

        </div>

    </section>


    {{-- ACCIONES RÁPIDAS DE CONSULTA --}}
    <section class="section" style="margin-top:0;">

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

            <a href="{{ url('/niveles') }}" class="quick-link-card">
                <div class="quick-link-icon">◉</div>
                <div>
                    <div class="quick-link-title">Niveles</div>
                    <div class="quick-link-desc">Avance por nivel de natación</div>
                </div>
            </a>

            <a href="{{ route('instructores.index') }}" class="quick-link-card">
                <div class="quick-link-icon">♙</div>
                <div>
                    <div class="quick-link-title">Instructores</div>
                    <div class="quick-link-desc">Disponibilidad y asignación</div>
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


    {{-- ESTADISTICAS --}}
    <section class="stats-grid">

        <div class="stat-card">
            <div class="stat-top">
                <span class="stat-title">Alumnos activos</span>
                <span class="stat-icon">♟</span>
            </div>
            <div class="stat-number">{{ $statAlumnosActivos }}</div>
            <div class="stat-extra">↑ {{ $statAlumnosNuevosMes }} este mes</div>
        </div>

        <div class="stat-card">
            <div class="stat-top">
                <span class="stat-title">Instructores</span>
                <span class="stat-icon">♙</span>
            </div>
            <div class="stat-number">{{ $statInstructoresActivos }}</div>
            <div class="stat-extra">{{ $statInstructoresDisponibles }} disponibles</div>
        </div>

        <div class="stat-card">
            <div class="stat-top">
                <span class="stat-title">Clases de hoy</span>
                <span class="stat-icon">≋</span>
            </div>
            <div class="stat-number">{{ $statClasesHoy }}</div>
            <div class="stat-extra">{{ $statClasesEnCurso }} en curso</div>
        </div>

        <div class="stat-card">
            <div class="stat-top">
                <span class="stat-title">Asistencia</span>
                <span class="stat-icon">✓</span>
            </div>
            <div class="stat-number">{{ $statAsistenciaPct }}%</div>
            <div class="stat-extra">{{ $statAsistenciaPct >= 90 ? 'Excelente' : ($statAsistenciaPct >= 75 ? 'Buena' : 'A mejorar') }}</div>
        </div>

        <div class="stat-card money">
            <div class="stat-top">
                <span class="stat-title">Ingresos del mes</span>
                <span class="stat-icon">$</span>
            </div>
            <div class="stat-number">${{ number_format($statIngresosMes, 0) }}</div>
            <div class="stat-extra">
                @if ($statIngresosCambioPct === null)
                    Sin datos del mes anterior
                @else
                    {{ $statIngresosCambioPct >= 0 ? '↑' : '↓' }} {{ abs($statIngresosCambioPct) }}% este mes
                @endif
            </div>
        </div>

        <div class="stat-card money">
            <div class="stat-top">
                <span class="stat-title">Pagos pendientes</span>
                <span class="stat-icon">⏳</span>
            </div>
            <div class="stat-number">${{ number_format($statPagosPendientesMonto, 0) }}</div>
            <div class="stat-extra" style="color:var(--yellow);">
                {{ $statPagosPendientesCount }} {{ Str::plural('pago', $statPagosPendientesCount) }} pendientes
            </div>
        </div>

    </section>


    {{-- ALERTA: PAGOS PRÓXIMOS A VENCER --}}
    @if ($pagosProximosVencer->isNotEmpty())
        <section class="payment-alert">

            <div class="payment-alert-header">

                <div class="payment-alert-title">
                    <span class="icon">⚠</span>
                    {{ $pagosProximosVencer->count() }} {{ Str::plural('pago', $pagosProximosVencer->count()) }} por vencer en los próximos 5 días
                </div>

                <a href="{{ route('pagos.index') }}">Ver módulo de pagos →</a>

            </div>

            <div class="payment-alert-list">

                @foreach ($pagosProximosVencer as $fila)
                    @php($pago = $fila['pago'])
                    <div class="payment-alert-row">

                        <div>
                            <strong>{{ $pago->alumno->nombreCompleto() }}</strong>
                            · {{ $pago->concepto->label() }}
                            · ${{ number_format((float) $pago->monto, 0) }}
                        </div>

                        <span class="payment-alert-dias {{ $fila['diasRestantes'] === 0 ? 'hoy' : '' }}">
                            @if ($fila['diasRestantes'] === 0)
                                Vence hoy
                            @elseif ($fila['diasRestantes'] === 1)
                                Vence mañana
                            @else
                                Vence en {{ $fila['diasRestantes'] }} días
                            @endif
                        </span>

                    </div>
                @endforeach

            </div>

        </section>
    @endif


    {{-- GRAFICAS --}}
    <section class="charts-grid">

        <div class="panel">

            <div class="panel-header">
                <h3>Actividad semanal</h3>
                <span>Últimos 7 días</span>
            </div>

            <div class="chart-bars">

                @foreach ($actividadSemanal as $dia)
                    <div class="bar-column">
                        <span class="bar-value">{{ $dia['total'] }}</span>
                        <div class="bar" style="height:{{ $dia['total'] > 0 ? max(8, round(($dia['total'] / $maxActividadSemanal) * 100)) : 3 }}%;"></div>
                        <span class="bar-label">{{ $dia['label'] }}</span>
                    </div>
                @endforeach

            </div>

        </div>


        <div class="panel">

            <div class="panel-header">
                <h3>Asistencia general</h3>
                <span>Este mes</span>
            </div>

            <div class="attendance">

                <div class="circle-chart" style="background: conic-gradient(var(--green) 0deg {{ $statAsistenciaGrados }}deg, rgba(255,255,255,.07) {{ $statAsistenciaGrados }}deg 360deg);">
                    <div class="circle-value">{{ $statAsistenciaPct }}%</div>
                </div>

                <div class="attendance-info">
                    <strong>{{ $statAsistenciaPct >= 90 ? 'Excelente' : ($statAsistenciaPct >= 75 ? 'Buena' : 'A mejorar') }}</strong>
                    <p>
                        La asistencia promedio de los alumnos
                        {{ $statAsistenciaPct >= 90 ? 'se mantiene por encima del objetivo.' : 'todavía puede mejorar respecto al objetivo.' }}
                    </p>
                </div>

            </div>

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


    {{-- NIVELES --}}
    <section class="section">

        <div class="section-header">
            <h3>Niveles de aprendizaje</h3>
            <a href="{{ url('/niveles') }}" class="section-link">Ver todos los niveles →</a>
        </div>

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
                            <div class="level-description">{{ $nivel->categoria }}</div>
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

    </section>


    {{-- ALUMNOS RECIENTES --}}
    <section class="section">

        <div class="section-header">
            <h3>Alumnos recientes</h3>
            <a href="{{ url('/alumnos') }}" class="section-link">Ver todos los alumnos →</a>
        </div>

        <div class="panel students-panel">

            <table class="students-table">

                <thead>
                    <tr>
                        <th>Alumno</th>
                        <th>Nivel</th>
                        <th>Sucursal</th>
                        <th>Asistencia</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($alumnosRecientes as $fila)
                        @php($alumno = $fila['alumno'])
                        <tr>

                            <td>
                                <div class="student">

                                    <div class="row-avatar">{{ $fila['iniciales'] }}</div>

                                    <div>
                                        <div class="student-name">{{ $alumno->nombreCompleto() }}</div>
                                        <div class="student-email">{{ $alumno->email ?? ($alumno->tutorUser?->email ?? 'Sin correo') }}</div>
                                    </div>

                                </div>
                            </td>

                            <td>
                                @if ($alumno->nivel)
                                    <div class="level-mini">
                                        <img src="{{ asset($alumno->nivel->imagen) }}" alt="{{ $alumno->nivel->nombre }}">
                                        <span>{{ $alumno->nombreNivelConSubNivel() }}</span>
                                    </div>
                                @else
                                    <span style="color:var(--muted);">Sin nivel</span>
                                @endif
                            </td>

                            <td>{{ $alumno->sucursal->nombre }}</td>

                            <td>
                                @if ($fila['asistencia'] !== null)
                                    <div style="display:flex;align-items:center;gap:8px;">
                                        <span>{{ $fila['asistencia'] }}%</span>
                                        <div class="attendance-line">
                                            <span style="width:{{ $fila['asistencia'] }}%;"></span>
                                        </div>
                                    </div>
                                @else
                                    <span style="color:var(--muted);">Sin datos</span>
                                @endif
                            </td>

                            <td>
                                <span class="badge">● {{ $alumno->estado->label() }}</span>
                            </td>

                            <td>
                                <a href="{{ route('alumnos.show', $alumno) }}" style="text-decoration:none;color:inherit;">👁</a>
                                &nbsp;
                                <a href="{{ route('alumnos.edit', $alumno) }}" style="text-decoration:none;color:inherit;">✎</a>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center;color:var(--muted);">
                                Todavía no hay alumnos registrados.
                            </td>
                        </tr>
                    @endforelse

                </tbody>

            </table>

        </div>

    </section>

@endsection
