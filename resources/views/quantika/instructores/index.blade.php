<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Instructores | Quantika Pool</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #031f2f;
            color: #ffffff;
            min-height: 100vh;
        }

        /* =========================
           CONTENEDOR
        ========================= */

        .page {
            min-height: 100vh;
            padding: 35px;
            background:
                radial-gradient(
                    circle at top right,
                    rgba(39, 201, 235, .08),
                    transparent 35%
                ),
                #031f2f;
        }

        /* =========================
           ENCABEZADO
        ========================= */

        .top-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            gap: 20px;
        }

        .header-left h1 {
            font-size: 38px;
            font-weight: 800;
            margin-bottom: 8px;
        }

        .header-left p {
            color: #8fb2c4;
            font-size: 16px;
        }

        .back-button {
            text-decoration: none;
            color: #ffffff;
            border: 1px solid rgba(65, 208, 235, .3);
            background: #07384d;
            padding: 12px 18px;
            border-radius: 12px;
            transition: .2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .back-button:hover {
            background: #0b526c;
            transform: translateY(-2px);
        }

        /* =========================
           BOTONES
        ========================= */

        .actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 30px;
        }

        .btn {
            border: none;
            padding: 13px 20px;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: #42d4eb;
            color: #032435;
        }

        .btn-secondary {
            background: #083b50;
            border: 1px solid rgba(65, 208, 235, .25);
            color: white;
        }

        /* =========================
           ESTADÍSTICAS
        ========================= */

        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: linear-gradient(
                145deg,
                #07394e,
                #052d40
            );

            border: 1px solid rgba(69, 207, 234, .18);
            border-radius: 18px;
            padding: 22px;
            position: relative;
            overflow: hidden;
        }

        .stat-card::after {
            content: "";
            position: absolute;
            width: 100px;
            height: 100px;
            right: -45px;
            bottom: -55px;
            background: rgba(39, 205, 235, .08);
            border-radius: 50%;
        }

        .stat-title {
            color: #8db1c3;
            font-size: 14px;
            margin-bottom: 12px;
        }

        .stat-number {
            font-size: 30px;
            font-weight: 800;
        }

        .stat-description {
            color: #40d9c0;
            font-size: 13px;
            margin-top: 5px;
        }

        /* =========================
           PANEL
        ========================= */

        .panel {
            background: rgba(5, 45, 62, .9);
            border: 1px solid rgba(65, 208, 235, .18);
            border-radius: 20px;
            overflow: hidden;
            margin-bottom: 30px;
        }

        .panel-header {
            padding: 22px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }

        .panel-header h2 {
            font-size: 21px;
        }

        .panel-header span {
            color: #71a5ba;
            font-size: 14px;
        }

        /* =========================
           TABLA
        ========================= */

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 16px 22px;
            font-size: 12px;
            color: #72a5b8;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        td {
            padding: 18px 22px;
            border-top: 1px solid rgba(255,255,255,.06);
            color: #d8e8ee;
        }

        tr:hover {
            background: rgba(47, 207, 235, .04);
        }

        /* =========================
           INSTRUCTOR
        ========================= */

        .instructor {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .avatar {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: #42d4eb;
            color: #063044;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
        }

        .instructor-info strong {
            display: block;
            margin-bottom: 4px;
        }

        .instructor-info small {
            color: #719caf;
        }

        /* =========================
           SUCURSAL
        ========================= */

        .branch {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: rgba(65,208,235,.08);
            border: 1px solid rgba(65,208,235,.15);
            padding: 7px 11px;
            border-radius: 20px;
            font-size: 13px;
        }

        .dot {
            width: 7px;
            height: 7px;
            background: #42d4eb;
            border-radius: 50%;
        }

        /* =========================
           DISPONIBILIDAD
        ========================= */

        .available {
            color: #39e1bd;
            font-weight: 700;
        }

        .busy {
            color: #ffbf38;
            font-weight: 700;
        }

        /* =========================
           ACCIONES TABLA
        ========================= */

        .table-actions {
            display: flex;
            gap: 8px;
        }

        .icon-btn {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,.08);
            background: #08384d;
            color: white;
            cursor: pointer;
        }

        .icon-btn:hover {
            background: #0c526a;
        }

        /* =========================
           RESPONSIVE
        ========================= */

        @media(max-width: 1100px) {

            .stats {
                grid-template-columns: repeat(2, 1fr);
            }

        }

        @media(max-width: 700px) {

            .page {
                padding: 20px;
            }

            .top-header {
                align-items: flex-start;
                flex-direction: column;
            }

            .header-left h1 {
                font-size: 30px;
            }

            .stats {
                grid-template-columns: 1fr;
            }

            .panel-header {
                align-items: flex-start;
                flex-direction: column;
                gap: 8px;
            }

            th,
            td {
                white-space: nowrap;
            }

        }

    </style>
</head>

<body>

