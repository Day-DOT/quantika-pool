@extends('quantika.super-admin.layout')

@section('title', 'Horarios y clases')
@section('page-title', 'Horarios y clases')

@push('styles')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>

    :root {

        --cyan-light: #8df4ff;

        --dark: #022536;
        --dark-2: #07374c;

        --glass: rgba(3, 38, 56, .72);

    }

    /* =========================
       CONTENEDOR
    ========================= */

    .page {

        width: min(1450px, 94%);

        margin: auto;

        padding: 26px 0 50px;

    }

    /* =========================
       TITULO
    ========================= */

    .title-area {

        margin-bottom: 22px;

    }

    .eyebrow {

        color: var(--cyan);

        text-transform: uppercase;

        letter-spacing: 2px;

        font-size: 11px;

        font-weight: 700;

        margin-bottom: 7px;

    }

    .title-area h1 {

        font-family: 'Outfit', sans-serif;

        font-size: clamp(28px, 3vw, 42px);

        line-height: 1;

        margin-bottom: 9px;

    }

    .subtitle {

        color: #9ebbc5;

        font-size: 14px;

    }

    /* =========================
       RESUMEN
    ========================= */

    .stats {

        display: grid;

        grid-template-columns:
            repeat(4, minmax(0,1fr));

        gap: 14px;

        margin-bottom: 22px;

    }

    .stat {

        background: var(--glass);

        border: 1px solid var(--border);

        border-radius: 18px;

        padding: 17px;

        backdrop-filter: blur(16px);

        box-shadow: 0 15px 40px rgba(0,0,0,.16);

    }

    .stat .stat-top {

        display: flex;

        justify-content: space-between;

        align-items: center;

        margin-bottom: 9px;

    }

    .stat .stat-icon {

        width: 35px;

        height: 35px;

        border-radius: 11px;

        display: grid;

        place-items: center;

        background: rgba(67,217,233,.11);

        color: var(--cyan-light);

    }

    .stat small {

        color: #8daeba;

        font-size: 12px;

    }

    .stat strong {

        font-family: 'Outfit';

        font-size: 25px;

    }

    /* =========================
       FILTROS
    ========================= */

    .filters {

        display: grid;

        grid-template-columns:
            1.3fr
            1fr
            1fr
            1fr
            auto;

        gap: 11px;

        padding: 16px;

        background: rgba(2,31,46,.74);

        border: 1px solid var(--border);

        border-radius: 18px;

        margin-bottom: 20px;

        backdrop-filter: blur(15px);

    }

    .filter {

        display: flex;

        flex-direction: column;

        gap: 5px;

    }

    .filter label {

        color: #83aebb;

        font-size: 10px;

        text-transform: uppercase;

        letter-spacing: .7px;

    }

    .filter select,
    .filter input {

        height: 42px;

        border-radius: 11px;

        border: 1px solid rgba(255,255,255,.10);

        background: rgba(255,255,255,.055);

        color: white;

        padding: 0 12px;

        outline: none;

    }

    .filter select option {

        background: #07374c;

    }

    .filter button {

        height: 42px;

        align-self: end;

        border: none;

        border-radius: 11px;

        padding: 0 18px;

        cursor: pointer;

        color: #002531;

        font-weight: 800;

        background:
            linear-gradient(
                135deg,
                #8df4ff,
                #35cddd
            );

    }

    /* =========================
       CALENDARIO
    ========================= */

    .calendar-card {

        background: rgba(2,31,46,.78);

        border: 1px solid var(--border);

        border-radius: 24px;

        overflow: hidden;

        backdrop-filter: blur(16px);

        box-shadow:
            0 25px 70px rgba(0,0,0,.22);

    }

    .calendar-header {

        padding: 18px 20px;

        display: flex;

        align-items: center;

        justify-content: space-between;

        border-bottom: 1px solid rgba(255,255,255,.07);

    }

    .calendar-title {

        display: flex;

        align-items: center;

        gap: 12px;

    }

    .calendar-title-icon {

        width: 40px;

        height: 40px;

        display: grid;

        place-items: center;

        border-radius: 12px;

        background: rgba(67,217,233,.10);

        color: var(--cyan);

        font-size: 19px;

    }

    .calendar-title h2 {

        font-family: 'Outfit';

        font-size: 19px;

    }

    .calendar-title p {

        color: #789fac;

        font-size: 11px;

        margin-top: 2px;

    }

    .week-controls {

        display: flex;

        align-items: center;

        gap: 7px;

    }

    .week-controls button {

        width: 35px;

        height: 35px;

        border-radius: 10px;

        border: 1px solid var(--border);

        background: rgba(255,255,255,.05);

        color: white;

        cursor: pointer;

    }

    .today {

        padding: 0 13px;

        width: auto !important;

        font-size: 12px;

        font-weight: 700;

    }

    .btn-modal-submit {
        min-height: 48px;
        padding: 0 21px;
        border-radius: 13px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 15px;
        font-size: 14px;
        font-weight: 900;
        border: none;
        cursor: pointer;
        background: #42d5ee;
        color: #023146;
        box-shadow: 0 10px 25px rgba(66,213,238,.18);
        transition: .2s ease;
    }

    .btn-modal-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 30px rgba(66,213,238,.25);
    }

    /* =========================
       WEEK
    ========================= */

    .calendar {

        display: grid;

        grid-template-columns: 65px repeat(7, 1fr);

        min-width: 950px;

    }

    .calendar-wrapper {

        overflow-x: auto;

    }

    .corner {

        border-right: 1px solid rgba(255,255,255,.06);

        border-bottom: 1px solid rgba(255,255,255,.06);

    }

    .day {

        padding: 13px 8px;

        text-align: center;

        border-right: 1px solid rgba(255,255,255,.06);

        border-bottom: 1px solid rgba(255,255,255,.06);

    }

    .day span {

        display: block;

        font-size: 10px;

        color: #7799a6;

        text-transform: uppercase;

    }

    .day strong {

        display: block;

        font-family: 'Outfit';

        font-size: 18px;

        margin-top: 3px;

    }

    .day.active {

        background: rgba(67,217,233,.08);

    }

    .day.active strong {

        color: var(--cyan-light);

    }

    .time {

        min-height: 92px;

        display: flex;

        align-items: flex-start;

        justify-content: center;

        padding-top: 12px;

        color: #71929e;

        font-size: 10px;

        border-right: 1px solid rgba(255,255,255,.06);

        border-bottom: 1px solid rgba(255,255,255,.06);

    }

    .slot {

        min-height: 92px;

        padding: 6px;

        border-right: 1px solid rgba(255,255,255,.06);

        border-bottom: 1px solid rgba(255,255,255,.06);

    }

    /* =========================
       CLASE
    ========================= */

    .class {

        height: 80px;

        border-radius: 12px;

        padding: 9px;

        background:
            linear-gradient(
                135deg,
                rgba(24,134,183,.40),
                rgba(18,69,91,.75)
            );

        border: 1px solid rgba(91,221,235,.18);

        position: relative;

        overflow: hidden;

        cursor: pointer;

        transition: .2s;

    }

    .class:hover {

        transform: translateY(-2px);

        border-color: rgba(141,244,255,.45);

        box-shadow:
            0 10px 25px rgba(0,0,0,.20);

    }

    .class::after {

        content: "";

        position: absolute;

        width: 45px;

        height: 45px;

        border-radius: 50%;

        right: -20px;

        bottom: -20px;

        background: rgba(141,244,255,.08);

    }

    .class-time {

        color: var(--cyan-light);

        font-size: 9px;

        font-weight: 800;

    }

    .class-name {

        font-family: 'Outfit';

        font-size: 12px;

        font-weight: 700;

        margin-top: 3px;

    }

    .class-info {

        display: flex;

        gap: 5px;

        flex-wrap: wrap;

        margin-top: 5px;

    }

    .tag {

        padding: 3px 6px;

        border-radius: 5px;

        font-size: 8px;

        background: rgba(255,255,255,.08);

        color: #b8d7df;

    }

    /* =========================
       PANEL INFERIOR
    ========================= */

    .bottom-grid {

        display: grid;

        grid-template-columns: 1.4fr .9fr;

        gap: 18px;

        margin-top: 20px;

    }

    .horario-panel {

        background: rgba(2,31,46,.78);

        border: 1px solid var(--border);

        border-radius: 20px;

        padding: 19px;

        backdrop-filter: blur(15px);

    }

    .panel-head {

        display: flex;

        align-items: center;

        justify-content: space-between;

        margin-bottom: 14px;

    }

    .horario-panel h3 {

        font-family: 'Outfit';

        font-size: 17px;

    }

    .panel-head span {

        color: #779ca9;

        font-size: 11px;

    }

    .class-row {

        display: flex;

        align-items: center;

        justify-content: space-between;

        padding: 12px;

        border-radius: 12px;

        background: rgba(255,255,255,.035);

        margin-bottom: 8px;

    }

    .class-row-left {

        display: flex;

        align-items: center;

        gap: 10px;

    }

    .mini-icon {

        width: 34px;

        height: 34px;

        display: grid;

        place-items: center;

        border-radius: 10px;

        background: rgba(67,217,233,.09);

    }

    .class-row strong {

        display: block;

        font-size: 12px;

    }

    .class-row small {

        color: #789aa6;

        font-size: 10px;

    }

    .horario-status {

        font-size: 9px;

        font-weight: 700;

        padding: 5px 8px;

        border-radius: 7px;

        background: rgba(67,217,233,.10);

        color: var(--cyan-light);

    }

    /* =========================
       RESPONSIVE
    ========================= */

    @media(max-width: 1100px) {

        .stats {

            grid-template-columns:
                repeat(2,1fr);

        }

        .filters {

            grid-template-columns:
                repeat(2,1fr);

        }

        .bottom-grid {

            grid-template-columns: 1fr;

        }

    }

    @media(max-width: 700px) {

        .page {

            width: 92%;

            padding-top: 18px;

        }

        .stats {

            grid-template-columns: 1fr 1fr;

        }

        .filters {

            grid-template-columns: 1fr;

        }

        .title-area h1 {

            font-size: 30px;

        }

        .calendar-header {

            align-items: flex-start;

            gap: 15px;

            flex-direction: column;

        }

        .week-controls {

            width: 100%;

        }

        .today {

            flex: 1;

        }

    }

    @media(max-width: 450px) {

        .stats {

            grid-template-columns: 1fr;

        }

        .stat strong {

            font-size: 22px;

        }

    }

