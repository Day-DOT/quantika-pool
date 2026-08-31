<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Portal del instructor') · Quantika Pool</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <style>

        :root {
            --bg: #021d2b;
            --bg-dark: #011622;
            --sidebar: #032537;
            --card: #07364a;
            --card-2: #062f42;
            --border: rgba(63, 207, 237, .20);

            --cyan: #42d5ee;
            --cyan-dark: #16b9d7;

            --text: #f5fbff;
            --muted: #82a7b8;
            --muted-2: #9db5c2;

            --green: #16e0a4;
            --yellow: #ffbd20;
            --red: #ff6b6b;
            --purple: #c05cff;
            --blue: #21c7ff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            min-height: 100vh;
            background:
                radial-gradient(circle at 80% 10%, rgba(16, 113, 145, .15), transparent 35%),
                var(--bg);
            color: var(--text);
            font-family: Arial, Helvetica, sans-serif;
            overflow-x: hidden;
        }

        button, input, select, textarea { font-family: inherit; }
        a { color: inherit; text-decoration: none; }

        /* =========================================
           LAYOUT GENERAL
        ========================================= */

        .quantika-app { min-height: 100vh; display: flex; }

        /* =========================================
           SIDEBAR
        ========================================= */

        .sidebar {
            width: 255px;
            min-width: 255px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: linear-gradient(180deg, #052b3e 0%, #032638 55%, #021d2c 100%);
            border-right: 1px solid rgba(67, 206, 235, .14);
            display: flex;
            flex-direction: column;
            z-index: 100;
        }

        .sidebar-logo {
            height: 175px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 25px;
            border-bottom: 1px solid rgba(255,255,255,.07);
        }

        .sidebar-logo img { width: 190px; max-width: 100%; height: auto; object-fit: contain; }

        .sidebar-user {
            margin: 24px 22px 18px;
            padding: 18px;
            border-radius: 22px;
            background: linear-gradient(135deg, rgba(20, 89, 116, .60), rgba(4, 44, 65, .65));
            border: 1px solid rgba(76, 213, 239, .17);
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .avatar {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--cyan);
            color: #043047;
            font-size: 18px;
            font-weight: 900;
            flex-shrink: 0;
            box-shadow: 0 8px 25px rgba(66, 216, 239, .18);
        }

        .sidebar-user strong { display: block; font-size: 15px; font-weight: 800; }
        .sidebar-user span { display: block; color: var(--muted); font-size: 11px; margin-top: 4px; }

        .sidebar-scroll {
            flex: 1;
            overflow-y: auto;
            padding: 5px 15px 20px;
            scrollbar-width: thin;
            scrollbar-color: #176a83 transparent;
        }

        .menu-title {
            padding: 14px 15px 10px;
            color: #6f96a9;
            font-size: 11px;
            letter-spacing: 3px;
            font-weight: 800;
        }

        .menu { display: flex; flex-direction: column; gap: 6px; }

        .menu-item {
            min-height: 50px;
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 8px 14px;
            border-radius: 17px;
            color: #8eabba;
            font-size: 14px;
            font-weight: 700;
            transition: .25s ease;
            position: relative;
        }

        .menu-item:hover { background: rgba(39, 124, 153, .17); color: #fff; transform: translateX(2px); }

        .menu-item.active {
            color: var(--cyan);
            background: linear-gradient(90deg, rgba(29, 137, 165, .32), rgba(17, 78, 102, .40));
        }

        .menu-item.active::before {
            content: "";
            position: absolute;
            left: 0;
            top: 7px;
            bottom: 7px;
            width: 4px;
            background: var(--cyan);
            border-radius: 0 10px 10px 0;
            box-shadow: 0 0 12px rgba(66, 216, 239, .6);
        }

        .menu-icon {
            width: 40px;
            height: 40px;
            border-radius: 13px;
            background: rgba(24, 99, 126, .30);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }

        .menu-item.active .menu-icon { background: var(--cyan); color: #053147; }

        .sidebar-footer {
            height: 54px;
            border-top: 1px solid rgba(255,255,255,.07);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #9db5c2;
            font-size: 11px;
        }

        /* =========================================
           CONTENIDO / TOPBAR
        ========================================= */

        .main { width: calc(100% - 255px); margin-left: 255px; min-height: 100vh; }

        .topbar {
            height: 92px;
            padding: 0 34px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            border-bottom: 1px solid rgba(255,255,255,.07);
            background: rgba(2, 28, 42, .88);
            backdrop-filter: blur(14px);
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .page-title small {
            display: block;
            color: var(--cyan);
            font-size: 12px;
            font-weight: 900;
            letter-spacing: 4px;
            margin-bottom: 6px;
        }

        .page-title h1 { font-size: 28px; line-height: 1; font-weight: 900; }

        .top-actions { display: flex; align-items: center; gap: 12px; }

        .top-user {
            height: 54px;
            padding: 5px 15px 5px 6px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-radius: 18px;
            background: rgba(8, 57, 77, .78);
            border: 1px solid rgba(66,216,239,.16);
            font-weight: 800;
            font-size: 14px;
        }

        .top-user .avatar { width: 42px; height: 42px; border-radius: 13px; font-size: 13px; }

        form.logout-form { margin: 0; }

        .btn-logout {
            width: 54px;
            height: 54px;
            border-radius: 18px;
            background: rgba(8, 57, 77, .78);
            border: 1px solid rgba(66,216,239,.16);
            color: var(--text);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            cursor: pointer;
        }

        .btn-logout:hover { background: rgba(255,107,107,.14); border-color: rgba(255,107,107,.35); }

        .content { padding: 32px; max-width: 1500px; margin: auto; }

        /* =========================================
           ALERTAS
        ========================================= */

        .alert {
            padding: 16px 20px;
            border-radius: 16px;
            margin-bottom: 22px;
            font-size: 13px;
            font-weight: 700;
            border: 1px solid;
        }

        .alert-success { background: rgba(20,231,173,.08); border-color: rgba(20,231,173,.30); color: var(--green); }
        .alert-error { background: rgba(255,107,107,.08); border-color: rgba(255,107,107,.30); color: var(--red); }

        /* =========================================
           ESTADÍSTICAS
        ========================================= */

        .stats-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; margin-bottom: 8px; }

        /* Variantes de número de columnas: se usan como clase (`.cols-N`) en
           vez de un `style` inline, para que estas reglas @media sí puedan
           ganarle en cascada. */
        .stats-grid.cols-1 { grid-template-columns: repeat(1, minmax(0, 1fr)); }
        .stats-grid.cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .stats-grid.cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .stats-grid.cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }

        .stat-card {
            min-height: 150px;
            padding: 21px;
            border-radius: 20px;
            background: linear-gradient(145deg, rgba(7,54,74,.96), rgba(4,42,59,.94));
            border: 1px solid rgba(66,213,238,.17);
            position: relative;
            overflow: hidden;
            transition: .2s ease;
        }

        .stat-card:hover { transform: translateY(-3px); border-color: rgba(66,213,238,.35); }

        .stat-card::after {
            content: "";
            width: 95px;
            height: 95px;
            border-radius: 50%;
            position: absolute;
            right: -35px;
            bottom: -35px;
            background: rgba(66,213,238,.06);
        }

        .stat-top { display: flex; align-items: center; justify-content: space-between; gap: 8px; }
        .stat-name { color: #82aebf; font-size: 12px; font-weight: 800; }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(66,213,238,.10);
            color: var(--cyan);
            font-size: 18px;
        }

        .stat-value { margin-top: 15px; font-size: 31px; font-weight: 900; }
        .stat-change { margin-top: 7px; color: var(--muted); font-size: 10px; font-weight: 800; }

        /* =========================================
           SECCIONES / TARJETAS
        ========================================= */

        .section-header { display: flex; justify-content: space-between; align-items: center; margin: 30px 0 14px; gap: 12px; flex-wrap: wrap; }
        .section-header h3 { font-size: 19px; font-weight: 900; }
        .section-link { color: var(--cyan); font-size: 11px; font-weight: 800; }

        .card {
            background: linear-gradient(145deg, rgba(7, 51, 70, .97), rgba(4, 35, 53, .97));
            border: 1px solid rgba(65, 210, 237, .15);
            border-radius: 22px;
            overflow: hidden;
        }

        .card-pad { padding: 26px; }

        .empty-state {
            padding: 50px 30px;
            text-align: center;
            color: var(--muted);
        }

        .empty-state strong { display: block; color: var(--text); font-size: 16px; margin-bottom: 8px; }

        /* =========================================
           BADGES / PROGRESO
        ========================================= */

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 10px;
            border-radius: 30px;
            font-size: 9px;
            font-weight: 800;
            letter-spacing: .3px;
            border: 1px solid;
            white-space: nowrap;
        }

        .badge-green { color: #16e6aa; background: rgba(20,231,173,.08); border-color: rgba(20,231,173,.25); }
        .badge-yellow { color: var(--yellow); background: rgba(255,194,41,.08); border-color: rgba(255,194,41,.28); }
        .badge-red { color: var(--red); background: rgba(255,107,107,.08); border-color: rgba(255,107,107,.28); }
        .badge-muted { color: var(--muted); background: rgba(134,170,189,.08); border-color: rgba(134,170,189,.25); }
        .badge-cyan { color: var(--cyan); background: rgba(66,216,239,.08); border-color: rgba(66,216,239,.28); }

        .progress { width: 100%; height: 8px; background: #123c4e; border-radius: 20px; overflow: hidden; }

        .progress span {
            display: block;
            height: 100%;
            border-radius: inherit;
            background: var(--cyan);
            box-shadow: 0 0 10px rgba(66,216,239,.45);
        }

        /* =========================================
           TABLAS
        ========================================= */

        .table-wrap { overflow-x: auto; }

        table.quantika-table { width: 100%; border-collapse: collapse; }

        table.quantika-table th {
            padding: 14px 18px;
            text-align: left;
            color: #688b9d;
            font-size: 9px;
            letter-spacing: 1.5px;
            font-weight: 900;
            border-bottom: 1px solid rgba(255,255,255,.07);
            white-space: nowrap;
        }

        table.quantika-table td {
            padding: 14px 18px;
            border-bottom: 1px solid rgba(255,255,255,.05);
            font-size: 13px;
            vertical-align: middle;
        }

        table.quantika-table tr:last-child td { border-bottom: none; }
        table.quantika-table tr:hover td { background: rgba(47, 207, 235, .03); }

        .person { display: flex; align-items: center; gap: 10px; }

        .person .avatar { width: 36px; height: 36px; border-radius: 11px; font-size: 11px; }

        .person-name { font-weight: 800; }
        .person-sub { color: #9db5c2; font-size: 10px; margin-top: 2px; }

        /* =========================================
           BOTONES
        ========================================= */

        .btn {
            min-height: 48px;
            padding: 0 21px;
            border-radius: 13px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            font-size: 14px;
            font-weight: 900;
            transition: .2s ease;
            border: none;
            cursor: pointer;
        }

        .btn-primary { background: var(--cyan); color: #023146; box-shadow: 0 10px 25px rgba(66,213,238,.18); }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 14px 30px rgba(66,213,238,.25); }

        .btn-outline { border: 1px solid rgba(66,213,238,.40); color: white; background: rgba(2,29,43,.25); }
        .btn-outline:hover { background: rgba(66,213,238,.10); }

        .btn-sm { min-height: 36px; padding: 0 14px; font-size: 11px; border-radius: 11px; }

        .btn-yes { background: var(--green); color: #063327; }
        .btn-no { background: var(--red); color: #390606; }
        .btn-yes:hover, .btn-no:hover { transform: translateY(-2px); }

        .icon-btn {
            width: 38px;
            height: 38px;
            border-radius: 11px;
            border: 1px solid rgba(255,255,255,.08);
            background: #08384d;
            color: white;
            cursor: pointer;
        }

        .icon-btn:hover { background: #0c526a; }

        /* =========================================
           FORMULARIOS
        ========================================= */

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .5px;
            color: var(--muted);
            text-transform: uppercase;
        }

        input[type=text], input[type=date], input[type=number], select, textarea {
            width: 100%;
            min-height: 48px;
            padding: 0 15px;
            border-radius: 12px;
            border: 1px solid rgba(83,214,238,.18);
            background: #042337;
            color: white;
            font-size: 14px;
        }

        textarea { min-height: 90px; padding: 12px 15px; resize: vertical; }

        input:focus, select:focus, textarea:focus { outline: none; border-color: var(--cyan); }

        .field { margin-bottom: 18px; }

        .estado-select[data-estado="no_iniciado"] { border-color: rgba(134,170,189,.35); }
        .estado-select[data-estado="en_proceso"] { border-color: rgba(255,194,41,.45); }
        .estado-select[data-estado="logrado"] { border-color: rgba(20,231,173,.45); }

        /* =========================================
           GRUPOS / AGENDA
        ========================================= */

        .dia-block { margin-bottom: 26px; }

        .dia-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
        }

        .dia-header h4 { font-size: 16px; font-weight: 800; }
        .dia-header .count { color: var(--muted); font-size: 11px; }
        .dia-header.today h4 { color: var(--cyan); }

        .grupos-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; }

        .grupo-card {
            padding: 18px;
            border-radius: 18px;
            background: linear-gradient(145deg, rgba(7, 51, 70, .95), rgba(4, 37, 55, .95));
            border: 1px solid rgba(65, 210, 237, .15);
            transition: .2s ease;
            display: block;
        }

        .grupo-card:hover { border-color: rgba(65, 210, 237, .35); transform: translateY(-3px); }

        .grupo-nombre { font-weight: 800; font-size: 15px; margin-bottom: 6px; }
        .grupo-meta { color: var(--muted); font-size: 11px; margin-bottom: 3px; }

        .criterio-row {
            display: grid;
            grid-template-columns: 1.4fr 1fr 1.4fr;
            gap: 14px;
            align-items: start;
            padding: 16px 0;
            border-bottom: 1px solid rgba(255,255,255,.06);
        }

        .criterio-row:last-child { border-bottom: none; }
        .criterio-nombre { font-weight: 800; font-size: 14px; margin-bottom: 4px; }
        .criterio-desc { color: var(--muted); font-size: 11px; }

        /* =========================================
           RESPONSIVE
        ========================================= */

        @media (max-width: 1250px) {
            .sidebar { width: 230px; min-width: 230px; }
            .main { width: calc(100% - 230px); margin-left: 230px; }
            .stats-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .stats-grid.cols-1, .stats-grid.cols-2, .stats-grid.cols-3, .stats-grid.cols-4 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
            .grupos-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 1000px) {
            .sidebar { width: 78px; min-width: 78px; }
            .main { width: calc(100% - 78px); margin-left: 78px; }
            .sidebar-logo { height: 90px; padding: 12px; }
            .sidebar-logo img { width: 50px; }
            .sidebar-user { margin: 12px 10px; padding: 10px; justify-content: center; }
            .sidebar-user div:last-child { display: none; }
            .menu-title { display: none; }
            .menu-item { justify-content: center; padding: 7px; }
            .menu-item span:last-child { display: none; }
            .content { padding: 22px; }
            .criterio-row { grid-template-columns: 1fr; }
        }

        @media (max-width: 800px) {
            .topbar { padding: 14px 18px; }
            .page-title h1 { font-size: 23px; }
            .top-user span { display: none; }
            .stats-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .grupos-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 600px) {
            .sidebar { width: 60px; min-width: 60px; }
            .main { width: calc(100% - 60px); margin-left: 60px; }
            .content { padding: 14px; }
            .stats-grid { grid-template-columns: 1fr; }
            .stats-grid.cols-1, .stats-grid.cols-2, .stats-grid.cols-3, .stats-grid.cols-4 {
                grid-template-columns: 1fr;
            }
        }

    </style>

    @stack('styles')
</head>

<body>

<div class="quantika-app">

    {{-- SIDEBAR --}}
    <aside class="sidebar">

        <div class="sidebar-logo">
            <img src="{{ asset('images/quantika-logo.png') }}" alt="Quantika Pool">
        </div>

        @php
            $nombreUsuario = auth()->user()->name;
            $inicialesUsuario = collect(preg_split('/\s+/', trim($nombreUsuario)))
                ->filter()
                ->map(fn ($parte) => mb_strtoupper(mb_substr($parte, 0, 1)))
                ->take(2)
                ->implode('');
        @endphp

        <div class="sidebar-user">
            <div class="avatar">{{ $inicialesUsuario ?: 'IN' }}</div>
            <div>
                <strong>{{ $nombreUsuario }}</strong>
                <span>Instructor{{ isset($instructor) && $instructor?->sucursal ? ' · '.$instructor->sucursal->nombre : '' }}</span>
            </div>
        </div>

        <div class="sidebar-scroll">

            <div class="menu-title">PORTAL DEL INSTRUCTOR</div>

            <nav class="menu">

                <a href="{{ route('instructor.dashboard') }}" class="menu-item {{ request()->routeIs('instructor.dashboard') ? 'active' : '' }}">
                    <div class="menu-icon">⌂</div>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('instructor.agenda') }}" class="menu-item {{ request()->routeIs('instructor.agenda') ? 'active' : '' }}">
                    <div class="menu-icon">▣</div>
                    <span>Mi Agenda</span>
                </a>

                <a href="{{ route('instructor.alumnos.index') }}" class="menu-item {{ request()->routeIs('instructor.alumnos.*') ? 'active' : '' }}">
                    <div class="menu-icon">♟</div>
                    <span>Mis Alumnos</span>
                </a>

                <a href="{{ route('instructor.evaluaciones.index') }}" class="menu-item {{ request()->routeIs('instructor.evaluaciones.*') ? 'active' : '' }}">
                    <div class="menu-icon">✓</div>
                    <span>Evaluaciones</span>
                </a>

            </nav>

        </div>

        <div class="sidebar-footer">QUANTIKA POOL © 2026</div>

    </aside>


    {{-- CONTENIDO PRINCIPAL --}}
    <main class="main">

        <header class="topbar">

            <div class="page-title">
                <small>PORTAL DEL INSTRUCTOR</small>
                <h1>@yield('page-title', 'Dashboard')</h1>
            </div>

            <div class="top-actions">

                <div class="top-user">
                    <div class="avatar">{{ $inicialesUsuario ?: 'IN' }}</div>
                    <span>{{ $nombreUsuario }}</span>
                </div>

                <form class="logout-form" method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-logout" title="Cerrar sesión">⏻</button>
                </form>

            </div>

        </header>

        <div class="content">

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-error">
                    {{ $errors->first() }}
                </div>
            @endif

            @yield('content')

        </div>

    </main>

</div>

<script>
    // Conserva la posición de scroll del menú entre navegaciones (cada clic
    // recarga la página completa, así que sin esto el menú siempre "brincaba"
    // hasta arriba al entrar a otra sección).
    (function () {
        const menu = document.querySelector('.sidebar-scroll');
        if (! menu) return;

        const key = 'sidebarScrollTop';
        const guardado = sessionStorage.getItem(key);
        if (guardado !== null) {
            menu.scrollTop = parseInt(guardado, 10);
        }

        menu.addEventListener('scroll', function () {
            sessionStorage.setItem(key, menu.scrollTop);
        });
    })();
</script>

</body>
</html>
