<!DOCTYPE html>
<html lang="es">
<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Alumnos | Quantika Pool</title>

    <style>

        /* =====================================================
           RESET
        ===================================================== */

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {

            --bg: #031d2b;
            --bg-2: #042536;
            --panel: #062d40;
            --panel-2: #07364a;

            --border: rgba(71, 208, 235, .18);

            --cyan: #42d5ed;
            --cyan-2: #21c4e5;

            --text: #f4fbff;
            --muted: #82a8b9;

            --green: #13e3a2;
            --yellow: #ffc329;
            --red: #ff5f6d;
            --purple: #b85cff;
        }


        /* =====================================================
           BODY
        ===================================================== */

        body {

            font-family:
                Inter,
                ui-sans-serif,
                system-ui,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                sans-serif;

            background:
                radial-gradient(
                    circle at 80% 10%,
                    rgba(28, 153, 194, .08),
                    transparent 35%
                ),
                var(--bg);

            color: var(--text);

            min-height: 100vh;

            overflow-x: hidden;
        }


        /* =====================================================
           APP
        ===================================================== */

        .app {

            min-height: 100vh;

            display: flex;
        }


        /* =====================================================
           SIDEBAR
        ===================================================== */

        .sidebar {

            width: 270px;

            min-width: 270px;

            height: 100vh;

            position: fixed;

            left: 0;
            top: 0;

            background:
                linear-gradient(
                    180deg,
                    #062c40 0%,
                    #032333 100%
                );

            border-right: 1px solid var(--border);

            display: flex;

            flex-direction: column;

            z-index: 50;
        }


        /* LOGO */

        .logo-area {

            height: 190px;

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 25px;

            border-bottom: 1px solid var(--border);
        }

        .logo-area img {

            width: 215px;

            max-width: 100%;

            height: auto;

            object-fit: contain;
        }


        /* ADMIN CARD */

        .admin-card {

            margin: 28px 22px 20px;

            padding: 18px;

            border: 1px solid var(--border);

            border-radius: 22px;

            background:
                linear-gradient(
                    135deg,
                    rgba(14, 83, 108, .62),
                    rgba(5, 44, 61, .75)
                );

            display: flex;

            align-items: center;

            gap: 15px;
        }

        .admin-avatar {

            width: 58px;
            height: 58px;

            border-radius: 17px;

            background: var(--cyan);

            color: #043044;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 20px;

            font-weight: 900;
        }

        .admin-info strong {

            display: block;

            font-size: 16px;
        }

        .admin-info span {

            display: block;

            margin-top: 5px;

            color: var(--muted);

            font-size: 13px;
        }


        /* NAV */

        .nav {

            flex: 1;

            overflow-y: auto;

            padding: 8px 16px 20px;
        }

        .nav::-webkit-scrollbar {

            width: 5px;
        }

        .nav::-webkit-scrollbar-thumb {

            background: rgba(66, 213, 237, .35);

            border-radius: 10px;
        }

        .nav-title {

            margin: 20px 12px 12px;

            font-size: 11px;

            letter-spacing: 3px;

            font-weight: 800;

            color: #60899b;
        }

        .nav-link {

            position: relative;

            display: flex;

            align-items: center;

            gap: 13px;

            padding: 12px 12px;

            margin: 5px 0;

            color: #8aabba;

            text-decoration: none;

            border-radius: 17px;

            font-weight: 700;

            font-size: 14px;

            transition: .2s ease;
        }

        .nav-link:hover {

            background: rgba(38, 151, 181, .10);

            color: white;
        }

        .nav-link.active {

            background:
                linear-gradient(
                    90deg,
                    rgba(17, 135, 165, .42),
                    rgba(14, 82, 106, .30)
                );

            color: var(--cyan);
        }

        .nav-link.active::before {

            content: "";

            position: absolute;

            left: 0;

            top: 10px;

            bottom: 10px;

            width: 4px;

            border-radius: 10px;

            background: var(--cyan);
        }

        .nav-icon {

            width: 42px;
            height: 42px;

            border-radius: 14px;

            background: rgba(33, 131, 159, .16);

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 17px;

            flex-shrink: 0;
        }

        .nav-link.active .nav-icon {

            background: var(--cyan);

            color: #043044;
        }


        /* SIDEBAR FOOTER */

        .sidebar-footer {

            padding: 18px;

            border-top: 1px solid var(--border);

            text-align: center;

            color: #527d8f;

            font-size: 12px;
        }


        /* =====================================================
           MAIN
        ===================================================== */

        .main {

            width: calc(100% - 270px);

            margin-left: 270px;

            min-height: 100vh;
        }


        /* =====================================================
           TOPBAR
        ===================================================== */

        .topbar {

            height: 105px;

            border-bottom: 1px solid var(--border);

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding: 0 40px;

            background: rgba(3, 29, 43, .88);

            backdrop-filter: blur(15px);

            position: sticky;

            top: 0;

            z-index: 40;
        }

        .top-left {

            display: flex;

            align-items: center;

            gap: 20px;
        }

        .back-button {

            width: 42px;
            height: 42px;

            border-radius: 14px;

            border: 1px solid var(--border);

            background: #082f42;

            color: white;

            display: flex;

            align-items: center;

            justify-content: center;

            text-decoration: none;

            font-size: 20px;

            transition: .2s;
        }

        .back-button:hover {

            background: var(--cyan);

            color: #032333;

            transform: translateX(-2px);
        }

        .page-label {

            color: var(--cyan);

            font-size: 11px;

            font-weight: 900;

            letter-spacing: 4px;

            margin-bottom: 4px;
        }

        .page-title {

            font-size: 27px;

            font-weight: 900;
        }


        /* TOP RIGHT */

        .top-actions {

            display: flex;

            align-items: center;

            gap: 12px;
        }

        .branch-selector {

            min-width: 190px;

            height: 50px;

            padding: 0 18px;

            border-radius: 16px;

            border: 1px solid var(--border);

            background: #08384c;

            color: white;

            font-weight: 800;

            display: flex;

            align-items: center;

            justify-content: space-between;

            cursor: pointer;
        }

        .branch-dot {

            width: 9px;
            height: 9px;

            border-radius: 50%;

            background: var(--cyan);

            box-shadow: 0 0 14px var(--cyan);
        }

        .notification {

            width: 50px;
            height: 50px;

            border-radius: 16px;

            border: 1px solid var(--border);

            background: #08384c;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 20px;
        }


        /* =====================================================
           CONTENT
        ===================================================== */

        .content {

            padding: 34px 40px 60px;

            max-width: 1700px;

            margin: auto;
        }


        /* =====================================================
           HEADER
        ===================================================== */

        .content-header {

            display: flex;

            align-items: flex-end;

            justify-content: space-between;

            gap: 20px;

            margin-bottom: 26px;
        }

        .content-header h2 {

            font-size: 31px;

            font-weight: 900;

            margin-bottom: 7px;
        }

        .content-header p {

            color: var(--muted);

            font-size: 14px;
        }

        .btn-primary {

            border: none;

            background:
                linear-gradient(
                    135deg,
                    #43d9ef,
                    #26c5e4
                );

            color: #043044;

            padding: 14px 21px;

            border-radius: 15px;

            font-weight: 900;

            cursor: pointer;

            box-shadow:
                0 12px 30px rgba(32, 204, 231, .17);

            transition: .2s;
        }

        .btn-primary:hover {

            transform: translateY(-2px);

            box-shadow:
                0 16px 35px rgba(32, 204, 231, .25);
        }


        /* =====================================================
           SUMMARY CARDS
        ===================================================== */

        .summary {

            display: grid;

            grid-template-columns:
                repeat(4, minmax(0, 1fr));

            gap: 16px;

            margin-bottom: 25px;
        }

        .summary-card {

            min-height: 120px;

            padding: 20px;

            border-radius: 21px;

            border: 1px solid var(--border);

            background:
                linear-gradient(
                    145deg,
                    #07364a,
                    #052b3c
                );

            position: relative;

            overflow: hidden;
        }

        .summary-card::after {

            content: "";

            width: 85px;
            height: 85px;

            border-radius: 50%;

            position: absolute;

            right: -28px;
            bottom: -38px;

            background: rgba(66, 213, 237, .07);
        }

        .summary-label {

            color: #76a7ba;

            font-size: 13px;

            font-weight: 700;
        }

        .summary-value {

            font-size: 30px;

            font-weight: 900;

            margin-top: 12px;
        }

        .summary-extra {

            color: var(--green);

            font-size: 12px;

            font-weight: 800;

            margin-top: 4px;
        }


        /* =====================================================
           TOOLBAR
        ===================================================== */

        .toolbar {

            padding: 18px;

            border-radius: 20px;

            border: 1px solid var(--border);

            background: rgba(5, 43, 59, .78);

            display: flex;

            align-items: center;

            gap: 12px;

            margin-bottom: 18px;

            flex-wrap: wrap;
        }

        .search {

            flex: 1;

            min-width: 250px;

            height: 47px;

            position: relative;
        }

        .search input {

            width: 100%;
            height: 100%;

            padding: 0 17px 0 45px;

            border-radius: 14px;

            border: 1px solid var(--border);

            outline: none;

            background: #062c3d;

            color: white;

            font-size: 14px;
        }

        .search span {

            position: absolute;

            left: 16px;

            top: 12px;

            color: #6f9aab;
        }

        .filter {

            height: 47px;

            padding: 0 15px;

            border-radius: 14px;

            border: 1px solid var(--border);

            background: #062c3d;

            color: white;

            outline: none;

            min-width: 145px;

            cursor: pointer;
        }


        /* =====================================================
           TABLE
        ===================================================== */

        .table-card {

            border-radius: 23px;

            border: 1px solid var(--border);

            background:
                linear-gradient(
                    145deg,
                    rgba(7, 54, 74, .92),
                    rgba(4, 40, 55, .92)
                );

            overflow: hidden;
        }

        .table-head {

            padding: 20px 23px;

            display: flex;

            align-items: center;

            justify-content: space-between;

            border-bottom: 1px solid var(--border);
        }

        .table-head h3 {

            font-size: 17px;

            font-weight: 900;
        }

        .table-head span {

            color: #6d9caf;

            font-size: 12px;
        }

        .table-wrapper {

            overflow-x: auto;
        }

        table {

            width: 100%;

            border-collapse: collapse;

            min-width: 950px;
        }

        th {

            text-align: left;

            padding: 15px 20px;

            color: #6d9caf;

            font-size: 10px;

            text-transform: uppercase;

            letter-spacing: 1.5px;

            background: rgba(1, 26, 39, .32);
        }

        td {

            padding: 16px 20px;

            border-top: 1px solid rgba(71, 208, 235, .08);

            font-size: 13px;

            color: #d8edf5;
        }

        tbody tr {

            transition: .2s;
        }

        tbody tr:hover {

            background: rgba(66, 213, 237, .035);
        }


        /* ALUMNO */

        .student {

            display: flex;

            align-items: center;

            gap: 12px;
        }

        .student-avatar {

            width: 43px;
            height: 43px;

            border-radius: 13px;

            background:
                linear-gradient(
                    135deg,
                    #49d8ed,
                    #167f9c
                );

            display: flex;

            align-items: center;

            justify-content: center;

            color: #033044;

            font-weight: 900;

            flex-shrink: 0;
        }

        .student-name {

            font-weight: 850;

            color: white;
        }

        .student-email {

            color: #7198a9;

            font-size: 11px;

            margin-top: 3px;
        }


        /* LEVEL */

        .level {

            display: flex;

            align-items: center;

            gap: 9px;
        }

        .level-animal {

            width: 39px;
            height: 39px;

            border-radius: 50%;

            display: flex;

            align-items: center;

            justify-content: center;

            border: 1px solid rgba(66, 213, 237, .35);

            background: #062b3c;

            overflow: hidden;
        }

        .level-animal img {

            width: 27px;
            height: 27px;

            object-fit: contain;
        }

        .level-name {

            font-weight: 800;

            color: white;
        }

        .level-number {

            font-size: 10px;

            color: #7198a9;

            margin-top: 2px;
        }


        /* BRANCH */

        .branch {

            color: #a6c5d2;

            font-weight: 700;
        }


        /* PROGRESS */

        .progress {

            width: 110px;
        }

        .progress-top {

            display: flex;

            justify-content: space-between;

            font-size: 10px;

            margin-bottom: 5px;
        }

        .progress-top strong {

            color: white;
        }

        .progress-bar {

            height: 5px;

            background: #123f50;

            border-radius: 20px;

            overflow: hidden;
        }

        .progress-fill {

            height: 100%;

            background: var(--cyan);

            border-radius: 20px;
        }


        /* STATUS */

        .status {

            display: inline-flex;

            align-items: center;

            gap: 6px;

            padding: 7px 10px;

            border-radius: 30px;

            font-size: 10px;

            font-weight: 900;
        }

        .status::before {

            content: "";

            width: 6px;
            height: 6px;

            border-radius: 50%;
        }

        .status.active {

            color: var(--green);

            border: 1px solid rgba(19, 227, 162, .28);

            background: rgba(19, 227, 162, .08);
        }

        .status.active::before {

            background: var(--green);
        }

        .status.inactive {

            color: #ff7c85;

            border: 1px solid rgba(255, 95, 109, .28);

            background: rgba(255, 95, 109, .08);
        }

        .status.inactive::before {

            background: var(--red);
        }


        /* ACTIONS */

        .actions {

            display: flex;

            gap: 6px;
        }

        .action-btn {

            width: 35px;
            height: 35px;

            border-radius: 11px;

            border: 1px solid var(--border);

            background: #092f41;

            color: #91b6c4;

            cursor: pointer;

            transition: .2s;
        }

        .action-btn:hover {

            background: var(--cyan);

            color: #033044;
        }


        /* =====================================================
           EMPTY
        ===================================================== */

        .empty {

            text-align: center;

            padding: 60px;

            color: #7096a6;

            display: none;
        }


        /* =====================================================
           MODAL
        ===================================================== */

        .modal {

            position: fixed;

            inset: 0;

            background: rgba(0, 12, 19, .72);

            backdrop-filter: blur(8px);

            z-index: 100;

            display: none;

            align-items: center;

            justify-content: center;

            padding: 20px;
        }

        .modal.show {

            display: flex;
        }

        .modal-box {

            width: 100%;

            max-width: 760px;

            max-height: 90vh;

            overflow-y: auto;

            background:
                linear-gradient(
                    145deg,
                    #07374b,
                    #042737
                );

            border: 1px solid var(--border);

            border-radius: 25px;

            box-shadow:
                0 30px 90px rgba(0,0,0,.45);
        }

        .modal-header {

            padding: 22px 25px;

            border-bottom: 1px solid var(--border);

            display: flex;

            align-items: center;

            justify-content: space-between;
        }

        .modal-header h3 {

            font-size: 20px;
        }

        .close {

            width: 38px;
            height: 38px;

            border: 1px solid var(--border);

            background: #082f41;

            color: white;

            border-radius: 12px;

            cursor: pointer;

            font-size: 20px;
        }

        .form {

            padding: 25px;

            display: grid;

            grid-template-columns:
                repeat(2, minmax(0, 1fr));

            gap: 17px;
        }

        .form-group {

            display: flex;

            flex-direction: column;

            gap: 7px;
        }

        .form-group.full {

            grid-column: 1 / -1;
        }

        .form-group label {

            color: #91b6c4;

            font-size: 12px;

            font-weight: 800;
        }

        .form-group input,
        .form-group select {

            height: 45px;

            border-radius: 12px;

            border: 1px solid var(--border);

            background: #042a3b;

            color: white;

            padding: 0 13px;

            outline: none;
        }

        .modal-footer {

            padding: 18px 25px;

            border-top: 1px solid var(--border);

            display: flex;

            justify-content: flex-end;

            gap: 10px;
        }

        .btn-secondary {

            padding: 13px 20px;

            border-radius: 13px;

            border: 1px solid var(--border);

            background: transparent;

            color: #9bb9c6;

            font-weight: 800;

            cursor: pointer;
        }


        /* =====================================================
           RESPONSIVE
        ===================================================== */

        @media (max-width: 1100px) {

            .sidebar {

                width: 230px;

                min-width: 230px;
            }

            .main {

                width: calc(100% - 230px);

                margin-left: 230px;
            }

            .summary {

                grid-template-columns:
                    repeat(2, 1fr);
            }

            .content {

                padding: 28px;
            }
        }


        @media (max-width: 850px) {

            .sidebar {

                position: relative;

                width: 100%;

                min-width: 0;

                height: auto;
            }

            .app {

                flex-direction: column;
            }

            .logo-area {

                height: 100px;
            }

            .logo-area img {

                width: 180px;
            }

            .admin-card {

                display: none;
            }

            .nav {

                display: flex;

                overflow-x: auto;

                gap: 5px;
            }

            .nav-title {

                display: none;
            }

            .nav-link {

                min-width: 110px;

                justify-content: center;

                flex-direction: column;

                gap: 5px;
            }

            .nav-link span:last-child {

                font-size: 11px;
            }

            .sidebar-footer {

                display: none;
            }

            .main {

                width: 100%;

                margin-left: 0;
            }

            .topbar {

                padding: 0 20px;

                height: 85px;
            }

            .top-actions {

                display: none;
            }

            .content {

                padding: 22px 18px 40px;
            }

            .content-header {

                align-items: flex-start;

                flex-direction: column;
            }
        }


        @media (max-width: 600px) {

            .summary {

                grid-template-columns: 1fr;
            }

            .form {

                grid-template-columns: 1fr;
            }

            .form-group.full {

                grid-column: auto;
            }

            .page-title {

                font-size: 22px;
            }

            .content-header h2 {

                font-size: 26px;
            }

            .toolbar {

                align-items: stretch;
            }

            .search {

                min-width: 100%;
            }

            .filter {

                flex: 1;
            }
        }

    </style>

