<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Evaluaciones | Quantika Pool</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background:
                radial-gradient(circle at top right, rgba(40,190,230,.08), transparent 35%),
                #031d2b;
            color: #fff;
            min-height: 100vh;
        }

        .page {
            padding: 35px;
            max-width: 1500px;
            margin: auto;
        }

        .top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 35px;
        }

        .back {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: #83b7ca;
            text-decoration: none;
            margin-bottom: 18px;
            transition: .2s;
        }

        .back:hover {
            color: #42d4ee;
        }

        .title-area h1 {
            font-size: 36px;
            font-weight: 800;
            margin-bottom: 8px;
        }

        .title-area p {
            color: #79aabd;
            font-size: 16px;
        }

        .badge {
            padding: 10px 18px;
            border-radius: 30px;
            border: 1px solid rgba(55,210,238,.35);
            background: rgba(13,66,88,.55);
            color: #42d4ee;
            font-size: 13px;
            font-weight: bold;
        }

        .section-title {
            margin-bottom: 20px;
            font-size: 21px;
        }

        .instructors {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 22px;
        }

        .instructor-card {
            background:
                linear-gradient(
                    145deg,
                    rgba(10,65,86,.95),
                    rgba(4,38,54,.96)
                );

            border: 1px solid rgba(55,190,220,.18);
            border-radius: 22px;
            padding: 24px;
            text-decoration: none;
            color: white;
            transition: .25s;
            position: relative;
            overflow: hidden;
        }

        .instructor-card::after {
            content: "";
            position: absolute;
            width: 130px;
            height: 130px;
            right: -55px;
            bottom: -65px;
            border-radius: 50%;
            background: rgba(40,210,235,.08);
        }

        .instructor-card:hover {
            transform: translateY(-5px);
            border-color: rgba(57,214,239,.65);
            box-shadow: 0 15px 40px rgba(0,0,0,.25);
        }

        .instructor-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 25px;
        }

        .avatar {
            width: 60px;
            height: 60px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #40d5ed, #21aeca);
            color: #043043;
            font-size: 20px;
            font-weight: 900;
        }

        .instructor-name {
            font-size: 19px;
            font-weight: 800;
        }

        .instructor-info {
            color: #79aabd;
            margin-top: 5px;
            font-size: 14px;
        }

        .stats {
            display: flex;
            gap: 12px;
        }

        .stat {
            flex: 1;
            background: rgba(0,25,38,.35);
            border: 1px solid rgba(255,255,255,.06);
            border-radius: 14px;
            padding: 13px;
        }

        .stat-number {
            font-size: 22px;
            font-weight: 800;
        }

        .stat-label {
            font-size: 11px;
            color: #6e9daf;
            margin-top: 4px;
        }

        .view {
            margin-top: 20px;
            color: #42d4ee;
            font-weight: bold;
            font-size: 13px;
        }

        @media(max-width: 1000px) {
            .instructors {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media(max-width: 650px) {
            .page {
                padding: 20px;
            }

            .top {
                align-items: flex-start;
                gap: 15px;
                flex-direction: column;
            }

            .instructors {
                grid-template-columns: 1fr;
            }

            .title-area h1 {
                font-size: 28px;
            }
        }
    </style>
</head>

<body>

<div class="page">

    <a href="{{ route('admin.dashboard') }}" class="back">
        ← Volver al dashboard
    </a>

    <div class="top">

        <div class="title-area">
            <h1>Evaluaciones</h1>

            <p>
                Consulta el progreso de los alumnos evaluados por cada instructor.
            </p>
        </div>

        <div class="badge">
            QUANTIKA POOL · SUCURSAL 1
        </div>

    </div>

    <h2 class="section-title">
        Instructores
    </h2>

    <div class="instructors">

        <!-- INSTRUCTOR 1 -->
        <a href="{{ route('evaluaciones.instructor', 1) }}"
           class="instructor-card">

            <div class="instructor-header">

                <div class="avatar">
                    AM
                </div>

                <div>
                    <div class="instructor-name">
                        Ana Martínez
                    </div>

                    <div class="instructor-info">
                        Instructor · Sucursal 1
                    </div>
                </div>

            </div>

            <div class="stats">

                <div class="stat">
                    <div class="stat-number">12</div>
                    <div class="stat-label">
                        ALUMNOS
                    </div>
                </div>

                <div class="stat">
                    <div class="stat-number">8</div>
                    <div class="stat-label">
                        EVALUADOS
                    </div>
                </div>

            </div>

            <div class="view">
                Ver alumnos →
            </div>

        </a>


        <!-- INSTRUCTOR 2 -->
        <a href="{{ route('evaluaciones.instructor', 2) }}"
           class="instructor-card">

            <div class="instructor-header">

                <div class="avatar">
                    CR
                </div>

                <div>
                    <div class="instructor-name">
                        Carlos Ramírez
                    </div>

                    <div class="instructor-info">
                        Instructor · Sucursal 1
                    </div>
                </div>

            </div>

            <div class="stats">

                <div class="stat">
                    <div class="stat-number">15</div>
                    <div class="stat-label">
                        ALUMNOS
                    </div>
                </div>

                <div class="stat">
                    <div class="stat-number">11</div>
                    <div class="stat-label">
                        EVALUADOS
                    </div>
                </div>

            </div>

            <div class="view">
                Ver alumnos →
            </div>

        </a>


        <!-- INSTRUCTOR 3 -->
        <a href="{{ route('evaluaciones.instructor', 3) }}"
           class="instructor-card">

            <div class="instructor-header">

                <div class="avatar">
                    LS
                </div>

                <div>
                    <div class="instructor-name">
                        Laura Sánchez
                    </div>

                    <div class="instructor-info">
                        Instructor · Sucursal 2
                    </div>
                </div>

            </div>

            <div class="stats">

                <div class="stat">
                    <div class="stat-number">10</div>
                    <div class="stat-label">
                        ALUMNOS
                    </div>
                </div>

                <div class="stat">
                    <div class="stat-number">7</div>
                    <div class="stat-label">
                        EVALUADOS
                    </div>
                </div>

            </div>

            <div class="view">
                Ver alumnos →
            </div>

        </a>

    </div>

</div>

</body>
</html>