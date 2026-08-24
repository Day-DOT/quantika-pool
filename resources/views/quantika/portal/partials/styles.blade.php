{{--
    Hoja de estilos compartida del Portal de Alumnos / Tutores.
    Reutiliza la misma paleta y componentes visuales del panel de
    administración (resources/views/quantika/layouts/app.blade.php)
    para que el portal se sienta parte del mismo sistema.
--}}
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
        --muted-2: #63879a;

        --green: #16e0a4;
        --yellow: #ffbd20;
        --purple: #c05cff;
        --blue: #21c7ff;
        --red: #ff6b6b;
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

    /* ===== LAYOUT ===== */

    .quantika-app { min-height: 100vh; display: flex; }

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
        margin: 24px 22px 16px;
        padding: 18px;
        border-radius: 22px;
        background: linear-gradient(135deg, rgba(20, 89, 116, .60), rgba(4, 44, 65, .65));
        border: 1px solid rgba(76, 213, 239, .17);
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .avatar {
        width: 48px;
        height: 48px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--cyan);
        color: #043047;
        font-size: 17px;
        font-weight: 900;
        flex-shrink: 0;
        box-shadow: 0 8px 25px rgba(66, 216, 239, .18);
    }

    .sidebar-user strong { display: block; font-size: 14px; font-weight: 800; }
    .sidebar-user span { display: block; color: var(--muted); font-size: 11px; margin-top: 4px; }

    .sidebar-scroll { flex: 1; overflow-y: auto; padding: 5px 15px 20px; }

    .menu-title { padding: 14px 15px 10px; color: #6f96a9; font-size: 11px; letter-spacing: 3px; font-weight: 800; }

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
        left: 0; top: 7px; bottom: 7px;
        width: 4px;
        background: var(--cyan);
        border-radius: 0 10px 10px 0;
        box-shadow: 0 0 12px rgba(66, 216, 239, .6);
    }

    .menu-icon {
        width: 40px; height: 40px;
        border-radius: 13px;
        background: rgba(24, 99, 126, .30);
        display: flex; align-items: center; justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }

    .menu-item.active .menu-icon { background: var(--cyan); color: #053147; }

    .menu-item.logout { margin-top: 4px; }

    .sidebar-footer {
        height: 54px;
        border-top: 1px solid rgba(255,255,255,.07);
        display: flex; align-items: center; justify-content: center;
        color: #50778b;
        font-size: 11px;
    }

    .main { width: calc(100% - 255px); margin-left: 255px; min-height: 100vh; }

    /* ===== TOPBAR ===== */

    .topbar {
        min-height: 92px;
        padding: 14px 34px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
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

    .top-actions { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }

    .alumno-select {
        height: 52px;
        min-width: 220px;
        padding: 0 6px 0 17px;
        border-radius: 18px;
        border: 1px solid rgba(66,216,239,.20);
        background: linear-gradient(135deg, rgba(17, 82, 106, .72), rgba(7, 47, 66, .90));
        color: white;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        font-weight: 800;
    }

    .alumno-select select {
        background: transparent;
        border: none;
        color: white;
        font-weight: 800;
        font-size: 14px;
        outline: none;
        cursor: pointer;
        appearance: none;
        padding-right: 10px;
    }

    .alumno-select select option { color: #043047; }

    .top-user {
        height: 52px;
        padding: 5px 15px 5px 6px;
        display: flex; align-items: center; gap: 10px;
        border-radius: 18px;
        background: rgba(8, 57, 77, .78);
        border: 1px solid rgba(66,216,239,.16);
        font-weight: 800;
        font-size: 13px;
    }

    .top-user .avatar { width: 40px; height: 40px; border-radius: 13px; font-size: 13px; }

    .btn-logout {
        height: 52px;
        width: 52px;
        border-radius: 18px;
        background: rgba(8, 57, 77, .78);
        border: 1px solid rgba(255,107,107,.25);
        color: #ff9d9d;
        display: flex; align-items: center; justify-content: center;
        font-size: 18px;
        cursor: pointer;
    }

    .btn-logout:hover { background: rgba(255,107,107,.14); }

    .content { padding: 30px 34px 50px; max-width: 1500px; margin: auto; }

    .flash-status {
        margin-bottom: 20px;
        padding: 15px 20px;
        border-radius: 16px;
        background: rgba(20,231,173,.10);
        border: 1px solid rgba(20,231,173,.30);
        color: #a6f7e2;
        font-size: 13px;
        font-weight: 700;
    }

    .flash-errors {
        margin-bottom: 20px;
        padding: 15px 20px;
        border-radius: 16px;
        background: rgba(255,107,107,.10);
        border: 1px solid rgba(255,107,107,.30);
        color: #ffc7c7;
        font-size: 13px;
        font-weight: 700;
    }

    .flash-errors ul { margin-left: 18px; margin-top: 4px; }

    /* ===== SELECTOR DE ALUMNOS (dashboard) ===== */

    .kids-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
        gap: 16px;
        margin-bottom: 30px;
    }

    .kid-card {
        padding: 18px;
        border-radius: 20px;
        background: linear-gradient(145deg, rgba(7, 54, 74, .95), rgba(4, 37, 55, .95));
        border: 1px solid rgba(65, 210, 237, .15);
        display: flex;
        align-items: center;
        gap: 14px;
        transition: .25s ease;
    }

    .kid-card:hover { transform: translateY(-3px); border-color: rgba(65, 210, 237, .35); }

    .kid-card.active { border-color: var(--cyan); box-shadow: 0 10px 30px rgba(66,216,239,.15); }

    .kid-card .animal { width: 46px; height: 46px; min-width: 46px; }
    .kid-card .animal img { width: 28px; height: 28px; }

    .kid-name { font-weight: 800; font-size: 14px; }
    .kid-meta { color: var(--muted); font-size: 11px; margin-top: 2px; }

    /* ===== ESTADÍSTICAS ===== */

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 26px;
    }

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
        width: 95px; height: 95px;
        border-radius: 50%;
        position: absolute;
        right: -35px; bottom: -35px;
        background: rgba(66,213,238,.06);
    }

    .stat-top { display: flex; align-items: center; justify-content: space-between; gap: 8px; }
    .stat-name { color: #82aebf; font-size: 12px; font-weight: 800; }

    .stat-icon {
        width: 40px; height: 40px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        background: rgba(66,213,238,.10);
        color: var(--cyan);
        font-size: 18px;
    }

    .stat-value { margin-top: 15px; font-size: 31px; font-weight: 900; }
    .stat-sub { margin-top: 7px; color: var(--muted); font-size: 10px; font-weight: 800; }

    /* ===== SECCIONES ===== */

    .section-header { display: flex; justify-content: space-between; align-items: center; margin: 30px 0 14px; flex-wrap: wrap; gap: 10px; }
    .section-header h3 { font-size: 19px; font-weight: 900; }
    .section-link { color: var(--cyan); font-size: 11px; font-weight: 800; }

    /* ===== NIVEL / ANIMAL ===== */

    .level-block {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 20px;
        border-radius: 22px;
        background: linear-gradient(145deg, rgba(7, 51, 70, .98), rgba(4, 37, 55, .98));
        border: 1px solid rgba(65, 210, 237, .17);
        margin-bottom: 24px;
    }

    .animal {
        width: 64px; height: 64px; min-width: 64px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        border: 2px solid var(--level-color, var(--cyan));
        background: radial-gradient(circle, rgba(255,255,255,.08), rgba(1,32,47,.75));
        box-shadow: 0 0 22px color-mix(in srgb, var(--level-color, var(--cyan)), transparent 65%);
    }

    .animal img { width: 40px; height: 40px; object-fit: contain; display: block; }

    .level-number { color: var(--level-color, var(--cyan)); font-size: 9px; letter-spacing: 2px; font-weight: 900; margin-bottom: 4px; }
    .level-name { font-size: 22px; font-weight: 900; line-height: 1.1; }
    .level-description { margin-top: 3px; color: #779aaa; font-size: 12px; }

    .progress-row { display: flex; justify-content: space-between; margin-top: 14px; margin-bottom: 7px; font-size: 10px; font-weight: 800; }
    .progress-row span:last-child { color: var(--level-color, var(--cyan)); }

    .progress { width: 100%; height: 8px; background: #123c4e; border-radius: 20px; overflow: hidden; }
    .progress span {
        display: block; height: 100%; border-radius: inherit;
        background: var(--level-color, var(--cyan));
        box-shadow: 0 0 10px color-mix(in srgb, var(--level-color, var(--cyan)), transparent 45%);
    }

    /* ===== TARJETAS DE HORARIO (reservar) ===== */

    .schedule-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px; }

    .schedule-card {
        padding: 20px;
        border-radius: 20px;
        background: linear-gradient(145deg, rgba(7, 54, 74, .95), rgba(4, 37, 55, .95));
        border: 1px solid rgba(65, 210, 237, .15);
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .schedule-top { display: flex; justify-content: space-between; align-items: flex-start; gap: 10px; }
    .schedule-name { font-weight: 800; font-size: 16px; }
    .schedule-meta { color: var(--muted); font-size: 12px; margin-top: 3px; line-height: 1.5; }

    .schedule-cupo { font-size: 11px; font-weight: 800; }
    .schedule-cupo.ok { color: var(--green); }
    .schedule-cupo.low { color: var(--yellow); }
    .schedule-cupo.full { color: var(--red); }

    /* ===== BADGES ===== */

    .badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 12px;
        border-radius: 30px;
        font-size: 9px;
        font-weight: 800;
        letter-spacing: .5px;
    }

    .badge-green { color: #16e6aa; background: rgba(20,231,173,.10); border: 1px solid rgba(20,231,173,.28); }
    .badge-yellow { color: #ffd873; background: rgba(255,194,41,.10); border: 1px solid rgba(255,194,41,.28); }
    .badge-red { color: #ff9d9d; background: rgba(255,107,107,.10); border: 1px solid rgba(255,107,107,.28); }
    .badge-purple { color: #d9b3ff; background: rgba(187,85,255,.10); border: 1px solid rgba(187,85,255,.28); }
    .badge-cyan { color: #a6ecf7; background: rgba(66,216,239,.10); border: 1px solid rgba(66,216,239,.28); }
    .badge-muted { color: var(--muted); background: rgba(134,170,189,.08); border: 1px solid rgba(134,170,189,.22); }

    /* ===== BOTONES ===== */

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
        border: none;
        cursor: pointer;
        transition: .2s ease;
    }

    .btn-primary { background: var(--cyan); color: #023146; box-shadow: 0 10px 25px rgba(66,213,238,.18); }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 14px 30px rgba(66,213,238,.25); }
    .btn-primary:disabled { opacity: .45; cursor: not-allowed; transform: none; box-shadow: none; }

    .btn-outline { border: 1px solid rgba(66,213,238,.40); color: white; background: rgba(2,29,43,.25); }
    .btn-outline:hover { background: rgba(66,213,238,.10); }

    .btn-block { width: 100%; }

    /* ===== TABLAS ===== */

    .data-card {
        background: linear-gradient(145deg, rgba(7, 51, 70, .97), rgba(4, 35, 53, .97));
        border: 1px solid rgba(65, 210, 237, .15);
        border-radius: 22px;
        overflow: hidden;
    }

    .data-table { width: 100%; border-collapse: collapse; }

    .data-table th {
        padding: 14px 18px;
        text-align: left;
        color: #688b9d;
        font-size: 9px;
        letter-spacing: 1.5px;
        font-weight: 900;
        border-bottom: 1px solid rgba(255,255,255,.07);
    }

    .data-table td { padding: 14px 18px; border-bottom: 1px solid rgba(255,255,255,.05); font-size: 12px; vertical-align: middle; }
    .data-table tr:last-child td { border-bottom: none; }

    .empty-row { padding: 40px 20px; text-align: center; color: var(--muted); font-size: 13px; }

    /* ===== ESTADO VACÍO ===== */

    .empty-state {
        padding: 60px 30px;
        text-align: center;
        border-radius: 22px;
        background: linear-gradient(145deg, rgba(7, 51, 70, .97), rgba(4, 35, 53, .97));
        border: 1px solid rgba(65, 210, 237, .15);
    }

    .empty-state h3 { font-size: 20px; margin-bottom: 8px; }
    .empty-state p { color: var(--muted); font-size: 13px; }

    /* ===== FORMULARIOS ===== */

    .field-select, .field-input {
        width: 100%;
        min-height: 50px;
        padding: 0 16px;
        border-radius: 15px;
        border: 1px solid var(--border);
        background: rgba(2, 34, 49, .55);
        color: var(--text);
        font-size: 13px;
        font-weight: 700;
    }

    .field-label { display: block; color: var(--muted); font-size: 11px; font-weight: 800; letter-spacing: 1px; margin-bottom: 8px; text-transform: uppercase; }

    /* ===== CRITERIOS DE EVALUACIÓN (boleta) ===== */

    .criterio-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 16px 18px;
        border-bottom: 1px solid rgba(255,255,255,.05);
    }

    .criterio-row:last-child { border-bottom: none; }
    .criterio-nombre { font-weight: 800; font-size: 13px; }
    .criterio-obs { color: var(--muted); font-size: 11px; margin-top: 4px; max-width: 480px; }

    /* ===== RESPONSIVE ===== */

    @media (max-width: 1250px) {
        .sidebar { width: 230px; min-width: 230px; }
        .main { width: calc(100% - 230px); margin-left: 230px; }
        .stats-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (max-width: 1000px) {
        .sidebar { width: 78px; min-width: 78px; }
        .main { width: calc(100% - 78px); margin-left: 78px; }
        .sidebar-logo { height: 90px; padding: 12px; }
        .sidebar-logo img { width: 50px; }
        .sidebar-user { margin: 14px 10px; padding: 10px; justify-content: center; }
        .sidebar-user div:last-child { display: none; }
        .menu-title { display: none; }
        .menu-item { justify-content: center; padding: 7px; }
        .menu-item span:last-child { display: none; }
        .content { padding: 22px; }
    }

    @media (max-width: 800px) {
        .topbar { padding: 16px 20px; }
        .page-title h1 { font-size: 22px; }
        .top-user span { display: none; }
        .stats-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .kids-grid { grid-template-columns: 1fr; }
    }

    @media (max-width: 600px) {
        .sidebar { width: 64px; min-width: 64px; }
        .main { width: calc(100% - 64px); margin-left: 64px; }
        .content { padding: 15px; }
        .stats-grid { grid-template-columns: 1fr; }
        .schedule-grid { grid-template-columns: 1fr; }
        .data-card { overflow-x: auto; }
        .data-table { min-width: 640px; }
    }

</style>
