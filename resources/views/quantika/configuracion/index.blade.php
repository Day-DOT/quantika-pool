<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Configuración | Quantika Pool</title>

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
                    circle at top right,
                    rgba(31, 190, 225, .08),
                    transparent 35%
                ),
                #031f2d;
            color: #ffffff;
            min-height: 100vh;
        }

        .page {
            min-height: 100vh;
            padding: 35px;
        }

        /* =========================
           ENCABEZADO
        ========================= */

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 35px;
            gap: 20px;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .back-button {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            border: 1px solid rgba(64, 207, 235, .25);
            background: rgba(7, 53, 72, .75);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 23px;
            transition: .25s;
        }

        .back-button:hover {
            background: #40d0eb;
            color: #032331;
            transform: translateX(-2px);
        }

        .eyebrow {
            color: #42d5ef;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 7px;
        }

        h1 {
            font-size: 34px;
            font-weight: 800;
            letter-spacing: -1px;
        }

        .description {
            margin-top: 8px;
            color: #7fa9ba;
            font-size: 15px;
        }

        /* =========================
           GRID
        ========================= */

        .settings-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
            max-width: 1100px;
        }

        /* =========================
           TARJETAS
        ========================= */

        .setting-card {
            position: relative;
            overflow: hidden;

            min-height: 190px;

            padding: 27px;

            border-radius: 24px;

            background:
                linear-gradient(
                    145deg,
                    rgba(8, 60, 80, .96),
                    rgba(4, 38, 55, .96)
                );

            border: 1px solid rgba(64, 207, 235, .18);

            box-shadow:
                0 18px 40px rgba(0, 0, 0, .15);

            transition:
                transform .25s ease,
                border-color .25s ease,
                box-shadow .25s ease;
        }

        .setting-card::after {
            content: "";
            position: absolute;

            width: 130px;
            height: 130px;

            right: -45px;
            bottom: -55px;

            border-radius: 50%;

            background: rgba(64, 207, 235, .07);
        }

        .setting-card:hover {
            transform: translateY(-4px);

            border-color: rgba(64, 207, 235, .45);

            box-shadow:
                0 22px 50px rgba(0, 0, 0, .25);
        }

        /* =========================
           ICONO
        ========================= */

        .setting-icon {
            width: 55px;
            height: 55px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 17px;

            background: rgba(64, 207, 235, .12);

            border: 1px solid rgba(64, 207, 235, .18);

            font-size: 25px;

            margin-bottom: 22px;
        }

        .setting-card h2 {
            font-size: 20px;
            margin-bottom: 8px;
        }

        .setting-card p {
            color: #82aabb;
            font-size: 14px;
            line-height: 1.6;
            max-width: 420px;
        }

        /* =========================
           BOTON
        ========================= */

        .setting-button {
            display: inline-flex;
            align-items: center;
            gap: 10px;

            margin-top: 20px;

            padding: 10px 17px;

            border-radius: 12px;

            background: rgba(64, 207, 235, .10);

            border: 1px solid rgba(64, 207, 235, .25);

            color: #43d5ef;

            font-size: 13px;
            font-weight: 700;

            text-decoration: none;

            transition: .2s;
        }

        .setting-button:hover {
            background: #40d0eb;
            color: #032331;
        }

        /* =========================
           PIE
        ========================= */

        .footer {
            margin-top: 40px;

            padding-top: 20px;

            border-top: 1px solid rgba(255,255,255,.06);

            color: #527c8d;

            font-size: 12px;
        }

        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 850px) {

            .page {
                padding: 25px;
            }

            .settings-grid {
                grid-template-columns: 1fr;
            }

            h1 {
                font-size: 30px;
            }
        }

        @media (max-width: 550px) {

            .page {
                padding: 18px;
            }

            .header {
                align-items: flex-start;
            }

            .header-left {
                gap: 12px;
            }

            .back-button {
                width: 42px;
                height: 42px;
            }

            h1 {
                font-size: 26px;
            }

            .description {
                font-size: 14px;
            }

            .setting-card {
                min-height: auto;
                padding: 22px;
            }
        }
    </style>
</head>

<body>

<div class="page">

    <!-- ENCABEZADO -->

    <div class="header">

        <div class="header-left">

            <a href="{{ route('admin.dashboard') }}"
               class="back-button"
               title="Regresar al dashboard">
                ←
            </a>

            <div>
                <div class="eyebrow">
                    QUANTIKA POOL · CONFIGURACIÓN
                </div>

                <h1>Configuración</h1>

                <p class="description">
                    Administra las opciones generales del sistema.
                </p>
            </div>

        </div>

    </div>


    <!-- OPCIONES -->

    <div class="settings-grid">

        <!-- SUCURSALES -->

        <div class="setting-card">

            <div class="setting-icon">
                🏢
            </div>

            <h2>Sucursales</h2>

            <p>
                Administra las sucursales de Quantika Pool,
                sus datos y disponibilidad.
            </p>

            <a href="#" class="setting-button">
                Administrar
                <span>→</span>
            </a>

        </div>


        <!-- USUARIOS -->

        <div class="setting-card">

            <div class="setting-icon">
                👤
            </div>

            <h2>Usuarios</h2>

            <p>
                Administra usuarios, roles, accesos y
                sucursal asignada.
            </p>

            <a href="#" class="setting-button">
                Administrar
                <span>→</span>
            </a>

        </div>


        <!-- ALBERCA -->

        <div class="setting-card">

            <div class="setting-icon">
                🏊
            </div>

            <h2>Alberca</h2>

            <p>
                Configura los carriles, capacidad máxima
                y disponibilidad de la alberca.
            </p>

            <a href="#" class="setting-button">
                Administrar
                <span>→</span>
            </a>

        </div>


        <!-- SEGURIDAD -->

        <div class="setting-card">

            <div class="setting-icon">
                🔐
            </div>

            <h2>Seguridad</h2>

            <p>
                Administra la contraseña y las opciones
                relacionadas con el acceso al sistema.
            </p>

            <a href="#" class="setting-button">
                Administrar
                <span>→</span>
            </a>

        </div>

    </div>


    <!-- FOOTER -->

    <div class="footer">
        QUANTIKA POOL © 2026 · Sistema de administración
    </div>

</div>

</body>
</html>