</style>
@endpush

@section('content')

<div class="page">


    {{-- =========================
         TITULO
    ========================== --}}

    <div class="title-area">

        <div class="eyebrow">
            Gestión académica
        </div>

        <h1>
            Horarios y clases
        </h1>

        <p class="subtitle">
            Organiza las clases, grupos, instructores,
            carriles y horarios de cada sucursal.
        </p>

    </div>


    {{-- =========================
         ESTADISTICAS
    ========================== --}}

    <div class="stats">


        <div class="stat">

            <div class="stat-top">

                <small>
                    Clases esta semana
                </small>

                <div class="stat-icon">
                    📅
                </div>

            </div>

            <strong>
                {{ $statClasesSemana }}
            </strong>

        </div>


        <div class="stat">

            <div class="stat-top">

                <small>
                    Instructores
                </small>

                <div class="stat-icon">
                    🏊
                </div>

            </div>

            <strong>
                {{ $statInstructores }}
            </strong>

        </div>


        <div class="stat">

            <div class="stat-top">

                <small>
                    Carriles ocupados
                </small>

                <div class="stat-icon">
                    🛟
                </div>

            </div>

            <strong>
                {{ $statCarrilesOcupados }} / {{ $statCarrilesTotal }}
            </strong>

        </div>


    </div>


    {{-- =========================
         FILTROS
    ========================== --}}

    <div class="filters">


        <div class="filter">

            <label>
                Buscar
            </label>

            <input
                type="text"
                id="filtroBuscar"
                placeholder="Grupo, instructor o nivel..."
                onkeyup="filtrarClases()"
            >

        </div>


        <div class="filter">

            <label>
                Instructor
            </label>

            <select id="filtroInstructor" onchange="filtrarClases()">

                <option value="">
                    Todos
                </option>

                @foreach ($instructoresDisponibles as $instructorOpcion)
                    <option value="{{ $instructorOpcion->id }}">
                        {{ $instructorOpcion->user?->name }}
                    </option>
                @endforeach

            </select>

        </div>


        <div class="filter">

            <label>
                Nivel
            </label>

            <select id="filtroNivel" onchange="filtrarClases()">

                <option value="">
                    Todos los niveles
                </option>

                @foreach ($niveles as $nivelOpcion)
                    <option value="{{ $nivelOpcion->id }}">
                        {{ $nivelOpcion->nombre }}
                    </option>
                @endforeach

            </select>

        </div>


        <div class="filter">

            <label>
                Carril
            </label>

            <select id="filtroCarril" onchange="filtrarClases()">

                <option value="">
                    Todos
                </option>

                @foreach ($carrilesDisponibles as $carrilOpcion)
                    <option value="{{ $carrilOpcion->id }}">
                        {{ $carrilOpcion->nombre }}
                    </option>
                @endforeach

            </select>

        </div>


        <div class="filter">

            <label>
                &nbsp;
            </label>

            <button type="button" onclick="limpiarFiltros()">
                Limpiar filtros
            </button>

        </div>


    </div>


    {{-- =========================
         PARTE INFERIOR
    ========================== --}}

    <div class="bottom-grid">


        {{-- CLASES DEL DIA --}}

        <div class="horario-panel">

            <div class="panel-head">

                <h3>
                    Clases de hoy
                </h3>

                <span>
                    {{ $horariosHoy->count() }} clases programadas
                </span>

            </div>


            @forelse ($horariosHoy as $fila)
                @php($horario = $fila['horario'])
                <div class="class-row">

                    <div class="class-row-left">

                        <div class="mini-icon">
                            🏊
                        </div>

                        <div>

                            <strong>
                                {{ $horario->nombre_grupo }}
                            </strong>

                            <small>
                                {{ substr($horario->hora_inicio, 0, 5) }} · {{ $horario->carril?->nombre }} · {{ $horario->instructor?->user?->name ?? 'Sin instructor' }}
                            </small>

                        </div>

                    </div>

                    <span class="horario-status">
                        {{ $fila['estado'] }}
                    </span>

                </div>
            @empty
                <div class="class-row">
                    <span>No hay clases programadas para hoy.</span>
                </div>
            @endforelse


        </div>


        {{-- ACCIONES --}}

        <div class="horario-panel">

            <div class="panel-head">

                <h3>
                    Gestión rápida
                </h3>

            </div>


            <div class="class-row" style="cursor:pointer;" onclick="abrirModalHorario('crear')">

                <div class="class-row-left">

                    <div class="mini-icon">
                        ➕
                    </div>

                    <div>

                        <strong>
                            Nueva clase
                        </strong>

                        <small>
                            Programar una clase
                        </small>

                    </div>

                </div>

                <span class="horario-status">
                    Crear
                </span>

            </div>


            <div class="class-row" style="cursor:pointer;" onclick="abrirModalHorario('reagendar')">

                <div class="class-row-left">

                    <div class="mini-icon">
                        🔄
                    </div>

                    <div>

                        <strong>
                            Reagendar
                        </strong>

                        <small>
                            Cambiar fecha u horario
                        </small>

                    </div>

                </div>

                <span class="horario-status">
                    Gestionar
                </span>

            </div>


            <div class="class-row" style="cursor:pointer;" onclick="abrirModalHorario('asignar')">

                <div class="class-row-left">

                    <div class="mini-icon">
                        👤
                    </div>

                    <div>

                        <strong>
                            Asignar alumno
                        </strong>

                        <small>
                            Inscribir un alumno en una clase
                        </small>

                    </div>

                </div>

                <span class="horario-status">
                    Gestionar
                </span>

            </div>


            <div class="class-row" style="cursor:pointer;" onclick="abrirModalHorario('cambiar-grupo')">

                <div class="class-row-left">

                    <div class="mini-icon">
                        🔀
                    </div>

                    <div>

                        <strong>
                            Cambiar grupo
                        </strong>

                        <small>
                            Mover alumno o grupo
                        </small>

                    </div>

                </div>

                <span class="horario-status">
                    Gestionar
                </span>

            </div>


        </div>


    </div>


    {{-- =========================
         CALENDARIO
    ========================== --}}

    <div class="calendar-card">


        <div class="calendar-header">


            <div class="calendar-title">

                <div class="calendar-title-icon">
                    📅
                </div>

                <div>

                    <h2>
                        Semana actual
                    </h2>

                    <p>
                        {{ $semanaInicio->translatedFormat('d') }} — {{ $semanaFin->translatedFormat('d \d\e F, Y') }}
                    </p>

                </div>

            </div>


            <div class="week-controls">

                <button>
                    ‹
                </button>

                <button class="today">
                    Hoy
                </button>

                <button>
                    ›
                </button>

            </div>


        </div>


        <div class="calendar-wrapper">


            <div class="calendar">


                <div class="corner"></div>

                @foreach ($dias as $fila)
                    <div class="day {{ $fila['esHoy'] ? 'active' : '' }}">

                        <span>
                            {{ \Illuminate\Support\Str::limit($fila['dia']->label(), 3, '') }}
                        </span>

                        <strong>
                            {{ $fila['fecha']->format('d') }}
                        </strong>

                    </div>
                @endforeach



                @foreach ($horas as $hora)
                    <div class="time">
                        {{ $hora }}
                    </div>

                    @foreach ($dias as $fila)
                        <div class="slot">
                            @foreach ($grid[$hora][$fila['dia']->value] as $horario)
                                <div
                                    class="class"
                                    data-instructor="{{ $horario->instructor_id }}"
                                    data-nivel="{{ $horario->nivel_id }}"
                                    data-carril="{{ $horario->carril_id }}"
                                    data-search="{{ mb_strtolower($horario->nombre_grupo.' '.$horario->instructor?->user?->name.' '.$horario->nivel?->nombre) }}">

                                    <div class="class-time">
                                        {{ substr($horario->hora_inicio, 0, 5) }} — {{ substr($horario->hora_fin, 0, 5) }}
                                    </div>

                                    <div class="class-name">
                                        {{ $horario->nombre_grupo }}
                                    </div>

                                    <div class="class-info">

                                        <span class="tag">
                                            {{ $horario->nivel?->nombre }}
                                        </span>

                                        <span class="tag">
                                            {{ $horario->carril?->nombre }}
                                        </span>

                                    </div>

                                </div>
                            @endforeach
                        </div>
                    @endforeach
                @endforeach



            </div>

        </div>

    </div>




