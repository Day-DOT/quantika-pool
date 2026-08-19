<!DOCTYPE html>
<html lang="es">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Pagos | Quantika Pool</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;

            background:
                radial-gradient(
                    circle at 85% 0%,
                    rgba(38, 198, 218, .10),
                    transparent 35%
                ),
                radial-gradient(
                    circle at 0% 100%,
                    rgba(20, 130, 160, .08),
                    transparent 30%
                ),
                #031d2b;

            color: #fff;
            min-height: 100vh;
        }

        .page {
            width: 100%;
            max-width: 1500px;
            margin: auto;
            padding: 30px;
        }

        /* =========================
           HEADER
        ========================= */

        .back {
            display: inline-flex;
            align-items: center;
            gap: 8px;

            color: #7daebf;
            text-decoration: none;

            font-size: 14px;

            margin-bottom: 22px;

            transition: .2s;
        }

        .back:hover {
            color: #42d5ed;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;

            gap: 25px;

            margin-bottom: 28px;
        }

        .title h1 {
            font-size: 34px;
            font-weight: 850;

            margin-bottom: 7px;
        }

        .title p {
            color: #769ead;
            font-size: 14px;
        }

        .branch {
            display: flex;
            align-items: center;
            gap: 10px;

            padding: 11px 16px;

            border-radius: 16px;

            background: rgba(9, 66, 86, .60);

            border: 1px solid rgba(55, 207, 233, .22);

            color: #43d4ec;

            font-size: 13px;
            font-weight: bold;
        }

        /* =========================
           BUTTONS
        ========================= */

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;

            min-height: 44px;

            padding: 0 17px;

            border-radius: 13px;

            text-decoration: none;

            font-size: 13px;
            font-weight: 700;

            transition: .2s;
        }

        .btn-primary {
            background: linear-gradient(
                135deg,
                #3dd8ed,
                #20b9d2
            );

            color: #032536;

            box-shadow:
                0 10px 25px rgba(35, 201, 229, .15);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: rgba(9, 57, 75, .65);
            border: 1px solid rgba(75, 191, 216, .18);
            color: #a9d0db;
        }

        .btn-secondary:hover {
            border-color: rgba(75, 211, 235, .45);
            color: white;
        }

        /* =========================
           STATISTICS
        ========================= */

        .stats {
            display: grid;

            grid-template-columns:
                repeat(4, 1fr);

            gap: 16px;

            margin-bottom: 22px;
        }

        .stat {
            position: relative;

            overflow: hidden;

            background:
                linear-gradient(
                    145deg,
                    rgba(9, 67, 87, .95),
                    rgba(4, 39, 54, .97)
                );

            border: 1px solid rgba(52, 191, 219, .16);

            border-radius: 20px;

            padding: 20px;

            min-height: 130px;
        }

        .stat::after {
            content: "";

            position: absolute;

            width: 110px;
            height: 110px;

            right: -55px;
            top: -55px;

            border-radius: 50%;

            background: rgba(46, 210, 237, .05);
        }

        .stat-label {
            color: #749cab;

            font-size: 11px;

            font-weight: 700;

            letter-spacing: .8px;

            margin-bottom: 12px;
        }

        .stat-value {
            font-size: 29px;

            font-weight: 900;

            margin-bottom: 6px;
        }

        .stat-small {
            color: #719aa9;

            font-size: 11px;
        }

        .green {
            color: #2ce0ae;
        }

        .yellow {
            color: #ffc32d;
        }

        .blue {
            color: #45d7ed;
        }

        .red {
            color: #ff7f8a;
        }

        /* =========================
           MAIN GRID
        ========================= */

        .main-grid {
            display: grid;

            grid-template-columns:
                1.55fr
                .85fr;

            gap: 20px;

            margin-bottom: 20px;
        }

        .card {
            background:
                linear-gradient(
                    145deg,
                    rgba(8, 61, 81, .96),
                    rgba(3, 38, 53, .98)
                );

            border: 1px solid rgba(55, 191, 218, .16);

            border-radius: 22px;

            padding: 22px;
        }

        .card-header {
            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 15px;

            margin-bottom: 20px;
        }

        .card-title {
            font-size: 18px;
            font-weight: 800;
        }

        .card-description {
            margin-top: 5px;

            color: #729bab;

            font-size: 12px;
        }

        /* =========================
           CHART
        ========================= */

        .chart {
            height: 250px;

            display: flex;

            align-items: flex-end;

            gap: 18px;

            padding:
                15px
                10px
                0;
        }

        .chart-column {
            height: 100%;

            flex: 1;

            display: flex;

            flex-direction: column;

            justify-content: flex-end;

            align-items: center;

            gap: 8px;
        }

        .chart-bar {
            width: 100%;

            max-width: 42px;

            border-radius:
                9px
                9px
                4px
                4px;

            background:
                linear-gradient(
                    to top,
                    #159bb9,
                    #3ed8ec
                );

            box-shadow:
                0 8px 20px rgba(31, 202, 229, .10);
        }

        .month {
            color: #688f9e;
            font-size: 10px;
        }

        /* =========================
           PAYMENT STATUS
        ========================= */

        .payment-status {
            display: flex;

            flex-direction: column;

            gap: 13px;
        }

        .status-row {
            display: flex;

            align-items: center;

            gap: 12px;

            padding: 13px;

            border-radius: 14px;

            background: rgba(0, 28, 42, .35);

            border: 1px solid rgba(255,255,255,.035);
        }

        .status-circle {
            width: 11px;
            height: 11px;

            border-radius: 50%;
        }

        .circle-green {
            background: #28dbae;
            box-shadow: 0 0 12px rgba(40,219,174,.35);
        }

        .circle-yellow {
            background: #ffc42d;
            box-shadow: 0 0 12px rgba(255,196,45,.30);
        }

        .circle-blue {
            background: #45d7ed;
            box-shadow: 0 0 12px rgba(69,215,237,.30);
        }

        .status-info {
            flex: 1;
        }

        .status-name {
            font-size: 13px;
            font-weight: 700;
        }

        .status-count {
            color: #759cab;
            font-size: 11px;
            margin-top: 3px;
        }

        .status-money {
            font-weight: 800;
            font-size: 13px;
        }

        /* =========================
           DEBTORS
        ========================= */

        .debtors {
            margin-top: 20px;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;

            border-collapse: collapse;

            min-width: 700px;
        }

        th {
            text-align: left;

            color: #668f9f;

            font-size: 10px;

            text-transform: uppercase;

            letter-spacing: .7px;

            padding:
                12px
                10px;

            border-bottom:
                1px solid
                rgba(255,255,255,.07);
        }

        td {
            padding:
                15px
                10px;

            border-bottom:
                1px solid
                rgba(255,255,255,.045);

            font-size: 13px;
        }

        .student {
            display: flex;

            align-items: center;

            gap: 11px;
        }

        .avatar {
            width: 38px;
            height: 38px;

            border-radius: 12px;

            background:
                linear-gradient(
                    135deg,
                    #d7edf3,
                    #85b8c7
                );

            color: #063044;

            display: flex;

            align-items: center;
            justify-content: center;

            font-size: 11px;
            font-weight: 900;
        }

        .student-name {
            font-weight: 700;
        }

        .student-detail {
            color: #668f9f;

            font-size: 10px;

            margin-top: 3px;
        }

        .amount {
            font-weight: 800;
            color: #ff8790;
        }

        .status-pill {
            display: inline-block;

            padding:
                6px
                10px;

            border-radius: 20px;

            font-size: 10px;

            font-weight: 800;
        }

        .status-overdue {
            color: #ff8790;
            background: rgba(255,96,110,.09);
            border: 1px solid rgba(255,96,110,.18);
        }

        /* =========================
           RESPONSIVE
        ========================= */

        @media(max-width: 1100px) {

            .stats {
                grid-template-columns:
                    repeat(2, 1fr);
            }

            .main-grid {
                grid-template-columns: 1fr;
            }

        }

        @media(max-width: 700px) {

            .page {
                padding: 20px;
            }

            .header {
                flex-direction: column;
                align-items: flex-start;
            }

            .stats {
                grid-template-columns: 1fr;
            }

            .title h1 {
                font-size: 28px;
            }

            .actions {
                width: 100%;
            }

            .btn {
                flex: 1;
            }

        }

    </style>