</head>

<body>


<div class="app">


    <!-- =====================================================
         SIDEBAR
    ====================================================== -->

    <aside class="sidebar">

        <div class="logo-area">

            <img
                src="{{ asset('images/quantika-logo.png') }}"
                alt="Quantika Pool">

        </div>


        <div class="admin-card">

            <div class="admin-avatar">
                DC
            </div>

            <div class="admin-info">

                <strong>Administrador</strong>

                <span>Panel de control</span>

            </div>

        </div>


        <nav class="nav">


            <div class="nav-title">
                PRINCIPAL
            </div>


            <a
                href="{{ route('admin.dashboard') }}"
                class="nav-link">

                <span class="nav-icon">⌂</span>

                <span>Dashboard</span>

            </a>


            <a
                href="{{ route('alumnos.index') }}"
                class="nav-link active">

                <span class="nav-icon">♙</span>

                <span>Alumnos</span>

            </a>


            <a
                href="#"
                class="nav-link">

                <span class="nav-icon">◎</span>

                <span>Niveles</span>

            </a>


            <a
                href="#"
                class="nav-link">

                <span class="nav-icon">▣</span>

                <span>Clases</span>

            </a>


            <a
                href="#"
                class="nav-link">

                <span class="nav-icon">✓</span>

                <span>Evaluaciones</span>

            </a>


            <a href="{{ route('horarios.index') }}" class="menu-item">

    <div class="menu-icon">
        📅
    </div>

    <span>Horarios</span>

