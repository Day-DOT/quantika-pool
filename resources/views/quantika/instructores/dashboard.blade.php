<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Instructores | Quantika Pool</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #021d2b;
            color: #f4fbff;
            min-height: 100vh;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        /* =====================================================
           CONTENEDOR
        ===================================================== */

        .app {
            display: flex;
            min-height: 100vh;
        }


        /* =====================================================
           SIDEBAR
        ===================================================== */

        .sidebar {
            width: 300px;
            min-width: 300px;
            background: #032537;
            border-right: 1px solid rgba(71, 208, 235, .14);

            display: flex;
            flex-direction: column;

            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;

            z-index: 20;
        }


        /* LOGO */

        .logo-area {
            height: 195px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-bottom: 1px solid rgba(71, 208, 235, .10);
        }

        .logo-area img {
            width: 220px;
            max-width: 80%;
            height: auto;
            object-fit: contain;
        }


        /* USUARIO */

        .user-card {
            margin: 42px 28px 30px;

            padding: 20px;

            border: 1px solid rgba(71, 208, 235, .18);
            border-radius: 24px;

            background: rgba(10, 61, 82, .45);

            display: flex;
            align-items: center;
            gap: 16px;
        }

        .avatar {
            width: 64px;
            height: 64px;

            border-radius: 18px;

            background: #3ed5ee;

            color: #063246;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 21px;
            font-weight: 900;
        }

        .user-info strong {
            display: block;

            font-size: 17px;

            margin-bottom: 7px;
        }

        .user-info span {
            color: #7da5b7;

            font-size: 14px;
        }


        /* MENU */

        .menu {
            padding: 0 20px;

            overflow-y: auto;

            flex: 1;
        }

        .menu-title {
            color: #668b9d;

            font-size: 12px;

            letter-spacing: 3px;

            font-weight: 800;

            margin: 24px 18px 14px;
        }

        .menu-item {
            height: 64px;

            display: flex;
            align-items: center;

            gap: 16px;

            padding: 0 18px;

            margin-bottom: 8px;

            border-radius: 18px;

            color: #8eafbd;

            font-weight: 700;

            transition: .2s ease;
        }

        .menu-item:hover {
            background: rgba(20, 93, 119, .38);

            color: #fff;
        }

        .menu-item.active {
            background: #0b435c;

            color: #43d8f1;

            border-left: 4px solid #43d8f1;
        }

        .menu-icon {
            width: 44px;
            height: 44px;

            border-radius: 15px;

            background: rgba(20, 79, 102, .60);

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 19px;
        }

        .menu-item.active .menu-icon {
            background: #40d4ed;

            color: #063246;
        }


        /* FOOTER SIDEBAR */

        .sidebar-footer {
            padding: 20px;

            text-align: center;

            border-top: 1px solid rgba(71, 208, 235, .10);

            color: #507c90;

            font-size: 12px;
        }


        /* =====================================================
           CONTENIDO
        ===================================================== */

        .main {
            margin-left: 300px;

            width: calc(100% - 300px);

            min-height: 100vh;

            background:
                radial-gradient(
                    circle at 80% 0%,
                    rgba(18, 104, 132, .20),
                    transparent 35%
                ),
                #021d2b;
        }


        /* =====================================================
           TOPBAR
        ===================================================== */

        .topbar {
            height: 120px;

            border-bottom: 1px solid rgba(71, 208, 235, .10);

            display: flex;
            align-items: center;
            justify-content: space-between;

            padding: 0 42px;
        }

        .top-title small {
            display: block;

            color: #42d7f0;

            letter-spacing: 4px;

            font-weight: 900;

            font-size: 12px;

            margin-bottom: 8px;
        }

        .top-title h1 {
            font-size: 32px;

            font-weight: 900;
        }

        .top-actions {
            display: flex;

            align-items: center;

            gap: 14px;
        }

        .branch {
            background: #0a3d53;

            border: 1px solid rgba(71, 208, 235, .20);

            padding: 15px 22px;

            border-radius: 17px;

            min-width: 180px;

            font-weight: 800;
        }

        .branch span {
            color: #42d7f0;

            margin-right: 10px;
        }

        .notification {
            width: 52px;
            height: 52px;

            border-radius: 16px;

            background: #0a3d53;

            border: 1px solid rgba(71, 208, 235, .18);

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 20px;
        }


        /* =====================================================
           CONTENIDO PRINCIPAL
        ===================================================== */

        .content {
            padding: 38px 42px 60px;
        }


        /* ENCABEZADO */

        .page-header {
            display: flex;

            justify-content: space-between;

            align-items: flex-end;

            margin-bottom: 30px;
        }

        .page-header h2 {
            font-size: 32px;

            margin-bottom: 8px;
        }

        .page-header p {
            color: #7da6b8;

            font-size: 15px;
        }


        .btn-primary {
            background: #40d4ed;

            color: #032537;

            border: none;

            border-radius: 15px;

            padding: 15px 23px;

            font-size: 14px;

            font-weight: 900;

            cursor: pointer;

            transition: .2s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);

            box-shadow: 0 10px 30px rgba(64, 212, 237, .20);
        }


        /* =====================================================
           ESTADISTICAS
        ===================================================== */

        .stats {
            display: grid;

            grid-template-columns: repeat(4, 1fr);

            gap: 18px;

            margin-bottom: 30px;
        }

        .stat-card {
            background: #063247;

            border: 1px solid rgba(71, 208, 235, .16);

            border-radius: 20px;

            padding: 22px;

            position: relative;

            overflow: hidden;
        }

        .stat-card::after {
            content: "";

            position: absolute;

            width: 90px;
            height: 90px;

            right: -30px;
            bottom: -35px;

            border-radius: 50%;

            background: rgba(61, 211, 237, .07);
        }

        .stat-card small {
            color: #76a3b5;

            font-weight: 700;
        }

        .stat-number {
            font-size: 30px;

            font-weight: 900;

            margin-top: 10px;
        }

        .stat-text {
            color: #42d7f0;

            font-size: 12px;

            font-weight: 800;

            margin-top: 5px;
        }


        /* =====================================================
           BUSQUEDA
        ===================================================== */

        .toolbar {
            background: #063247;

            border: 1px solid rgba(71, 208, 235, .14);

            border-radius: 20px;

            padding: 18px;

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 20px;

            margin-bottom: 20px;
        }

        .search {
            flex: 1;

            position: relative;
        }

        .search input {
            width: 100%;

            height: 48px;

            border-radius: 13px;

            border: 1px solid rgba(71, 208, 235, .16);

            background: #04283a;

            color: white;

            padding: 0 18px;

            outline: none;

            font-size: 14px;
        }

        .search input:focus {
            border-color: #40d4ed;
        }

        .filter {
            height: 48px;

            background: #04283a;

            border: 1px solid rgba(71, 208, 235, .16);

            color: #a4c3cf;

            padding: 0 18px;

            border-radius: 13px;

            outline: none;
        }


        /* =====================================================
           TABLA
        ===================================================== */

        .table-card {
            background: #063247;

            border: 1px solid rgba(71, 208, 235, .14);

            border-radius: 22px;

            overflow: hidden;
        }

        .table-header {
            padding: 22px 25px;

            display: flex;

            justify-content: space-between;

            align-items: center;

            border-bottom: 1px solid rgba(71, 208, 235, .10);
        }

        .table-header h3 {
            font-size: 18px;
        }

        .table-header span {
            color: #638c9d;

            font-size: 13px;
        }

        table {
            width: 100%;

            border-collapse: collapse;
        }

        th {
            text-align: left;

            padding: 16px 22px;

            color: #628e9f;

            font-size: 11px;

            letter-spacing: 1px;

            text-transform: uppercase;

            background: rgba(1, 29, 43, .35);
        }

        td {
            padding: 18px 22px;

            border-top: 1px solid rgba(71, 208, 235, .08);

            font-size: 14px;
        }

        tr:hover td {
            background: rgba(17, 91, 115, .16);
        }


        /* INSTRUCTOR */

        .instructor {
            display: flex;

            align-items: center;

            gap: 12px;
        }

        .instructor-avatar {
            width: 42px;
            height: 42px;

            border-radius: 13px;

            background: #40d4ed;

            color: #063246;

            display: flex;
            align-items: center;
            justify-content: center;

            font-weight: 900;
        }

        .instructor-name strong {
            display: block;

            margin-bottom: 4px;
        }

        .instructor-name span {
            color: #6e9aaa;

            font-size: 12px;
        }


        /* BADGES */

        .badge {
            display: inline-flex;

            align-items: center;

            gap: 6px;

            padding: 7px 11px;

            border-radius: 30px;

            font-size: 11px;

            font-weight: 800;
        }

        .badge-active {
            color: #21e5b0;

            background: rgba(33, 229, 176, .08);

            border: 1px solid rgba(33, 229, 176, .20);
        }

        .badge-available {
            color: #42d7f0;

            background: rgba(66, 215, 240, .08);

            border: 1px solid rgba(66, 215, 240, .20);
        }

        .badge-busy {
            color: #ffbd38;

            background: rgba(255, 189, 56, .08);

            border: 1px solid rgba(255, 189, 56, .20);
        }


        /* SUCURSAL */

        .branch-tag {
            color: #9dc1ce;

            font-size: 13px;
        }


        /* ACCIONES */

        .actions {
            display: flex;

            gap: 7px;
        }

        .action {
            width: 36px;
            height: 36px;

            border-radius: 10px;

            border: 1px solid rgba(71, 208, 235, .12);

            background: #0a4055;

            color: #9cc1ce;

            display: flex;
            align-items: center;
            justify-content: center;

            cursor: pointer;
        }

        .action:hover {
            color: #40d4ed;

            border-color: rgba(64, 212, 237, .35);
        }


        /* =====================================================
           RESPONSIVE
        ===================================================== */

        @media (max-width: 1100px) {

            .sidebar {
                width: 240px;
                min-width: 240px;
            }

            .main {
                margin-left: 240px;
                width: calc(100% - 240px);
            }

            .stats {
                grid-template-columns: repeat(2, 1fr);
            }

            .content {
                padding: 30px;
            }

            .topbar {
                padding: 0 30px;
            }
        }


        @media (max-width: 850px) {

            .sidebar {
                position: relative;

                width: 100%;
                min-width: 100%;

                height: auto;

                min-height: auto;
            }

            .app {
                display: block;
            }

            .main {
                margin-left: 0;

                width: 100%;
            }

            .logo-area {
                height: 130px;
            }

            .menu {
                display: flex;

                overflow-x: auto;

                padding: 10px;
            }

            .menu-title,
            .user-card,
            .sidebar-footer {
                display: none;
            }

            .menu-item {
                min-width: 150px;

                margin: 0 5px;
            }

            .topbar {
                height: auto;

                padding: 20px;

                gap: 15px;

                flex-wrap: wrap;
            }

            .content {
                padding: 25px 20px;
            }

            .stats {
                grid-template-columns: repeat(2, 1fr);
            }

            .table-card {
                overflow-x: auto;
            }

            table {
                min-width: 850px;
            }
        }


        @media (max-width: 600px) {

            .stats {
                grid-template-columns: 1fr;
            }

            .page-header {
                flex-direction: column;

                align-items: flex-start;

                gap: 18px;
            }

            .toolbar {
                flex-direction: column;

                align-items: stretch;
            }

            .top-actions {
                width: 100%;
            }

            .branch {
                flex: 1;
            }

            .page-header h2 {
                font-size: 27px;
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
                alt="Quantika Pool"
            >

        </div>


        <div class="user-card">

            <div class="avatar">
                DC
            </div>

            <div class="user-info">

                <strong>Administrador</strong>

                <span>Panel de control</span>

            </div>

        </div>


        <nav class="menu">

            <div class="menu-title">
                PRINCIPAL
            </div>


            <a href="{{ route('admin.dashboard') }}" class="menu-item">

                <div class="menu-icon">
                    ⌂
                </div>

                Dashboard

            </a>


            <a href="{{ route('alumnos.index') }}" class="menu-item">

                <div class="menu-icon">
                    ♙
                </div>

                Alumnos

            </a>


            <a href="#" class="menu-item">

                <div class="menu-icon">
                    ◎
                </div>

                Niveles

            </a>


            <a href="#" class="menu-item">

                <div class="menu-icon">
                    ▣
                </div>

                Clases

            </a>


            <a href="#" class="menu-item">

                <div class="menu-icon">
                    ✓
                </div>

                Evaluaciones

            </a>


            <a href="#" class="menu-item">

                <div class="menu-icon">
                    ▣
                </div>

                Horarios

            </a>


            <div class="menu-title">
                ADMINISTRACIÓN
            </div>


            <!-- ESTE ES EL BOTÓN DE INSTRUCTORES -->

            <a href="{{ route('instructores.index') }}"
               class="menu-item active">

                <div class="menu-icon">
                    ♟
                </div>

                Instructores

            </a>


            <a href="#" class="menu-item">

                <div class="menu-icon">
                    $
                </div>

                Pagos

            </a>


            <a href="#" class="menu-item">

                <div class="menu-icon">
                    ▤
                </div>

                Reportes

            </a>


            <div class="menu-title">
                SISTEMA
            </div>


            <a href="#" class="menu-item">

                <div class="menu-icon">
                    ⚙
                </div>

                Configuración

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

            <div class="top-title">

                <small>
                    QUANTIKA POOL · SUCURSAL 1
                </small>

                <h1>
                    Instructores
                </h1>

            </div>


            <div class="top-actions">

                <div class="branch">

                    <span>●</span>

                    Sucursal 1

                </div>


                <div class="notification">
                    🔔
                </div>

            </div>

        </header>



        <!-- CONTENIDO -->

        <section class="content">


            <!-- ENCABEZADO -->

            <div class="page-header">

                <div>

                    <h2>
                        Personal e instructores
                    </h2>

                    <p>
                        Administra instructores, disponibilidad y sucursales.
                    </p>

                </div>


                <button class="btn-primary">
                    + Nuevo instructor
                </button>

            </div>



            <!-- ESTADISTICAS -->

            <div class="stats">


                <div class="stat-card">

                    <small>
                        Instructores activos
                    </small>

                    <div class="stat-number">
                        18
                    </div>

                    <div class="stat-text">
                        ↑ 3 este mes
                    </div>

                </div>


                <div class="stat-card">

                    <small>
                        Disponibles hoy
                    </small>

                    <div class="stat-number">
                        12
                    </div>

                    <div class="stat-text">
                        Listos para clases
                    </div>

                </div>


                <div class="stat-card">

                    <small>
                        Clases asignadas
                    </small>

                    <div class="stat-number">
                        24
                    </div>

                    <div class="stat-text">
                        Hoy
                    </div>

                </div>


                <div class="stat-card">

                    <small>
                        Ambas sucursales
                    </small>

                    <div class="stat-number">
                        6
                    </div>

                    <div class="stat-text">
                        Instructores
                    </div>

                </div>


            </div>



            <!-- BUSCADOR -->

            <div class="toolbar">

                <div class="search">

                    <input
                        type="text"
                        placeholder="Buscar instructor por nombre..."
                    >

                </div>


                <select class="filter">

                    <option>
                        Todas las sucursales
                    </option>

                    <option>
                        Sucursal 1
                    </option>

                    <option>
                        Sucursal 2
                    </option>

                </select>


                <select class="filter">

                    <option>
                        Todos los estados
                    </option>

                    <option>
                        Disponible
                    </option>

                    <option>
                        En clase
                    </option>

                </select>

            </div>



            <!-- TABLA -->

            <div class="table-card">


                <div class="table-header">

                    <h3>
                        Instructores registrados
                    </h3>

                    <span>
                        18 instructores
                    </span>

                </div>



                <table>

                    <thead>

                        <tr>

                            <th>
                                Instructor
                            </th>

                            <th>
                                Sucursal
                            </th>

                            <th>
                                Disponibilidad
                            </th>

                            <th>
                                Clases hoy
                            </th>

                            <th>
                                Estado
                            </th>

                            <th>
                                Acciones
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                        <tr>

                            <td>

                                <div class="instructor">

                                    <div class="instructor-avatar">
                                        AG
                                    </div>

                                    <div class="instructor-name">

                                        <strong>
                                            Ana García
                                        </strong>

                                        <span>
                                            Instructora de natación
                                        </span>

                                    </div>

                                </div>

                            </td>


                            <td>
                                <span class="branch-tag">
                                    ● Sucursal 1
                                </span>
                            </td>


                            <td>

                                <span class="badge badge-available">
                                    Disponible
                                </span>

                            </td>


                            <td>
                                4 clases
                            </td>


                            <td>

                                <span class="badge badge-active">
                                    ● Activo
                                </span>

                            </td>


                            <td>

                                <div class="actions">

                                    <button class="action">
                                        👁
                                    </button>

                                    <button class="action">
                                        ✎
                                    </button>

                                    <button class="action">
                                        ⋯
                                    </button>

                                </div>

                            </td>

                        </tr>



                        <tr>

                            <td>

                                <div class="instructor">

                                    <div class="instructor-avatar">
                                        CR
                                    </div>

                                    <div class="instructor-name">

                                        <strong>
                                            Carlos Ramírez
                                        </strong>

                                        <span>
                                            Instructor de natación
                                        </span>

                                    </div>

                                </div>

                            </td>


                            <td>
                                <span class="branch-tag">
                                    ● Sucursal 1 · Sucursal 2
                                </span>
                            </td>


                            <td>

                                <span class="badge badge-busy">
                                    En clase
                                </span>

                            </td>


                            <td>
                                5 clases
                            </td>


                            <td>

                                <span class="badge badge-active">
                                    ● Activo
                                </span>

                            </td>


                            <td>

                                <div class="actions">

                                    <button class="action">
                                        👁
                                    </button>

                                    <button class="action">
                                        ✎
                                    </button>

                                    <button class="action">
                                        ⋯
                                    </button>

                                </div>

                            </td>

                        </tr>



                        <tr>

                            <td>

                                <div class="instructor">

                                    <div class="instructor-avatar">
                                        VM
                                    </div>

                                    <div class="instructor-name">

                                        <strong>
                                            Valeria Martínez
                                        </strong>

                                        <span>
                                            Instructora de natación
                                        </span>

                                    </div>

                                </div>

                            </td>


                            <td>
                                <span class="branch-tag">
                                    ● Sucursal 2
                                </span>
                            </td>


                            <td>

                                <span class="badge badge-available">
                                    Disponible
                                </span>

                            </td>


                            <td>
                                3 clases
                            </td>


                            <td>

                                <span class="badge badge-active">
                                    ● Activo
                                </span>

                            </td>


                            <td>

                                <div class="actions">

                                    <button class="action">
                                        👁
                                    </button>

                                    <button class="action">
                                        ✎
                                    </button>

                                    <button class="action">
                                        ⋯
                                    </button>

                                </div>

                            </td>

                        </tr>



                        <tr>

                            <td>

                                <div class="instructor">

                                    <div class="instructor-avatar">
                                        JL
                                    </div>

                                    <div class="instructor-name">

                                        <strong>
                                            Jorge López
                                        </strong>

                                        <span>
                                            Instructor de natación
                                        </span>

                                    </div>

                                </div>

                            </td>


                            <td>
                                <span class="branch-tag">
                                    ● Sucursal 1
                                </span>
                            </td>


                            <td>

                                <span class="badge badge-busy">
                                    En clase
                                </span>

                            </td>


                            <td>
                                6 clases
                            </td>


                            <td>

                                <span class="badge badge-active">
                                    ● Activo
                                </span>

                            </td>


                            <td>

                                <div class="actions">

                                    <button class="action">
                                        👁
                                    </button>

                                    <button class="action">
                                        ✎
                                    </button>

                                    <button class="action">
                                        ⋯
                                    </button>

                                </div>

                            </td>

                        </tr>


                    </tbody>

                </table>

            </div>


        </section>

    </main>

</div>

</body>
</html>