</head>

<body>

<div class="page">

    <!-- REGRESAR -->

    <a
        href="{{ route('admin.dashboard') }}"
        class="back"
    >
        ← Volver al dashboard
    </a>


    <!-- HEADER -->

    <div class="header">

        <div class="title">

            <h1>
                Pagos
            </h1>

            <p>
                Administración de mensualidades, inscripciones,
                conceptos adicionales y saldos.
            </p>

        </div>


        <div class="actions">

            <a
                href="{{ route('pagos.deudores') }}"
                class="btn btn-secondary"
            >
                Ver deudores
            </a>

            <a
                href="{{ route('pagos.registrar') }}"
                class="btn btn-primary"
            >
                + Registrar pago
            </a>

        </div>

    </div>


    <!-- SUCURSAL -->

    <div class="branch">

        ◉ QUANTIKA POOL · SUCURSAL 1

    </div>


    <br>


    <!-- ESTADÍSTICAS -->

    <div class="stats">


        <div class="stat">

            <div class="stat-label">
                COBRADO ESTE MES
            </div>

            <div class="stat-value green">
                $48,500
            </div>

            <div class="stat-small">
                +12.5% respecto al mes anterior
            </div>

        </div>


        <div class="stat">

            <div class="stat-label">
                PAGOS PENDIENTES
            </div>

            <div class="stat-value yellow">
                $8,200
            </div>

            <div class="stat-small">
                14 pagos pendientes
            </div>

        </div>


        <div class="stat">

            <div class="stat-label">
                EN REVISIÓN
            </div>

            <div class="stat-value blue">
                $5,400
            </div>

            <div class="stat-small">
                6 comprobantes
            </div>

        </div>


        <div class="stat">

            <div class="stat-label">
                DEUDORES
            </div>

            <div class="stat-value red">
                7
            </div>

            <div class="stat-small">
                Alumnos con saldo vencido
            </div>

        </div>


    </div>


    <!-- GRAFICA + ESTADOS -->

    <div class="main-grid">


        <!-- GRÁFICA -->

        <div class="card">

            <div class="card-header">

                <div>

                    <div class="card-title">
                        Ingresos del mes
                    </div>

                    <div class="card-description">
                        Comportamiento de los pagos registrados.
                    </div>

                </div>

                <strong class="green">
                    $48,500
                </strong>

            </div>


            <div class="chart">


                <div class="chart-column">

                    <div
                        class="chart-bar"
                        style="height:38%;"
                    ></div>

                    <span class="month">
                        ENE
                    </span>

                </div>


                <div class="chart-column">

                    <div
                        class="chart-bar"
                        style="height:52%;"
                    ></div>

                    <span class="month">
                        FEB
                    </span>

                </div>


                <div class="chart-column">

                    <div
                        class="chart-bar"
                        style="height:46%;"
                    ></div>

                    <span class="month">
                        MAR
                    </span>

                </div>


                <div class="chart-column">

                    <div
                        class="chart-bar"
                        style="height:65%;"
                    ></div>

                    <span class="month">
                        ABR
                    </span>

                </div>


                <div class="chart-column">

                    <div
                        class="chart-bar"
                        style="height:58%;"
                    ></div>

                    <span class="month">
                        MAY
                    </span>

                </div>


                <div class="chart-column">

                    <div
                        class="chart-bar"
                        style="height:76%;"
                    ></div>

                    <span class="month">
                        JUN
                    </span>

                </div>


                <div class="chart-column">

                    <div
                        class="chart-bar"
                        style="height:89%;"
                    ></div>

                    <span class="month">
                        JUL
                    </span>

                </div>


                <div class="chart-column">

                    <div
                        class="chart-bar"
                        style="height:100%;"
                    ></div>

                    <span class="month">
                        AGO
                    </span>

                </div>


            </div>

        </div>


        <!-- ESTADOS -->

        <div class="card">

            <div class="card-header">

                <div>

                    <div class="card-title">
                        Estado de pagos
                    </div>

                    <div class="card-description">
                        Situación actual.
                    </div>

                </div>

            </div>


            <div class="payment-status">


                <div class="status-row">

                    <div class="status-circle circle-green"></div>

                    <div class="status-info">

                        <div class="status-name">
                            Pagados
                        </div>

                        <div class="status-count">
                            86 pagos
                        </div>

                    </div>

                    <div class="status-money green">
                        $48,500
                    </div>

                </div>


                <div class="status-row">

                    <div class="status-circle circle-yellow"></div>

                    <div class="status-info">

                        <div class="status-name">
                            Pendientes
                        </div>

                        <div class="status-count">
                            14 pagos
                        </div>

                    </div>

                    <div class="status-money yellow">
                        $8,200
                    </div>

                </div>


                <div class="status-row">

                    <div class="status-circle circle-blue"></div>

                    <div class="status-info">

                        <div class="status-name">
                            En revisión
                        </div>

                        <div class="status-count">
                            6 pagos
                        </div>

                    </div>

                    <div class="status-money blue">
                        $5,400
                    </div>

                </div>


            </div>

        </div>

    </div>


    <!-- DEUDORES -->

    <div class="card debtors">

        <div class="card-header">

            <div>

                <div class="card-title">
                    Alumnos con saldo vencido
                </div>

                <div class="card-description">
                    Deudores de QUANTIKA POOL · Sucursal 1
                </div>

            </div>

            <a
                href="{{ route('pagos.deudores') }}"
                class="btn btn-secondary"
            >
                Ver todos
            </a>

        </div>


        <div class="table-container">

            <table>

                <thead>

                    <tr>

                        <th>
                            Alumno
                        </th>

                        <th>
                            Concepto
                        </th>

                        <th>
                            Vencimiento
                        </th>

                        <th>
                            Saldo
                        </th>

                        <th>
                            Estado
                        </th>

                        <th>
                            Acción
                        </th>

                    </tr>

                </thead>


                <tbody>


                    <tr>

                        <td>

                            <div class="student">

                                <div class="avatar">
                                    MG
                                </div>

                                <div>

                                    <div class="student-name">
                                        María González
                                    </div>

                                    <div class="student-detail">
                                        Nivel 03 · Delfín
                                    </div>

                                </div>

                            </div>

                        </td>

                        <td>
                            Mensualidad
                        </td>

                        <td>
                            10 Ago 2026
                        </td>

                        <td class="amount">
                            $1,200
                        </td>

                        <td>

                            <span class="status-pill status-overdue">
                                VENCIDO
                            </span>

                        </td>

                        <td>

                            <a
                                href="{{ route('pagos.alumno', 1) }}"
                                class="btn btn-secondary"
                            >
                                Ver
                            </a>

                        </td>

                    </tr>


                    <tr>

                        <td>

                            <div class="student">

                                <div class="avatar">
                                    CR
                                </div>

                                <div>

                                    <div class="student-name">
                                        Carlos Ramírez
                                    </div>

                                    <div class="student-detail">
                                        Nivel 02 · Pez
                                    </div>

                                </div>

                            </div>

                        </td>

                        <td>
                            Mensualidad
                        </td>

                        <td>
                            12 Ago 2026
                        </td>

                        <td class="amount">
                            $1,200
                        </td>

                        <td>

                            <span class="status-pill status-overdue">
                                VENCIDO
                            </span>

                        </td>

                        <td>

                            <a
                                href="{{ route('pagos.alumno', 2) }}"
                                class="btn btn-secondary"
                            >
                                Ver
                            </a>

                        </td>

                    </tr>


                </tbody>

            </table>

        </div>

    </div>

</div>

</body>
</html>