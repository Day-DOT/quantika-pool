<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Horarios | QUANTIKA POOL</title>

    <link rel="preconnect"
          href="https://fonts.googleapis.com">

    <link rel="preconnect"
          href="https://fonts.gstatic.com"
          crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap"
          rel="stylesheet">

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {

            font-family: 'DM Sans', sans-serif;

            background:
                linear-gradient(
                    rgba(2, 25, 39, .90),
                    rgba(3, 48, 67, .94)
                ),
                url('/images/quantika-pool-bg.jpg');

            background-size: cover;
            background-position: center;
            background-attachment: fixed;

            min-height: 100vh;

            color: #eafaff;

        }

        :root {

            --cyan: #43d9e9;
            --cyan-light: #8df4ff;
            --blue: #1686b7;

            --dark: #022536;
            --dark-2: #07374c;

            --glass: rgba(3, 38, 56, .72);

            --border: rgba(126, 231, 245, .18);

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
           HEADER
        ========================= */

        .topbar {

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 20px;

            margin-bottom: 22px;

        }

        .back {

            display: inline-flex;

            align-items: center;

            gap: 9px;

            color: #dffaff;

            text-decoration: none;

            font-size: 14px;

            font-weight: 600;

            padding: 10px 15px;

            border-radius: 12px;

            background: rgba(255,255,255,.07);

            border: 1px solid var(--border);

            transition: .2s;

        }

        .back:hover {

            background: rgba(67,217,233,.12);

            transform: translateX(-2px);

        }

        .branch {

            display: flex;

            align-items: center;

            gap: 12px;

        }

        .branch-label {

            font-size: 12px;

            color: #8eb4c3;

        }

        .branch-select {

            border: 1px solid var(--border);

            background: rgba(2,30,44,.8);

            color: white;

            padding: 11px 15px;

            border-radius: 12px;

            outline: none;

            font-weight: 600;

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

        h1 {

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

        .stat-top {

            display: flex;

            justify-content: space-between;

            align-items: center;

            margin-bottom: 9px;

        }

        .stat-icon {

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

        .panel {

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

        .panel h3 {

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

        .status {

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

            .topbar {

                align-items: flex-start;

                flex-direction: column;

            }

            .branch {

                width: 100%;

            }

            .branch-select {

                width: 100%;

            }

            .stats {

                grid-template-columns: 1fr 1fr;

            }

            .filters {

                grid-template-columns: 1fr;

            }

            h1 {

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

</head>


<body>


<div class="page">


    {{-- =========================
         HEADER
    ========================== --}}

    <div class="topbar">

        <a href="{{ route('admin.dashboard') }}"
           class="back">

            ← Regresar al dashboard

        </a>


        <div class="branch">

            <span class="branch-label">
                Sucursal activa
            </span>

            <select class="branch-select">

                <option>
                    QUANTIKA POOL · SUCURSAL 1
                </option>

                <option>
                    QUANTIKA · SUCURSAL 2
                </option>

            </select>

        </div>

    </div>


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
                48
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
                8
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
                6 / 8
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
                placeholder="Grupo, instructor o alumno..."
            >

        </div>


        <div class="filter">

            <label>
                Instructor
            </label>

            <select>

                <option>
                    Todos
                </option>

                <option>
                    Instructor 1
                </option>

                <option>
                    Instructor 2
                </option>

            </select>

        </div>


        <div class="filter">

            <label>
                Nivel
            </label>

            <select>

                <option>
                    Todos los niveles
                </option>

                <option>
                    Principiante
                </option>

                <option>
                    Intermedio
                </option>

                <option>
                    Avanzado
                </option>

            </select>

        </div>


        <div class="filter">

            <label>
                Carril
            </label>

            <select>

                <option>
                    Todos
                </option>

                <option>
                    Carril 1
                </option>

                <option>
                    Carril 2
                </option>

                <option>
                    Carril 3
                </option>

                <option>
                    Carril 4
                </option>

            </select>

        </div>


        <div class="filter">

            <label>
                &nbsp;
            </label>

            <button>
                Filtrar
            </button>

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
                        17 — 23 de agosto, 2026
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


                <div class="day active">

                    <span>
                        Lun
                    </span>

                    <strong>
                        17
                    </strong>

                </div>


                <div class="day">

                    <span>
                        Mar
                    </span>

                    <strong>
                        18
                    </strong>

                </div>


                <div class="day">

                    <span>
                        Mié
                    </span>

                    <strong>
                        19
                    </strong>

                </div>


                <div class="day">

                    <span>
                        Jue
                    </span>

                    <strong>
                        20
                    </strong>

                </div>


                <div class="day">

                    <span>
                        Vie
                    </span>

                    <strong>
                        21
                    </strong>

                </div>


                <div class="day">

                    <span>
                        Sáb
                    </span>

                    <strong>
                        22
                    </strong>

                </div>


                <div class="day">

                    <span>
                        Dom
                    </span>

                    <strong>
                        23
                    </strong>

                </div>


                {{-- 08:00 --}}

                <div class="time">
                    08:00
                </div>

                <div class="slot">

                    <div class="class">

                        <div class="class-time">
                            08:00 — 09:00
                        </div>

                        <div class="class-name">
                            Delfines
                        </div>

                        <div class="class-info">

                            <span class="tag">
                                Principiante
                            </span>

                            <span class="tag">
                                C1
                            </span>

                        </div>

                    </div>

                </div>

                <div class="slot"></div>

                <div class="slot">

                    <div class="class">

                        <div class="class-time">
                            08:00 — 09:00
                        </div>

                        <div class="class-name">
                            Tiburones
                        </div>

                        <div class="class-info">

                            <span class="tag">
                                Intermedio
                            </span>

                            <span class="tag">
                                C2
                            </span>

                        </div>

                    </div>

                </div>

                <div class="slot"></div>

                <div class="slot">

                    <div class="class">

                        <div class="class-time">
                            08:00 — 09:00
                        </div>

                        <div class="class-name">
                            Orcas
                        </div>

                        <div class="class-info">

                            <span class="tag">
                                Avanzado
                            </span>

                            <span class="tag">
                                C3
                            </span>

                        </div>

                    </div>

                </div>

                <div class="slot"></div>


                {{-- 09:00 --}}

                <div class="time">
                    09:00
                </div>

                <div class="slot"></div>

                <div class="slot">

                    <div class="class">

                        <div class="class-time">
                            09:00 — 10:00
                        </div>

                        <div class="class-name">
                            Caballitos
                        </div>

                        <div class="class-info">

                            <span class="tag">
                                Principiante
                            </span>

                            <span class="tag">
                                C1
                            </span>

                        </div>

                    </div>

                </div>

                <div class="slot"></div>

                <div class="slot"></div>

                <div class="slot">

                    <div class="class">

                        <div class="class-time">
                            09:00 — 10:00
                        </div>

                        <div class="class-name">
                            Mantarrayas
                        </div>

                        <div class="class-info">

                            <span class="tag">
                                Intermedio
                            </span>

                            <span class="tag">
                                C2
                            </span>

                        </div>

                    </div>

                </div>

                <div class="slot"></div>


                {{-- 10:00 --}}

                <div class="time">
                    10:00
                </div>

                <div class="slot">

                    <div class="class">

                        <div class="class-time">
                            10:00 — 11:00
                        </div>

                        <div class="class-name">
                            Estrellitas
                        </div>

                        <div class="class-info">

                            <span class="tag">
                                Principiante
                            </span>

                            <span class="tag">
                                C2
                            </span>

                        </div>

                    </div>

                </div>

                <div class="slot"></div>

                <div class="slot">

                    <div class="class">

                        <div class="class-time">
                            10:00 — 11:00
                        </div>

                        <div class="class-name">
                            Delfines
                        </div>

                        <div class="class-info">

                            <span class="tag">
                                Intermedio
                            </span>

                            <span class="tag">
                                C1
                            </span>

                        </div>

                    </div>

                </div>

                <div class="slot"></div>

                <div class="slot"></div>

                <div class="slot">

                    <div class="class">

                        <div class="class-time">
                            10:00 — 11:00
                        </div>

                        <div class="class-name">
                            Orcas
                        </div>

                        <div class="class-info">

                            <span class="tag">
                                Avanzado
                            </span>

                            <span class="tag">
                                C3
                            </span>

                        </div>

                    </div>

                </div>

                <div class="slot"></div>


                {{-- 11:00 --}}

                <div class="time">
                    11:00
                </div>

                <div class="slot"></div>

                <div class="slot">

                    <div class="class">

                        <div class="class-time">
                            11:00 — 12:00
                        </div>

                        <div class="class-name">
                            Tiburones
                        </div>

                        <div class="class-info">

                            <span class="tag">
                                Intermedio
                            </span>

                            <span class="tag">
                                C2
                            </span>

                        </div>

                    </div>

                </div>

                <div class="slot"></div>

                <div class="slot">

                    <div class="class">

                        <div class="class-time">
                            11:00 — 12:00
                        </div>

                        <div class="class-name">
                            Delfines
                        </div>

                        <div class="class-info">

                            <span class="tag">
                                Avanzado
                            </span>

                            <span class="tag">
                                C4
                            </span>

                        </div>

                    </div>

                </div>

                <div class="slot"></div>

                <div class="slot"></div>

                <div class="slot"></div>


            </div>

        </div>

    </div>


    {{-- =========================
         PARTE INFERIOR
    ========================== --}}

    <div class="bottom-grid">


        {{-- CLASES DEL DIA --}}

        <div class="panel">

            <div class="panel-head">

                <h3>
                    Clases de hoy
                </h3>

                <span>
                    8 clases programadas
                </span>

            </div>


            <div class="class-row">

                <div class="class-row-left">

                    <div class="mini-icon">
                        🐬
                    </div>

                    <div>

                        <strong>
                            Delfines
                        </strong>

                        <small>
                            08:00 · Carril 1 · Instructor
                        </small>

                    </div>

                </div>

                <span class="status">
                    Programada
                </span>

            </div>


            <div class="class-row">

                <div class="class-row-left">

                    <div class="mini-icon">
                        🦈
                    </div>

                    <div>

                        <strong>
                            Tiburones
                        </strong>

                        <small>
                            09:00 · Carril 2 · Instructor
                        </small>

                    </div>

                </div>

                <span class="status">
                    Programada
                </span>

            </div>


            <div class="class-row">

                <div class="class-row-left">

                    <div class="mini-icon">
                        🐋
                    </div>

                    <div>

                        <strong>
                            Orcas
                        </strong>

                        <small>
                            10:00 · Carril 3 · Instructor
                        </small>

                    </div>

                </div>

                <span class="status">
                    Programada
                </span>

            </div>


        </div>


        {{-- ACCIONES --}}

        <div class="panel">

            <div class="panel-head">

                <h3>
                    Gestión rápida
                </h3>

            </div>


            <div class="class-row">

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

                <span class="status">
                    Crear
                </span>

            </div>


            <div class="class-row">

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

                <span class="status">
                    Gestionar
                </span>

            </div>


            <div class="class-row">

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

                <span class="status">
                    Gestionar
                </span>

            </div>


        </div>


    </div>


</div>


</body>

</html>