</div>


{{-- =========================
     MODALES DE GESTIÓN
========================== --}}

<div id="modalOverlay" style="display:none;position:fixed;inset:0;z-index:200;background:rgba(1,15,23,.7);align-items:center;justify-content:center;">

    <div style="width:100%;max-width:520px;max-height:90vh;overflow-y:auto;background:#052d40;border:1px solid rgba(65,208,235,.25);border-radius:20px;padding:28px;">

        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;">
            <h2 id="modalHorarioTitulo" style="font-size:20px;">Nueva clase</h2>
            <button type="button" onclick="cerrarModalHorario()" style="background:none;border:none;color:white;font-size:22px;cursor:pointer;">×</button>
        </div>

        @if ($errors->any())
            <div style="margin-bottom:14px;padding:12px 16px;border-radius:12px;background:rgba(255,95,109,.10);border:1px solid rgba(255,95,109,.35);color:#ff9aa1;font-size:12px;">
                <ul style="margin:0 0 0 16px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <?php
            $campoEstilo = 'width:100%;margin-top:6px;margin-bottom:14px;height:48px;padding:0 14px;border-radius:12px;border:1px solid rgba(83,214,238,.20);background:#042337;color:white;';
        ?>

        {{-- FORMULARIO: NUEVA CLASE --}}
        <form id="formCrearClase" method="POST" action="{{ route('horarios.store') }}" style="display:none;">
            @csrf

            <label>Nombre del grupo</label>
            <input type="text" name="nombre_grupo" required style="{{ $campoEstilo }}" placeholder="Ej. Delfines">

            @if ($esVistaGlobal)
                <label>Sucursal</label>
                <select name="sucursal_id" required style="{{ $campoEstilo }}">
                    <option value="">Seleccionar sucursal</option>
                    @foreach ($sucursalesTodas as $sucursalOpcion)
                        <option value="{{ $sucursalOpcion->id }}">{{ $sucursalOpcion->nombre }}</option>
                    @endforeach
                </select>
            @endif

            <label>Nivel</label>
            <select name="nivel_id" required style="{{ $campoEstilo }}">
                <option value="">Seleccionar nivel</option>
                @foreach ($niveles as $nivelOpcion)
                    <option value="{{ $nivelOpcion->id }}">{{ $nivelOpcion->nombre }}</option>
                @endforeach
            </select>

            <label>Instructor</label>
            <select name="instructor_id" required style="{{ $campoEstilo }}">
                <option value="">Seleccionar instructor</option>
                @foreach ($instructoresDisponibles as $instructorOpcion)
                    <option value="{{ $instructorOpcion->id }}">
                        {{ $instructorOpcion->user?->name }}{{ $esVistaGlobal ? ' · '.$instructorOpcion->sucursal?->nombre : '' }}
                    </option>
                @endforeach
            </select>

            <label>Carril</label>
            <select name="carril_id" required style="{{ $campoEstilo }}">
                <option value="">Seleccionar carril</option>
                @foreach ($carrilesDisponibles as $carrilOpcion)
                    <option value="{{ $carrilOpcion->id }}">
                        {{ $carrilOpcion->nombre }}{{ $esVistaGlobal ? ' · '.$carrilOpcion->sucursal?->nombre : '' }}
                    </option>
                @endforeach
            </select>

            <label>Día de la semana</label>
            <select name="dia_semana" required style="{{ $campoEstilo }}">
                @foreach ($diasSemana as $diaOpcion)
                    <option value="{{ $diaOpcion->value }}">{{ $diaOpcion->label() }}</option>
                @endforeach
            </select>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                <div>
                    <label>Hora inicio</label>
                    <input type="time" name="hora_inicio" required style="{{ $campoEstilo }}">
                </div>
                <div>
                    <label>Hora fin</label>
                    <input type="time" name="hora_fin" required style="{{ $campoEstilo }}">
                </div>
            </div>

            <label>Capacidad máxima</label>
            <input type="number" name="capacidad_maxima" min="1" max="50" value="10" required style="{{ $campoEstilo }}">

            <div style="display:flex;gap:12px;margin-top:6px;">
                <button type="submit" class="btn-modal-submit">Crear clase</button>
            </div>
        </form>

        {{-- FORMULARIO: REAGENDAR --}}
        <form id="formReagendar" method="POST" style="display:none;">
            @csrf
            @method('PATCH')

            <label>Clase a reagendar</label>
            <select name="_horario_id" required style="{{ $campoEstilo }}" onchange="this.form.action = '/horarios/' + this.value + '/reagendar';">
                <option value="">Seleccionar clase</option>
                @foreach ($horariosExistentes as $horarioOpcion)
                    <option value="{{ $horarioOpcion->id }}">
                        {{ $horarioOpcion->nombre_grupo }} · {{ $horarioOpcion->dia_semana->label() }} {{ substr($horarioOpcion->hora_inicio, 0, 5) }}
                    </option>
                @endforeach
            </select>

            <label>Nuevo día</label>
            <select name="dia_semana" required style="{{ $campoEstilo }}">
                @foreach ($diasSemana as $diaOpcion)
                    <option value="{{ $diaOpcion->value }}">{{ $diaOpcion->label() }}</option>
                @endforeach
            </select>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                <div>
                    <label>Nueva hora inicio</label>
                    <input type="time" name="hora_inicio" required style="{{ $campoEstilo }}">
                </div>
                <div>
                    <label>Nueva hora fin</label>
                    <input type="time" name="hora_fin" required style="{{ $campoEstilo }}">
                </div>
            </div>

            <label>Carril</label>
            <select name="carril_id" required style="{{ $campoEstilo }}">
                <option value="">Seleccionar carril</option>
                @foreach ($carrilesDisponibles as $carrilOpcion)
                    <option value="{{ $carrilOpcion->id }}">{{ $carrilOpcion->nombre }}</option>
                @endforeach
            </select>

            <div style="display:flex;gap:12px;margin-top:6px;">
                <button type="submit" class="btn-modal-submit">Guardar cambios</button>
            </div>
        </form>

        {{-- FORMULARIO: ASIGNAR ALUMNO --}}
        <form id="formAsignarAlumno" method="POST" action="{{ route('inscripciones.store') }}" style="display:none;">
            @csrf

            <label>Alumno</label>
            <select name="alumno_id" required style="{{ $campoEstilo }}">
                <option value="">Seleccionar alumno</option>
                @foreach ($alumnosParaAsignar as $alumnoOpcion)
                    <option value="{{ $alumnoOpcion->id }}">{{ $alumnoOpcion->nombreCompleto() }}</option>
                @endforeach
            </select>

            <label>Grupo / horario</label>
            <select name="horario_id" required style="{{ $campoEstilo }}">
                <option value="">Seleccionar grupo</option>
                @foreach ($horariosExistentes as $horarioOpcion)
                    <option value="{{ $horarioOpcion->id }}">
                        {{ $horarioOpcion->nombre_grupo }} · {{ $horarioOpcion->dia_semana->label() }} {{ substr($horarioOpcion->hora_inicio, 0, 5) }}
                    </option>
                @endforeach
            </select>

            <p style="color:var(--muted); font-size:12px; margin:-4px 0 12px;">
                Puedes asignar a un alumno a varias clases distintas de la semana; no hace falta que esté libre de otras inscripciones.
            </p>

            <div style="display:flex;gap:12px;margin-top:6px;">
                <button type="submit" class="btn-modal-submit">Asignar alumno</button>
            </div>
        </form>

        {{-- FORMULARIO: CAMBIAR GRUPO --}}
        <form id="formCambiarGrupo" method="POST" action="{{ route('inscripciones.cambiar-grupo') }}" style="display:none;">
            @csrf
            @method('PATCH')

            <label>Alumno</label>
            <select name="alumno_id" required style="{{ $campoEstilo }}">
                <option value="">Seleccionar alumno</option>
                @foreach ($alumnosConInscripcion as $alumnoOpcion)
                    <option value="{{ $alumnoOpcion->id }}">{{ $alumnoOpcion->nombreCompleto() }}</option>
                @endforeach
            </select>

            <label>Nuevo grupo / horario</label>
            <select name="horario_id" required style="{{ $campoEstilo }}">
                <option value="">Seleccionar grupo</option>
                @foreach ($horariosExistentes as $horarioOpcion)
                    <option value="{{ $horarioOpcion->id }}">
                        {{ $horarioOpcion->nombre_grupo }} · {{ $horarioOpcion->dia_semana->label() }} {{ substr($horarioOpcion->hora_inicio, 0, 5) }}
                    </option>
                @endforeach
            </select>

            <div style="display:flex;gap:12px;margin-top:6px;">
                <button type="submit" class="btn-modal-submit">Mover alumno</button>
            </div>
        </form>

    </div>

