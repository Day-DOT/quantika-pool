<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Super Administrador') · Quantika Pool</title>
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

        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }

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

        /* ================= LAYOUT ================= */

        .quantika-app { min-height: 100vh; display: flex; }

        .sidebar {
            width: 255px; min-width: 255px; height: 100vh;
            position: fixed; left: 0; top: 0;
            background: linear-gradient(180deg, #052b3e 0%, #032638 55%, #021d2c 100%);
            border-right: 1px solid rgba(67, 206, 235, .14);
            display: flex; flex-direction: column;
            z-index: 100;
        }

        .sidebar-logo {
            height: 175px; display: flex; align-items: center; justify-content: center;
            padding: 25px; border-bottom: 1px solid rgba(255,255,255,.07);
        }

        .sidebar-logo img { width: 190px; max-width: 100%; height: auto; object-fit: contain; }

        .sidebar-user {
            margin: 22px 22px 16px; padding: 16px; border-radius: 20px;
            background: linear-gradient(135deg, rgba(20, 89, 116, .60), rgba(4, 44, 65, .65));
            border: 1px solid rgba(76, 213, 239, .17);
            display: flex; align-items: center; gap: 14px;
        }

        .avatar {
            width: 50px; height: 50px; border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            background: var(--cyan); color: #043047; font-size: 18px; font-weight: 900; flex-shrink: 0;
        }

        .sidebar-user strong { display: block; font-size: 15px; font-weight: 800; }
        .sidebar-user span { display: block; color: var(--muted); font-size: 11px; margin-top: 3px; }

        .sidebar-scroll { flex: 1; overflow-y: auto; padding: 5px 15px 20px; scrollbar-width: thin; scrollbar-color: #176a83 transparent; }
        .sidebar-scroll::-webkit-scrollbar { width: 5px; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: #176a83; border-radius: 20px; }

        .menu-title { padding: 14px 15px 8px; color: #6f96a9; font-size: 11px; letter-spacing: 3px; font-weight: 800; }
        .menu { display: flex; flex-direction: column; gap: 5px; }

        .menu-item {
            min-height: 46px; display: flex; align-items: center; gap: 13px;
            padding: 8px 14px; border-radius: 15px; color: #8eabba;
            font-size: 13.5px; font-weight: 700; transition: .2s ease; position: relative;
        }

        .menu-item:hover { background: rgba(39, 124, 153, .17); color: #fff; }

        .menu-item.active {
            color: var(--cyan);
            background: linear-gradient(90deg, rgba(29, 137, 165, .32), rgba(17, 78, 102, .40));
        }

        .menu-item.active::before {
            content: ""; position: absolute; left: 0; top: 7px; bottom: 7px; width: 4px;
            background: var(--cyan); border-radius: 0 10px 10px 0;
            box-shadow: 0 0 12px rgba(66, 216, 239, .6);
        }

        .menu-icon {
            width: 36px; height: 36px; border-radius: 12px; background: rgba(24, 99, 126, .30);
            display: flex; align-items: center; justify-content: center; font-size: 15px; flex-shrink: 0;
        }

        .menu-item.active .menu-icon { background: var(--cyan); color: #053147; }

        .sidebar-footer {
            padding: 14px; border-top: 1px solid rgba(255,255,255,.07);
        }

        .logout-btn {
            width: 100%; min-height: 42px; border-radius: 13px; border: 1px solid rgba(255,107,107,.25);
            background: rgba(255,107,107,.08); color: #ffb3b3; font-size: 12.5px; font-weight: 800;
            display: flex; align-items: center; justify-content: center; gap: 8px; cursor: pointer;
        }

        .logout-btn:hover { background: rgba(255,107,107,.16); }

        .main { width: calc(100% - 255px); margin-left: 255px; min-height: 100vh; }

        /* ================= TOPBAR ================= */

        .topbar {
            height: 92px; padding: 0 34px;
            display: flex; align-items: center; justify-content: space-between; gap: 18px;
            border-bottom: 1px solid rgba(255,255,255,.07);
            background: rgba(2, 28, 42, .88); backdrop-filter: blur(14px);
            position: sticky; top: 0; z-index: 80;
        }

        .page-title small {
            display: block; color: var(--cyan); font-size: 11px; letter-spacing: 3px; font-weight: 900; margin-bottom: 6px;
        }

        .page-title h1 { font-size: 28px; line-height: 1; font-weight: 900; }

        .top-actions { display: flex; align-items: center; gap: 12px; }

        .branch-select-wrap { position: relative; }

        .branch-select {
            height: 50px; min-width: 210px; padding: 0 16px; border-radius: 16px;
            border: 1px solid rgba(66,216,239,.20);
            background: linear-gradient(135deg, rgba(17, 82, 106, .72), rgba(7, 47, 66, .90));
            color: white; display: flex; align-items: center; justify-content: space-between;
            cursor: pointer; font-size: 13.5px; font-weight: 800; width: 100%;
        }

        .branch-left { display: flex; align-items: center; gap: 9px; }

        .branch-dot { width: 9px; height: 9px; border-radius: 50%; background: var(--cyan); box-shadow: 0 0 12px var(--cyan); }
        .branch-arrow { color: #9db5c2; font-size: 13px; }

        .branch-menu {
            display: none; position: absolute; right: 0; top: calc(100% + 8px);
            min-width: 230px; background: var(--card); border: 1px solid var(--border);
            border-radius: 16px; padding: 8px; z-index: 90;
            box-shadow: 0 18px 40px rgba(0,0,0,.35);
        }

        .branch-menu.open { display: block; }

        .branch-menu form { display: block; }

        .branch-menu-item {
            width: 100%; text-align: left; padding: 10px 12px; border-radius: 11px; border: none;
            background: transparent; color: var(--text); font-size: 13px; font-weight: 700; cursor: pointer;
            display: flex; align-items: center; justify-content: space-between; gap: 8px;
        }

        .branch-menu-item:hover { background: rgba(66,216,239,.10); }
        .branch-menu-item.active { color: var(--cyan); }

        .notification {
            width: 50px; height: 50px; border-radius: 16px; background: rgba(8, 57, 77, .78);
            border: 1px solid rgba(66,216,239,.16); display: flex; align-items: center; justify-content: center;
            font-size: 18px; cursor: pointer;
        }

        .top-user {
            height: 50px; padding: 5px 14px 5px 6px; display: flex; align-items: center; gap: 9px;
            border-radius: 16px; background: rgba(8, 57, 77, .78); border: 1px solid rgba(66,216,239,.16);
            font-weight: 800; font-size: 13px;
        }

        .top-user .avatar { width: 38px; height: 38px; border-radius: 11px; font-size: 13px; }

        /* ================= HERO ================= */

        .hero {
            position: relative;
            min-height: 390px;
            overflow: hidden;
            border-radius: 28px;
            border: 1px solid rgba(71, 208, 235, .20);

            background:
                linear-gradient(
                    90deg,
                    rgba(1, 25, 39, .91) 0%,
                    rgba(2, 44, 65, .78) 48%,
                    rgba(1, 36, 55, .55) 100%
                ),
                url('/images/quantika-pool-bg.jpg');

            background-size: cover;
            background-position: center;

            box-shadow:
                inset 0 0 80px rgba(0,0,0,.28),
                0 20px 55px rgba(0,0,0,.18);
        }

        .hero::after {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(
                    circle at 80% 40%,
                    rgba(45, 213, 240, .10),
                    transparent 28%
                );
            pointer-events: none;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            min-height: 390px;
            display: grid;
            grid-template-columns: minmax(0, 1.3fr) minmax(260px, .7fr);
            align-items: center;
            gap: 30px;
            padding: 45px 55px;
        }

        .hero-text { max-width: 650px; }

        .hero .status {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            border: 1px solid rgba(66,213,238,.50);
            padding: 8px 15px;
            border-radius: 30px;
            color: #c3eaf4;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: 2px;
            margin-bottom: 20px;
            background: rgba(0,30,45,.30);
        }

        .hero .status span {
            width: 9px;
            height: 9px;
            background: var(--cyan);
            border-radius: 50%;
            box-shadow: 0 0 13px var(--cyan);
        }

        .hero h2 {
            font-size: clamp(38px, 3.8vw, 58px);
            line-height: .98;
            letter-spacing: -2px;
            font-weight: 950;
            margin-bottom: 18px;
        }

        .hero h2 .cyan { color: var(--cyan); }

        .hero-description {
            max-width: 600px;
            color: #d4e6ed;
            font-size: 16px;
            line-height: 1.5;
            margin-bottom: 25px;
        }

        .hero-buttons { display: flex; flex-wrap: wrap; gap: 12px; }

        .hero-logo-box {
            height: 245px;
            max-width: 330px;
            width: 100%;
            justify-self: end;
            border-radius: 25px;
            border: 1px solid rgba(66,213,238,.35);
            background: rgba(1, 30, 45, .72);
            backdrop-filter: blur(5px);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow:
                inset 0 0 45px rgba(0,0,0,.18),
                0 15px 35px rgba(0,0,0,.18);
        }

        .hero-logo-box img {
            width: 78%;
            max-width: 250px;
            height: auto;
            object-fit: contain;
        }

        @media (max-width: 950px) {
            .hero-content { grid-template-columns: 1fr; }
            .hero-logo-box { display: none; }
        }

        @media (max-width: 700px) {
            .hero { min-height: auto; border-radius: 21px; }
            .hero-content { min-height: auto; padding: 30px 25px; }
            .hero h2 { font-size: 36px; letter-spacing: -1px; }
            .hero-description { font-size: 13px; }
            .hero-buttons { flex-direction: column; }
        }

        /* ================= CONTENT ================= */

        .content { padding: 32px 36px 55px; max-width: 1500px; margin: 0 auto; }

        .alert {
            padding: 15px 18px; border-radius: 16px; margin-bottom: 20px;
            font-size: 13px; font-weight: 700; border: 1px solid transparent;
        }

        .alert-success { background: rgba(20,231,173,.10); border-color: rgba(20,231,173,.30); color: #7ff3d3; }
        .alert-error { background: rgba(255,107,107,.10); border-color: rgba(255,107,107,.30); color: #ffb3b3; }
        .alert ul { margin: 6px 0 0 18px; }

        .breadcrumb-back {
            display: inline-flex; align-items: center; gap: 8px; margin-bottom: 16px;
            color: var(--cyan); font-size: 12.5px; font-weight: 800;
        }

        .breadcrumb-back:hover { text-decoration: underline; }

        /* ================= STAT CARDS ================= */

        .stats-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; }

        /* Variantes de número de columnas: se usan como clase (`.cols-N`) en
           vez de un `style` inline, para que estas reglas @media sí puedan
           ganarle en cascada (un estilo inline nunca puede ser sobreescrito
           por una regla de clase, sin importar el @media). */
        .stats-grid.cols-1 { grid-template-columns: repeat(1, minmax(0, 1fr)); }
        .stats-grid.cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .stats-grid.cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .stats-grid.cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }

        .stat-card {
            min-height: 150px; padding: 21px; border-radius: 20px; position: relative; overflow: hidden;
            background: linear-gradient(145deg, rgba(7,54,74,.96), rgba(4,42,59,.94));
            border: 1px solid rgba(66,213,238,.17); transition: .2s ease;
        }

        .stat-card:hover { transform: translateY(-3px); border-color: rgba(66,213,238,.35); }

        .stat-card::after {
            content: ""; position: absolute; width: 95px; height: 95px; right: -35px; bottom: -35px;
            border-radius: 50%; background: rgba(66,213,238,.06);
        }

        .stat-top { display: flex; align-items: center; justify-content: space-between; gap: 8px; }
        .stat-name { color: #82aebf; font-size: 12px; font-weight: 800; }

        .stat-icon {
            width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center;
            justify-content: center; background: rgba(66,213,238,.10); color: var(--cyan); font-size: 18px;
        }

        .stat-value { margin-top: 15px; font-size: 31px; font-weight: 900; }
        .stat-change { margin-top: 7px; color: var(--green); font-size: 10px; font-weight: 800; }

        /* ================= SECTIONS / CARDS ================= */

        .section-header {
            display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;
            margin: 30px 0 14px;
        }

        .section-header h3 { font-size: 20px; font-weight: 900; }
        .section-link { color: var(--cyan); font-size: 11px; font-weight: 800; }

        .panel {
            border-radius: 22px; padding: 23px;
            background: rgba(5, 45, 62, .82);
            border: 1px solid rgba(66,213,238,.15);
        }

        /* ================= BUTTONS ================= */

        .btn {
            min-height: 48px; padding: 0 21px; border-radius: 13px; display: inline-flex;
            align-items: center; justify-content: center; gap: 15px; font-size: 14px; font-weight: 900;
            transition: .2s ease; cursor: pointer; border: none;
        }

        .btn-primary { background: var(--cyan); color: #023146; box-shadow: 0 10px 25px rgba(66,213,238,.18); }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 14px 30px rgba(66,213,238,.25); }

        .btn-outline { border: 1px solid rgba(66,213,238,.40); color: white; background: rgba(2,29,43,.25); }
        .btn-outline:hover { background: rgba(66,213,238,.10); }

        .btn-danger { border: 1px solid rgba(255,107,107,.35); color: #ffb3b3; background: rgba(255,107,107,.08); }
        .btn-danger:hover { background: rgba(255,107,107,.16); }

        .btn-sm { min-height: 36px; padding: 0 14px; font-size: 12px; }
        .btn-block { width: 100%; }

        /* ================= TABLES ================= */

        .table-wrap { overflow-x: auto; }

        .data-table { width: 100%; border-collapse: collapse; min-width: 720px; }

        .data-table th {
            padding: 13px 16px; text-align: left; color: #688b9d; font-size: 9.5px;
            letter-spacing: 1.3px; font-weight: 900; border-bottom: 1px solid rgba(255,255,255,.07);
            text-transform: uppercase;
        }

        .data-table td { padding: 13px 16px; border-bottom: 1px solid rgba(255,255,255,.05); font-size: 12.5px; }
        .data-table tr:last-child td { border-bottom: none; }
        .data-table tr:hover td { background: rgba(66,216,239,.03); }

        .badge {
            display: inline-flex; align-items: center; gap: 5px; padding: 5px 10px; border-radius: 30px;
            color: #16e6aa; background: rgba(20,231,173,.08); border: 1px solid rgba(20,231,173,.25);
            font-size: 9.5px; font-weight: 800;
        }

        .badge-muted { color: #9db5c2; background: rgba(157,181,194,.08); border-color: rgba(157,181,194,.25); }
        .badge-yellow { color: var(--yellow); background: rgba(255,194,41,.08); border-color: rgba(255,194,41,.25); }
        .badge-red { color: var(--red); background: rgba(255,107,107,.08); border-color: rgba(255,107,107,.25); }
        .badge-purple { color: var(--purple); background: rgba(187,85,255,.08); border-color: rgba(187,85,255,.25); }
        .badge-blue { color: var(--blue); background: rgba(22,207,255,.08); border-color: rgba(22,207,255,.25); }

        .empty-state { padding: 40px 20px; text-align: center; color: var(--muted); font-size: 13px; }

        /* ================= FORMS ================= */

        .form-card { max-width: 900px; }

        .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; }
        .form-grid .full { grid-column: 1 / -1; }

        .form-group { display: flex; flex-direction: column; gap: 8px; margin-bottom: 18px; }

        .form-group label { font-size: 12px; font-weight: 800; color: var(--muted); letter-spacing: .5px; }

        .form-input, .form-select, .form-textarea {
            width: 100%; min-height: 46px; padding: 0 15px; border-radius: 13px;
            border: 1px solid var(--border); background: rgba(2, 30, 44, .6); color: var(--text); font-size: 13.5px;
        }

        .form-textarea { min-height: 90px; padding: 12px 15px; resize: vertical; }
        .form-input:focus, .form-select:focus, .form-textarea:focus { outline: none; border-color: var(--cyan); }

        .form-hint { color: var(--muted-2); font-size: 11px; }
        .form-error { color: var(--red); font-size: 11.5px; font-weight: 700; }

        .checkbox-row { display: flex; align-items: center; gap: 10px; }
        .checkbox-row input[type="checkbox"] { width: 18px; height: 18px; accent-color: var(--cyan); }

        .form-actions { display: flex; gap: 12px; margin-top: 10px; flex-wrap: wrap; }

        .filters-bar {
            display: flex; gap: 12px; flex-wrap: wrap; align-items: end; margin-bottom: 18px;
        }

        .filters-bar .form-group { margin-bottom: 0; min-width: 170px; }

        .pagination-wrap { margin-top: 20px; }
        .pagination-wrap nav > div { color: var(--muted); font-size: 12px; }
        .pagination-wrap a, .pagination-wrap span { color: var(--text) !important; }

        /* ================= RESPONSIVE ================= */

        @media (max-width: 1250px) {
            .sidebar { width: 230px; min-width: 230px; }
            .main { width: calc(100% - 230px); margin-left: 230px; }
            .stats-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .stats-grid.cols-1, .stats-grid.cols-2, .stats-grid.cols-3, .stats-grid.cols-4 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
            .form-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 560px) {
            .stats-grid.cols-1, .stats-grid.cols-2, .stats-grid.cols-3, .stats-grid.cols-4 {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 1000px) {
            .sidebar { width: 74px; min-width: 74px; }
            .main { width: calc(100% - 74px); margin-left: 74px; }
            .sidebar-logo { height: 90px; padding: 12px; }
            .sidebar-logo img { width: 50px; }
            .sidebar-user { margin: 12px 10px; padding: 8px; justify-content: center; }
            .sidebar-user div:last-child { display: none; }
            .menu-title { display: none; }
            .menu-item { justify-content: center; padding: 7px; }
            .menu-item span:last-child { display: none; }
            .logout-btn span:last-child { display: none; }
        }

        @media (max-width: 800px) {
            .topbar { padding: 14px 18px; flex-wrap: wrap; }
            .page-title h1 { font-size: 22px; }
            .content { padding: 20px; }
            .branch-select { min-width: 160px; }
        }

        .section {
            margin-top: 28px;
        }

        /* =========================================================
           ACCIONES RÁPIDAS DE CONSULTA (dashboards de Admin/Super Admin)
        ========================================================= */

        .quick-links-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-top: 22px;
        }

        .quick-link-card {
            position: relative;
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 18px 20px;
            border-radius: 20px;
            background: linear-gradient(145deg, rgba(7,54,74,.92), rgba(3,35,51,.96));
            border: 1px solid rgba(66,213,238,.16);
            text-decoration: none;
            color: inherit;
            transition: .2s;
        }

        .quick-link-card:hover {
            transform: translateY(-3px);
            border-color: rgba(66,213,238,.45);
        }

        .quick-link-icon {
            width: 44px;
            height: 44px;
            min-width: 44px;
            border-radius: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            background: rgba(66,213,238,.10);
            color: var(--cyan);
        }

        .quick-link-title {
            font-size: 14px;
            font-weight: 900;
        }

        .quick-link-desc {
            color: var(--muted);
            font-size: 11px;
            margin-top: 3px;
        }

        .quick-link-badge {
            position: absolute;
            top: 14px;
            right: 16px;
            min-width: 22px;
            height: 22px;
            padding: 0 7px;
            border-radius: 20px;
            background: var(--yellow);
            color: #2a1e00;
            font-size: 11px;
            font-weight: 950;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @media (max-width: 1250px) {
            .quick-links-grid { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 480px) {
            .quick-links-grid { grid-template-columns: 1fr; }
        }

        /* =========================================================
           CALENDARIO SEMANAL DE CLASES (dashboards de Admin/Super Admin)
        ========================================================= */

        .week-calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 12px;
            margin-top: 6px;
        }

        .week-day {
            border-radius: 16px;
            border: 1px solid rgba(66,213,238,.14);
            background: rgba(2,28,42,.45);
            padding: 12px;
            min-height: 120px;
        }

        .week-day.es-hoy {
            border-color: rgba(66,213,238,.55);
            background: rgba(66,213,238,.06);
        }

        .week-day-head {
            text-align: center;
            margin-bottom: 10px;
        }

        .week-day-label {
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .5px;
            color: #b5d3de;
        }

        .week-day.es-hoy .week-day-label {
            color: var(--cyan);
        }

        .week-day-fecha {
            font-size: 10px;
            color: var(--muted);
            margin-top: 2px;
        }

        .week-class {
            border-radius: 12px;
            padding: 8px 10px;
            background: rgba(255,255,255,.03);
            border: 1px solid rgba(255,255,255,.06);
            margin-bottom: 8px;
            font-size: 11px;
        }

        .week-class:last-child {
            margin-bottom: 0;
        }

        .week-class-nombre {
            font-weight: 800;
            margin-bottom: 2px;
        }

        .week-class-hora {
            color: #9ec4d3;
            font-size: 10px;
        }

        .week-class-instructor {
            color: var(--muted);
            font-size: 10px;
            margin-top: 3px;
        }

        .week-class-cupo {
            display: inline-block;
            margin-top: 5px;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 9px;
            font-weight: 900;
        }

        .week-class-cupo.ok { background: rgba(19,227,162,.12); color: var(--green); }
        .week-class-cupo.low { background: rgba(255,189,32,.12); color: var(--yellow); }
        .week-class-cupo.full { background: rgba(255,95,109,.12); color: #ff6b6b; }

        .week-day-empty {
            color: var(--muted);
            font-size: 10px;
            text-align: center;
            margin-top: 10px;
        }

        @media (max-width: 1100px) {
            .week-calendar { grid-template-columns: repeat(4, 1fr); }
        }

        @media (max-width: 700px) {
            .week-calendar { grid-template-columns: repeat(2, 1fr); }
        }
    </style>

    @stack('styles')
</head>

<body>

@php
    $esSuperAdmin = auth()->user()->isSuperAdmin();
    $sucursalesMenu = \App\Models\Sucursal::orderBy('nombre')->get();
    $sucursalActualIdMenu = \App\Support\SucursalContext::actualId();
    $sucursalActualMenu = $sucursalActualIdMenu ? $sucursalesMenu->firstWhere('id', $sucursalActualIdMenu) : null;
@endphp

<div class="quantika-app">

    {{-- SIDEBAR --}}
    <aside class="sidebar">

        <div class="sidebar-logo">
            <img src="{{ auth()->user()->logoUrl() }}" alt="Quantika Pool">
        </div>

        <div class="sidebar-user">
            <div class="avatar">
                {{ \Illuminate\Support\Str::of(auth()->user()->name)->explode(' ')->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('') }}
            </div>
            <div>
                <strong>{{ auth()->user()->name }}</strong>
                <span>{{ $esSuperAdmin ? 'Super Administrador' : 'Administrador' }}</span>
            </div>
        </div>

        {{-- Este menú es el mismo para Admin y Super Admin: los enlaces comunes
             apuntan a la misma ruta para ambos roles; el Super Admin además ve
             una sección exclusiva de administración global. --}}
        <div class="sidebar-scroll">

            <div class="menu-title">Principal</div>
            <nav class="menu">
                <a href="{{ $esSuperAdmin ? route('super-admin.dashboard') : route('admin.dashboard') }}"
                   class="menu-item {{ request()->routeIs('super-admin.dashboard') || request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <div class="menu-icon">⌂</div>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('alumnos.index') }}"
                   class="menu-item {{ request()->routeIs('alumnos.*') ? 'active' : '' }}">
                    <div class="menu-icon">♟</div>
                    <span>Alumnos{{ $esSuperAdmin ? ' (global)' : '' }}</span>
                </a>
            </nav>

            <div class="menu-title">Operación diaria</div>
            <nav class="menu">
                <a href="{{ route('horarios.index') }}" class="menu-item {{ request()->routeIs('horarios.*') ? 'active' : '' }}">
                    <div class="menu-icon">▣</div>
                    <span>Horarios</span>
                </a>
                <a href="{{ route('reservas.index') }}" class="menu-item {{ request()->routeIs('reservas.*') ? 'active' : '' }}">
                    <div class="menu-icon">✓</div>
                    <span>Reservas pendientes</span>
                </a>
                <a href="{{ route('reposiciones.index') }}" class="menu-item {{ request()->routeIs('reposiciones.*') ? 'active' : '' }}">
                    <div class="menu-icon">↻</div>
                    <span>Reposiciones de clases</span>
                </a>
                <a href="{{ route('evaluaciones.index') }}" class="menu-item {{ request()->routeIs('evaluaciones.*') ? 'active' : '' }}">
                    <div class="menu-icon">📈</div>
                    <span>Evaluaciones</span>
                </a>
            </nav>

            <div class="menu-title">Administración</div>
            <nav class="menu">
                <a href="{{ route('instructores.index') }}" class="menu-item {{ request()->routeIs('instructores.*') ? 'active' : '' }}">
                    <div class="menu-icon">🏊</div>
                    <span>Instructores</span>
                </a>
                <a href="{{ route('pagos.index') }}" class="menu-item {{ request()->routeIs('pagos.*') ? 'active' : '' }}">
                    <div class="menu-icon">$</div>
                    <span>Pagos</span>
                </a>
                @if ($esSuperAdmin)
                    <a href="{{ route('super-admin.usuarios.index') }}" class="menu-item {{ request()->routeIs('super-admin.usuarios.*') ? 'active' : '' }}">
                        <div class="menu-icon">👤</div>
                        <span>Usuarios</span>
                    </a>
                    <a href="{{ route('super-admin.sucursales.index') }}" class="menu-item {{ request()->routeIs('super-admin.sucursales.*') || request()->routeIs('super-admin.sucursal-2') ? 'active' : '' }}">
                        <div class="menu-icon">🏢</div>
                        <span>Sucursales</span>
                    </a>
                @endif
            </nav>

            <div class="menu-title">Catálogo</div>
            <nav class="menu">
                <a href="{{ route('niveles.index') }}"
                   class="menu-item {{ request()->routeIs('niveles.*') ? 'active' : '' }}">
                    <div class="menu-icon">◉</div>
                    <span>Niveles</span>
                </a>
                @if ($esSuperAdmin)
                    <a href="{{ route('super-admin.criterios.index') }}" class="menu-item {{ request()->routeIs('super-admin.criterios.*') ? 'active' : '' }}">
                        <div class="menu-icon">✎</div>
                        <span>Criterios de evaluación</span>
                    </a>
                    <a href="{{ route('super-admin.carriles.index') }}" class="menu-item {{ request()->routeIs('super-admin.carriles.*') ? 'active' : '' }}">
                        <div class="menu-icon">▦</div>
                        <span>Carriles / Alberca</span>
                    </a>
                    <a href="{{ route('super-admin.planes.index') }}" class="menu-item {{ request()->routeIs('super-admin.planes.*') ? 'active' : '' }}">
                        <div class="menu-icon">$</div>
                        <span>Planes de mensualidad</span>
                    </a>
                @endif
            </nav>

            <div class="menu-title">Sistema</div>
            <nav class="menu">
                <a href="{{ route('configuracion.index') }}" class="menu-item {{ request()->routeIs('configuracion.*') ? 'active' : '' }}">
                    <div class="menu-icon">⚙</div>
                    <span>Configuración</span>
                </a>
            </nav>

        </div>

        <div class="sidebar-footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">
                    <span>⎋</span>
                    <span>Cerrar sesión</span>
                </button>
            </form>
        </div>

    </aside>

    {{-- MAIN --}}
    <main class="main">

        <header class="topbar">

            <div class="page-title">
                <small>{{ mb_strtoupper(auth()->user()->sucursalActual()?->nombre ?? 'QUANTIKA POOL') }} · {{ $esSuperAdmin ? 'SUPER ADMINISTRADOR' : 'ADMINISTRACIÓN' }}</small>
                <h1>@yield('page-title', 'Dashboard')</h1>
            </div>

            <div class="top-actions">

                @if ($esSuperAdmin)
                    {{-- SELECTOR DE SUCURSAL (real, conectado a SucursalContext) — solo Super Admin puede alternar --}}
                    <div class="branch-select-wrap">
                        <button type="button" class="branch-select" id="branchButton" onclick="toggleBranchMenu()">
                            <div class="branch-left">
                                <span class="branch-dot"></span>
                                <span id="branchName">{{ $sucursalActualMenu->nombre ?? 'Todas las sucursales' }}</span>
                            </div>
                            <span class="branch-arrow">▼</span>
                        </button>

                        <div class="branch-menu" id="branchMenu">
                            <form method="POST" action="{{ route('super-admin.sucursal-actual') }}">
                                @csrf
                                <button type="submit" name="sucursal_id" value="" class="branch-menu-item {{ ! $sucursalActualIdMenu ? 'active' : '' }}">
                                    <span>Todas las sucursales</span>
                                    @if(! $sucursalActualIdMenu) <span>✓</span> @endif
                                </button>
                            </form>
                            @foreach ($sucursalesMenu as $s)
                                <form method="POST" action="{{ route('super-admin.sucursal-actual') }}">
                                    @csrf
                                    <button type="submit" name="sucursal_id" value="{{ $s->id }}" class="branch-menu-item {{ $sucursalActualIdMenu === $s->id ? 'active' : '' }}">
                                        <span>{{ $s->nombre }}</span>
                                        @if($sucursalActualIdMenu === $s->id) <span>✓</span> @endif
                                    </button>
                                </form>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="top-user">
                    <div class="avatar">
                        {{ \Illuminate\Support\Str::of(auth()->user()->name)->explode(' ')->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('') }}
                    </div>
                    <span>{{ auth()->user()->name }}</span>
                </div>

            </div>

        </header>

        <div class="content">

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            @if (session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-error">
                    <strong>Se encontraron algunos problemas:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')

        </div>

    </main>

</div>

<script>
    function toggleBranchMenu() {
        document.getElementById('branchMenu').classList.toggle('open');
    }

    document.addEventListener('click', function (event) {
        const wrap = document.querySelector('.branch-select-wrap');
        if (wrap && ! wrap.contains(event.target)) {
            document.getElementById('branchMenu')?.classList.remove('open');
        }
    });

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

@stack('scripts')

</body>
</html>