</a>

            <div class="nav-title">
                ADMINISTRACIÓN
            </div>


            <a href="{{ route('instructores.index') }}" class="menu-item">

    <div class="menu-icon">
        ♟
    </div>

    Instructores

</a>

            <a
                href="#"
                class="nav-link">

                <span class="nav-icon">$</span>

                <span>Pagos</span>

            </a>


            <a
                href="#"
                class="nav-link">

                <span class="nav-icon">▤</span>

                <span>Reportes</span>

            </a>


            <div class="nav-title">
                SISTEMA
            </div>


            <a
                href="#"
                class="nav-link">

                <span class="nav-icon">⌂</span>

                <span>Sucursales</span>

            </a>


            <a
                href="#"
                class="nav-link">

                <span class="nav-icon">▦</span>

                <span>Carriles / Alberca</span>

            </a>


            <a
                href="#"
                class="nav-link">

                <span class="nav-icon">⚙</span>

                <span>Configuración</span>

            </a>


        </nav>


        <div class="sidebar-footer">

            QUANTIKA POOL © 2026

        </div>

    </aside>



    <!-- =====================================================
         MAIN
    ====================================================== -->

    <main class="main">


        <!-- TOPBAR -->

        <header class="topbar">


            <div class="top-left">

                <a
                    href="{{ route('admin.dashboard') }}"
                    class="back-button"
                    title="Regresar al Dashboard">

                    ←

                </a>


                <div>

                    <div class="page-label">
                        QUANTIKA POOL · SUCURSAL 1
                    </div>

                    <div class="page-title">
                        Alumnos
                    </div>

                </div>

            </div>


            <div class="top-actions">


                <div class="branch-selector">

                    <div style="display:flex;align-items:center;gap:10px;">

                        <span class="branch-dot"></span>

                        <span>Sucursal 1</span>

                    </div>

                    <span>▼</span>

                </div>


                <div class="notification">
                    🔔
                </div>

            </div>


        </header>



        <!-- =================================================
             CONTENT
        ================================================== -->

        <section class="content">


            <!-- HEADER -->

            <div class="content-header">

                <div>

                    <h2>
                        Gestión de alumnos
                    </h2>

                    <p>
                        Registra, consulta y administra los alumnos de Quantika Pool.
                    </p>

                </div>


                <button
                    class="btn-primary"
                    onclick="abrirModal()">

                    + Registrar alumno

                </button>

            </div>



            <!-- =================================================
                 SUMMARY
            ================================================== -->

            <div class="summary">


                <div class="summary-card">

                    <div class="summary-label">
                        Alumnos registrados
                    </div>

                    <div class="summary-value">
                        248
                    </div>

                    <div class="summary-extra">
                        ↑ 12 este mes
                    </div>

                </div>


                <div class="summary-card">

                    <div class="summary-label">
                        Alumnos activos
                    </div>

                    <div class="summary-value">
                        232
                    </div>

                    <div class="summary-extra">
                        93.5% del total
                    </div>

                </div>


                <div class="summary-card">

                    <div class="summary-label">
                        Principiantes
                    </div>

                    <div class="summary-value">
                        96
                    </div>

                    <div class="summary-extra">
                        Nivel inicial
                    </div>

                </div>


                <div class="summary-card">

                    <div class="summary-label">
                        Avanzados
                    </div>

                    <div class="summary-value">
                        42
                    </div>

                    <div class="summary-extra">
                        Dominio avanzado
                    </div>

                </div>


            </div>



            <!-- =================================================
                 TOOLBAR
            ================================================== -->

            <div class="toolbar">


                <div class="search">

                    <span>⌕</span>

                    <input
                        type="text"
                        id="buscar"
                        placeholder="Buscar alumno por nombre, correo o tutor..."
                        onkeyup="filtrarAlumnos()">

                </div>


                <select
                    class="filter"
                    id="filtroNivel"
                    onchange="filtrarAlumnos()">

                    <option value="">
                        Todos los niveles
                    </option>

                    <option value="Tortuga">
                        🐢 Tortuga
                    </option>

                    <option value="Pez">
                        🐟 Pez
                    </option>

                    <option value="Delfín">
                        🐬 Delfín
                    </option>

                    <option value="Tiburón">
                        🦈 Tiburón
                    </option>

                </select>


                <select
                    class="filter"
                    id="filtroSucursal"
                    onchange="filtrarAlumnos()">

                    <option value="">
                        Todas las sucursales
                    </option>

                    <option value="Sucursal 1">
                        Sucursal 1
                    </option>

                    <option value="Sucursal 2">
                        Sucursal 2
                    </option>

                </select>


                <select
                    class="filter"
                    id="filtroEstado"
                    onchange="filtrarAlumnos()">

                    <option value="">
                        Todos los estados
                    </option>

                    <option value="Activo">
                        Activos
                    </option>

                    <option value="Inactivo">
                        Inactivos
                    </option>

                </select>


            </div>



            <!-- =================================================
                 TABLE
            ================================================== -->

            <div class="table-card">


                <div class="table-head">

                    <h3>
                        Alumnos registrados
                    </h3>

                    <span>
                        Mostrando <strong id="contador">6</strong> alumnos
                    </span>

                </div>


                <div class="table-wrapper">

                    <table>

                        <thead>

                            <tr>

                                <th>
                                    Alumno
                                </th>

                                <th>
                                    Nivel actual
                                </th>

                                <th>
                                    Sucursal
                                </th>

                                <th>
                                    Tutor / Responsable
                                </th>

                                <th>
                                    Asistencia
                                </th>

                                <th>
                                    Estado
                                </th>

                                <th>
                                    Acciones
                                </th>

                            </tr>

                        </thead>


                        <tbody id="tablaAlumnos">


                            <!-- ALUMNO 1 -->

                            <tr
                                data-nombre="María González"
                                data-nivel="Delfín"
                                data-sucursal="Sucursal 1"
                                data-estado="Activo">

                                <td>

                                    <div class="student">

                                        <div class="student-avatar">
                                            MG
                                        </div>

                                        <div>

                                            <div class="student-name">
                                                María González
                                            </div>

                                            <div class="student-email">
                                                maria.gonzalez@email.com
                                            </div>

                                        </div>

                                    </div>

                                </td>


                                <td>

                                    <div class="level">

                                        <div class="level-animal">

                                            <img
                                                src="{{ asset('images/Niveles/delfin.png') }}"
                                                alt="Delfín">

                                        </div>

                                        <div>

                                            <div class="level-name">
                                                Delfín
                                            </div>

                                            <div class="level-number">
                                                Nivel 03
                                            </div>

                                        </div>

                                    </div>

                                </td>


                                <td>
                                    <span class="branch">
                                        Sucursal 1
                                    </span>
                                </td>


                                <td>
                                    Laura González
                                </td>


                                <td>

                                    <div class="progress">

                                        <div class="progress-top">

                                            <span>Asistencia</span>

                                            <strong>95%</strong>

                                        </div>

                                        <div class="progress-bar">

                                            <div
                                                class="progress-fill"
                                                style="width:95%">
                                            </div>

                                        </div>

                                    </div>

                                </td>


                                <td>

                                    <span class="status active">
                                        Activo
                                    </span>

                                </td>


                                <td>

                                    <div class="actions">

                                        <button
                                            class="action-btn"
                                            title="Ver"
                                            onclick="verAlumno('María González')">
                                            ◉
                                        </button>

                                        <button
                                            class="action-btn"
                                            title="Editar"
                                            onclick="editarAlumno('María González')">
                                            ✎
                                        </button>

                                        <button
                                            class="action-btn"
                                            title="Dar de baja"
                                            onclick="bajaAlumno('María González')">
                                            ⋯
                                        </button>

                                    </div>

                                </td>

                            </tr>



                            <!-- ALUMNO 2 -->

                            <tr
                                data-nombre="Carlos Ramírez"
                                data-nivel="Pez"
                                data-sucursal="Sucursal 2"
                                data-estado="Activo">

                                <td>

                                    <div class="student">

                                        <div class="student-avatar">
                                            CR
                                        </div>

                                        <div>

                                            <div class="student-name">
                                                Carlos Ramírez
                                            </div>

                                            <div class="student-email">
                                                carlos.ramirez@email.com
                                            </div>

                                        </div>

                                    </div>

                                </td>


                                <td>

                                    <div class="level">

                                        <div class="level-animal">

                                            <img
                                                src="{{ asset('images/Niveles/pez.png') }}"
                                                alt="Pez">

                                        </div>

                                        <div>

                                            <div class="level-name">
                                                Pez
                                            </div>

                                            <div class="level-number">
                                                Nivel 02
                                            </div>

                                        </div>

                                    </div>

                                </td>


                                <td>
                                    <span class="branch">
                                        Sucursal 2
                                    </span>
                                </td>


                                <td>
                                    Roberto Ramírez
                                </td>


                                <td>

                                    <div class="progress">

                                        <div class="progress-top">

                                            <span>Asistencia</span>

                                            <strong>88%</strong>

                                        </div>

                                        <div class="progress-bar">

                                            <div
                                                class="progress-fill"
                                                style="width:88%">
                                            </div>

                                        </div>

                                    </div>

                                </td>


                                <td>

                                    <span class="status active">
                                        Activo
                                    </span>

                                </td>


                                <td>

                                    <div class="actions">

                                        <button
                                            class="action-btn"
                                            onclick="verAlumno('Carlos Ramírez')">
                                            ◉
                                        </button>

                                        <button
                                            class="action-btn"
                                            onclick="editarAlumno('Carlos Ramírez')">
                                            ✎
                                        </button>

                                        <button
                                            class="action-btn"
                                            onclick="bajaAlumno('Carlos Ramírez')">
                                            ⋯
                                        </button>

                                    </div>

                                </td>

                            </tr>



                            <!-- ALUMNO 3 -->

                            <tr
                                data-nombre="Valentina Sánchez"
                                data-nivel="Tortuga"
                                data-sucursal="Sucursal 1"
                                data-estado="Activo">

                                <td>

                                    <div class="student">

                                        <div class="student-avatar">
                                            VS
                                        </div>

                                        <div>

                                            <div class="student-name">
                                                Valentina Sánchez
                                            </div>

                                            <div class="student-email">
                                                valentina.sanchez@email.com
                                            </div>

                                        </div>

                                    </div>

                                </td>


                                <td>

                                    <div class="level">

                                        <div class="level-animal">

                                            <img
                                                src="{{ asset('images/Niveles/tortuga.png') }}"
                                                alt="Tortuga">

                                        </div>

                                        <div>

                                            <div class="level-name">
                                                Tortuga
                                            </div>

                                            <div class="level-number">
                                                Nivel 01
                                            </div>

                                        </div>

                                    </div>

                                </td>


                                <td>
                                    <span class="branch">
                                        Sucursal 1
                                    </span>
                                </td>


                                <td>
                                    Andrea Sánchez
                                </td>


                                <td>

                                    <div class="progress">

                                        <div class="progress-top">

                                            <span>Asistencia</span>

                                            <strong>76%</strong>

                                        </div>

                                        <div class="progress-bar">

                                            <div
                                                class="progress-fill"
                                                style="width:76%">
                                            </div>

                                        </div>

                                    </div>

                                </td>


                                <td>

                                    <span class="status active">
                                        Activo
                                    </span>

                                </td>


                                <td>

                                    <div class="actions">

                                        <button
                                            class="action-btn"
                                            onclick="verAlumno('Valentina Sánchez')">
                                            ◉
                                        </button>

                                        <button
                                            class="action-btn"
                                            onclick="editarAlumno('Valentina Sánchez')">
                                            ✎
                                        </button>

                                        <button
                                            class="action-btn"
                                            onclick="bajaAlumno('Valentina Sánchez')">
                                            ⋯
                                        </button>

                                    </div>

                                </td>

                            </tr>



                            <!-- ALUMNO 4 -->

                            <tr
                                data-nombre="Diego Martínez"
                                data-nivel="Tiburón"
                                data-sucursal="Sucursal 1"
                                data-estado="Activo">

                                <td>

                                    <div class="student">

                                        <div class="student-avatar">
                                            DM
                                        </div>

                                        <div>

                                            <div class="student-name">
                                                Diego Martínez
                                            </div>

                                            <div class="student-email">
                                                diego.martinez@email.com
                                            </div>

                                        </div>

                                    </div>

                                </td>


                                <td>

                                    <div class="level">

                                        <div class="level-animal">

                                            <img
                                                src="{{ asset('images/Niveles/tiburon.png') }}"
                                                alt="Tiburón">

                                        </div>

                                        <div>

                                            <div class="level-name">
                                                Tiburón
                                            </div>

                                            <div class="level-number">
                                                Nivel 04
                                            </div>

                                        </div>

                                    </div>

                                </td>


                                <td>
                                    <span class="branch">
                                        Sucursal 1
                                    </span>
                                </td>


                                <td>
                                    Patricia Martínez
                                </td>


                                <td>

                                    <div class="progress">

                                        <div class="progress-top">

                                            <span>Asistencia</span>

                                            <strong>91%</strong>

                                        </div>

                                        <div class="progress-bar">

                                            <div
                                                class="progress-fill"
                                                style="width:91%">
                                            </div>

                                        </div>

                                    </div>

                                </td>


                                <td>

                                    <span class="status active">
                                        Activo
                                    </span>

                                </td>


                                <td>

                                    <div class="actions">

                                        <button
                                            class="action-btn"
                                            onclick="verAlumno('Diego Martínez')">
                                            ◉
                                        </button>

                                        <button
                                            class="action-btn"
                                            onclick="editarAlumno('Diego Martínez')">
                                            ✎
                                        </button>

                                        <button
                                            class="action-btn"
                                            onclick="bajaAlumno('Diego Martínez')">
                                            ⋯
                                        </button>

                                    </div>

                                </td>

                            </tr>



                            <!-- ALUMNO 5 -->

                            <tr
                                data-nombre="Sofía Hernández"
                                data-nivel="Delfín"
                                data-sucursal="Sucursal 2"
                                data-estado="Activo">

                                <td>

                                    <div class="student">

                                        <div class="student-avatar">
                                            SH
                                        </div>

                                        <div>

                                            <div class="student-name">
                                                Sofía Hernández
                                            </div>

                                            <div class="student-email">
                                                sofia.hernandez@email.com
                                            </div>

                                        </div>

                                    </div>

                                </td>


                                <td>

                                    <div class="level">

                                        <div class="level-animal">

                                            <img
                                                src="{{ asset('images/Niveles/delfin.png') }}"
                                                alt="Delfín">

                                        </div>

                                        <div>

                                            <div class="level-name">
                                                Delfín
                                            </div>

                                            <div class="level-number">
                                                Nivel 03
                                            </div>

                                        </div>

                                    </div>

                                </td>


                                <td>
                                    <span class="branch">
                                        Sucursal 2
                                    </span>
                                </td>


                                <td>
                                    Mariana Hernández
                                </td>


                                <td>

                                    <div class="progress">

                                        <div class="progress-top">

                                            <span>Asistencia</span>

                                            <strong>93%</strong>

                                        </div>

                                        <div class="progress-bar">

                                            <div
                                                class="progress-fill"
                                                style="width:93%">
                                            </div>

                                        </div>

                                    </div>

                                </td>


                                <td>

                                    <span class="status active">
                                        Activo
                                    </span>

                                </td>


                                <td>

                                    <div class="actions">

                                        <button
                                            class="action-btn"
                                            onclick="verAlumno('Sofía Hernández')">
                                            ◉
                                        </button>

                                        <button
                                            class="action-btn"
                                            onclick="editarAlumno('Sofía Hernández')">
                                            ✎
                                        </button>

                                        <button
                                            class="action-btn"
                                            onclick="bajaAlumno('Sofía Hernández')">
                                            ⋯
                                        </button>

                                    </div>

                                </td>

                            </tr>



                            <!-- ALUMNO 6 -->

                            <tr
                                data-nombre="Mateo López"
                                data-nivel="Pez"
                                data-sucursal="Sucursal 1"
                                data-estado="Inactivo">

                                <td>

                                    <div class="student">

                                        <div class="student-avatar">
                                            ML
                                        </div>

                                        <div>

                                            <div class="student-name">
                                                Mateo López
                                            </div>

                                            <div class="student-email">
                                                mateo.lopez@email.com
                                            </div>

                                        </div>

                                    </div>

                                </td>


                                <td>

                                    <div class="level">

                                        <div class="level-animal">

                                            <img
                                                src="{{ asset('images/Niveles/pez.png') }}"
                                                alt="Pez">

                                        </div>

                                        <div>

                                            <div class="level-name">
                                                Pez
                                            </div>

                                            <div class="level-number">
                                                Nivel 02
                                            </div>

                                        </div>

                                    </div>

                                </td>


                                <td>
                                    <span class="branch">
                                        Sucursal 1
                                    </span>
                                </td>


                                <td>
                                    Jorge López
                                </td>


                                <td>

                                    <div class="progress">

                                        <div class="progress-top">

                                            <span>Asistencia</span>

                                            <strong>61%</strong>

                                        </div>

                                        <div class="progress-bar">

                                            <div
                                                class="progress-fill"
                                                style="width:61%">
                                            </div>

                                        </div>

                                    </div>

                                </td>


                                <td>

                                    <span class="status inactive">
                                        Inactivo
                                    </span>

                                </td>


                                <td>

                                    <div class="actions">

                                        <button
                                            class="action-btn"
                                            onclick="verAlumno('Mateo López')">
                                            ◉
                                        </button>

                                        <button
                                            class="action-btn"
                                            onclick="editarAlumno('Mateo López')">
                                            ✎
                                        </button>

                                        <button
                                            class="action-btn"
                                            onclick="bajaAlumno('Mateo López')">
                                            ⋯
                                        </button>

                                    </div>

                                </td>

                            </tr>


                        </tbody>

                    </table>


                    <div
                        class="empty"
                        id="sinResultados">

                        No se encontraron alumnos con esos criterios.

                    </div>

                </div>

            </div>


        </section>


    </main>