<div class="page">

    <!-- ENCABEZADO -->

    <div class="top-header">

        <div class="header-left">

            <h1>Instructores</h1>

            <p>
                Administración de instructores y disponibilidad.
            </p>

        </div>

        <a href="{{ route('admin.dashboard') }}" class="back-button">
            ← Volver al Dashboard
        </a>

    </div>


    <!-- BOTONES -->

    <div class="actions">

        <a href="#" class="btn btn-primary">
            + Registrar instructor
        </a>

        <a href="#" class="btn btn-secondary">
            Disponibilidad
        </a>

        <a href="#" class="btn btn-secondary">
            Horarios
        </a>

    </div>


    <!-- ESTADÍSTICAS -->

    <div class="stats">

        <div class="stat-card">

            <div class="stat-title">
                Instructores registrados
            </div>

            <div class="stat-number">
                18
            </div>

            <div class="stat-description">
                ↑ 3 este mes
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-title">
                Disponibles hoy
            </div>

            <div class="stat-number">
                16
            </div>

            <div class="stat-description">
                Disponibilidad activa
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-title">
                Sucursal 1
            </div>

            <div class="stat-number">
                10
            </div>

            <div class="stat-description">
                Instructores asignados
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-title">
                Sucursal 2
            </div>

            <div class="stat-number">
                8
            </div>

            <div class="stat-description">
                Instructores asignados
            </div>

        </div>

    </div>


    <!-- LISTA -->

    <div class="panel">

        <div class="panel-header">

            <h2>Equipo de instructores</h2>

            <span>
                Administración del personal
            </span>

        </div>


        <div class="table-container">

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
                            Especialidad
                        </th>

                        <th>
                            Disponibilidad
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

                                <div class="avatar">
                                    MG
                                </div>

                                <div class="instructor-info">

                                    <strong>
                                        Mariana García
                                    </strong>

                                    <small>
                                        mariana@quantika.com
                                    </small>

                                </div>

                            </div>

                        </td>


                        <td>

                            <span class="branch">

                                <span class="dot"></span>

                                Sucursal 1

                            </span>

                        </td>


                        <td>
                            Nivel avanzado
                        </td>


                        <td class="available">
                            Disponible
                        </td>


                        <td class="available">
                            ● Activo
                        </td>


                        <td>

                            <div class="table-actions">

                                <button class="icon-btn">
                                    👁
                                </button>

                                <button class="icon-btn">
                                    ✎
                                </button>

                                <button class="icon-btn">
                                    ⋮
                                </button>

                            </div>

                        </td>

                    </tr>


                    <tr>

                        <td>

                            <div class="instructor">

                                <div class="avatar">
                                    CR
                                </div>

                                <div class="instructor-info">

                                    <strong>
                                        Carlos Ramírez
                                    </strong>

                                    <small>
                                        carlos@quantika.com
                                    </small>

                                </div>

                            </div>

                        </td>


                        <td>

                            <span class="branch">

                                <span class="dot"></span>

                                Sucursal 1

                            </span>

                        </td>


                        <td>
                            Nivel intermedio
                        </td>


                        <td class="busy">
                            En clase
                        </td>


                        <td class="available">
                            ● Activo
                        </td>


                        <td>

                            <div class="table-actions">

                                <button class="icon-btn">
                                    👁
                                </button>

                                <button class="icon-btn">
                                    ✎
                                </button>

                                <button class="icon-btn">
                                    ⋮
                                </button>

                            </div>

                        </td>

                    </tr>


                    <tr>

                        <td>

                            <div class="instructor">

                                <div class="avatar">
                                    VS
                                </div>

                                <div class="instructor-info">

                                    <strong>
                                        Valentina Sánchez
                                    </strong>

                                    <small>
                                        valentina@quantika.com
                                    </small>

                                </div>

                            </div>

                        </td>


                        <td>

                            <span class="branch">

                                <span class="dot"></span>

                                Sucursal 2

                            </span>

                        </td>


                        <td>
                            Nivel avanzado
                        </td>


                        <td class="available">
                            Disponible
                        </td>


                        <td class="available">
                            ● Activo
                        </td>


                        <td>

                            <div class="table-actions">

                                <button class="icon-btn">
                                    👁
                                </button>

                                <button class="icon-btn">
                                    ✎
                                </button>

                                <button class="icon-btn">
                                    ⋮
                                </button>

                            </div>

                        </td>

                    </tr>


                </tbody>

            </table>

        </div>

    </div>


    <!-- FUNCIONES DEL PORTAL -->

    <div class="panel">

        <div class="panel-header">

            <h2>Portal de instructores</h2>

            <span>
                Funciones disponibles
            </span>

        </div>


        <div style="
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
            gap:15px;
            padding:25px;
        ">


            <div class="stat-card">

                <div class="stat-title">
                    Agenda
                </div>

                <div style="font-size:15px;">
                    Agenda diaria y semanal.
                </div>

            </div>


            <div class="stat-card">

                <div class="stat-title">
                    Alumnos
                </div>

                <div style="font-size:15px;">
                    Consulta de alumnos por grupo y carril.
                </div>

            </div>


            <div class="stat-card">

                <div class="stat-title">
                    Asistencia
                </div>

                <div style="font-size:15px;">
                    Registro de asistencia.
                </div>

            </div>


            <div class="stat-card">

                <div class="stat-title">
                    Evaluaciones
                </div>

                <div style="font-size:15px;">
                    Evaluación por clase y criterio.
                </div>

            </div>


            <div class="stat-card">

                <div class="stat-title">
                    Habilidades
                </div>

                <div style="font-size:15px;">
                    No iniciado · En proceso · Logrado.
                </div>

            </div>


            <div class="stat-card">

                <div class="stat-title">
                    Observaciones
                </div>

                <div style="font-size:15px;">
                    Comentarios y observaciones.
                </div>

            </div>


        </div>

    </div>


</div>

</body>
</html>