</div>

@endsection

@push('scripts')
<script>

function filtrarClases() {

    const texto = document.getElementById('filtroBuscar').value.toLowerCase();
    const instructor = document.getElementById('filtroInstructor').value;
    const nivel = document.getElementById('filtroNivel').value;
    const carril = document.getElementById('filtroCarril').value;

    document.querySelectorAll('.calendar .class').forEach(function (clase) {
        const coincideTexto = !texto || clase.dataset.search.includes(texto);
        const coincideInstructor = !instructor || clase.dataset.instructor === instructor;
        const coincideNivel = !nivel || clase.dataset.nivel === nivel;
        const coincideCarril = !carril || clase.dataset.carril === carril;

        clase.style.display = (coincideTexto && coincideInstructor && coincideNivel && coincideCarril) ? '' : 'none';
    });
}

function limpiarFiltros() {
    document.getElementById('filtroBuscar').value = '';
    document.getElementById('filtroInstructor').value = '';
    document.getElementById('filtroNivel').value = '';
    document.getElementById('filtroCarril').value = '';
    filtrarClases();
}

function abrirModalHorario(tipo) {

    document.getElementById('formCrearClase').style.display = 'none';
    document.getElementById('formReagendar').style.display = 'none';
    document.getElementById('formAsignarAlumno').style.display = 'none';
    document.getElementById('formCambiarGrupo').style.display = 'none';

    const titulo = document.getElementById('modalHorarioTitulo');

    if (tipo === 'crear') {
        titulo.textContent = 'Nueva clase';
        document.getElementById('formCrearClase').style.display = 'block';
    } else if (tipo === 'reagendar') {
        titulo.textContent = 'Reagendar clase';
        document.getElementById('formReagendar').style.display = 'block';
    } else if (tipo === 'asignar') {
        titulo.textContent = 'Asignar alumno a una clase';
        document.getElementById('formAsignarAlumno').style.display = 'block';
    } else {
        titulo.textContent = 'Cambiar grupo';
        document.getElementById('formCambiarGrupo').style.display = 'block';
    }

    document.getElementById('modalOverlay').style.display = 'flex';
}

function cerrarModalHorario() {
    document.getElementById('modalOverlay').style.display = 'none';
}

@if ($errors->any())
    document.addEventListener('DOMContentLoaded', function () { abrirModalHorario('crear'); });
@endif

</script>
@endpush