</div>



<!-- =========================================================
     MODAL REGISTRAR
========================================================== -->

<div
    class="modal"
    id="modalAlumno">

    <div class="modal-box">


        <div class="modal-header">

            <h3>
                Registrar nuevo alumno
            </h3>

            <button
                class="close"
                onclick="cerrarModal()">

                ×

            </button>

        </div>


        <form
            class="form"
            onsubmit="registrarAlumno(event)">


            <div class="form-group">

                <label>
                    Nombre
                </label>

                <input
                    type="text"
                    required
                    placeholder="Nombre del alumno">

            </div>


            <div class="form-group">

                <label>
                    Apellidos
                </label>

                <input
                    type="text"
                    required
                    placeholder="Apellidos">

            </div>


            <div class="form-group">

                <label>
                    Fecha de nacimiento
                </label>

                <input
                    type="date"
                    required>

            </div>


            <div class="form-group">

                <label>
                    Teléfono
                </label>

                <input
                    type="tel"
                    placeholder="10 dígitos">

            </div>


            <div class="form-group">

                <label>
                    Correo electrónico
                </label>

                <input
                    type="email"
                    placeholder="correo@ejemplo.com">

            </div>


            <div class="form-group">

                <label>
                    Tutor / Responsable
                </label>

                <input
                    type="text"
                    required
                    placeholder="Nombre del tutor">

            </div>


            <div class="form-group">

                <label>
                    Sucursal
                </label>

                <select required>

                    <option value="">
                        Seleccionar sucursal
                    </option>

                    <option>
                        Sucursal 1
                    </option>

                    <option>
                        Sucursal 2
                    </option>

                </select>

            </div>


            <div class="form-group">

                <label>
                    Nivel actual
                </label>

                <select required>

                    <option value="">
                        Seleccionar nivel
                    </option>

                    <option>
                        Tortuga
                    </option>

                    <option>
                        Pez
                    </option>

                    <option>
                        Delfín
                    </option>

                    <option>
                        Tiburón
                    </option>

                </select>

            </div>


            <div class="form-group full">

                <label>
                    Observaciones
                </label>

                <input
                    type="text"
                    placeholder="Información adicional del alumno">

            </div>


        </form>


        <div class="modal-footer">

            <button
                class="btn-secondary"
                onclick="cerrarModal()">

                Cancelar

            </button>

            <button
                class="btn-primary"
                onclick="registrarAlumno(event)">

                Registrar alumno

            </button>

        </div>


    </div>

</div>



<script>


/* =========================================================
   MODAL
========================================================= */

function abrirModal() {

    document
        .getElementById('modalAlumno')
        .classList
        .add('show');

}


function cerrarModal() {

    document
        .getElementById('modalAlumno')
        .classList
        .remove('show');

}


window.onclick = function(event) {

    const modal =
        document.getElementById('modalAlumno');

    if (event.target === modal) {

        cerrarModal();

    }

};



/* =========================================================
   BUSCADOR Y FILTROS
========================================================= */

function filtrarAlumnos() {

    const busqueda =
        document
        .getElementById('buscar')
        .value
        .toLowerCase();

    const nivel =
        document
        .getElementById('filtroNivel')
        .value;

    const sucursal =
        document
        .getElementById('filtroSucursal')
        .value;

    const estado =
        document
        .getElementById('filtroEstado')
        .value;


    const filas =
        document.querySelectorAll(
            '#tablaAlumnos tr'
        );


    let visibles = 0;


    filas.forEach(function(fila) {


        const nombre =
            fila.dataset.nombre
            .toLowerCase();


        const nivelFila =
            fila.dataset.nivel;


        const sucursalFila =
            fila.dataset.sucursal;


        const estadoFila =
            fila.dataset.estado;


        const coincideBusqueda =
            nombre.includes(busqueda);


        const coincideNivel =
            !nivel ||
            nivelFila === nivel;


        const coincideSucursal =
            !sucursal ||
            sucursalFila === sucursal;


        const coincideEstado =
            !estado ||
            estadoFila === estado;


        if (
            coincideBusqueda &&
            coincideNivel &&
            coincideSucursal &&
            coincideEstado
        ) {

            fila.style.display = '';

            visibles++;

        } else {

            fila.style.display = 'none';

        }

    });


    document
        .getElementById('contador')
        .textContent = visibles;


    document
        .getElementById('sinResultados')
        .style.display =
            visibles === 0
                ? 'block'
                : 'none';

}



/* =========================================================
   REGISTRAR
========================================================= */

function registrarAlumno(event) {

    if (event) {

        event.preventDefault();

    }

    alert(
        'Alumno listo para registrarse.\\n\\n' +
        'En el siguiente paso conectaremos este formulario con la base de datos.'
    );

    cerrarModal();

}



/* =========================================================
   VER ALUMNO
========================================================= */

function verAlumno(nombre) {

    alert(
        'Historial del alumno: ' +
        nombre +
        '\\n\\n' +
        'Aquí mostraremos posteriormente:\\n' +
        '• Datos personales\\n' +
        '• Tutor\\n' +
        '• Nivel\\n' +
        '• Asistencia\\n' +
        '• Evaluaciones\\n' +
        '• Clases\\n' +
        '• Historial de cambios'
    );

}



/* =========================================================
   EDITAR
========================================================= */

function editarAlumno(nombre) {

    alert(
        'Editar alumno: ' +
        nombre +
        '\\n\\n' +
        'El formulario de edición se conectará a la base de datos posteriormente.'
    );

}



/* =========================================================
   BAJA
========================================================= */

function bajaAlumno(nombre) {

    const confirmar =
        confirm(
            '¿Deseas realizar la baja del alumno ' +
            nombre +
            '?\\n\\n' +
            'Después podremos elegir entre baja temporal o definitiva.'
        );


    if (confirmar) {

        alert(
            'Alumno seleccionado para baja: ' +
            nombre
        );

    }

}


</script>


</body>
</html>