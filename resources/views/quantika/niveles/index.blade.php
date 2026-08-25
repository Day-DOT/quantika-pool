<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Niveles | QUANTIKA POOL</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background:
                radial-gradient(circle at 10% 10%, rgba(31, 197, 231, .08), transparent 30%),
                radial-gradient(circle at 90% 80%, rgba(20, 130, 180, .08), transparent 30%),
                #031e2d;
            color: #fff;
            min-height: 100vh;
        }

        .page {
            width: 100%;
            max-width: 1500px;
            margin: auto;
            padding: 35px;
        }

        /* ENCABEZADO */

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            gap: 20px;
        }

        .header-left h1 {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 8px;
        }

        .header-left p {
            color: #83aabd;
            font-size: 15px;
        }

        .back-btn {
            text-decoration: none;
            color: #fff;
            border: 1px solid rgba(54, 208, 238, .35);
            background: rgba(7, 52, 70, .75);
            padding: 12px 20px;
            border-radius: 12px;
            transition: .25s;
        }

        .back-btn:hover {
            background: #42d4ed;
            color: #03202e;
        }

        /* BARRA SUPERIOR */

        .top-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 20px;
            font-weight: 800;
        }

        .add-btn {
            border: none;
            cursor: pointer;
            padding: 13px 22px;
            border-radius: 12px;
            background: #42d4ed;
            color: #03202e;
            font-weight: 800;
            font-size: 14px;
            transition: .25s;
        }

        .add-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(66, 212, 237, .25);
        }

        /* GRID */

        .levels-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
        }

        /* CARD */

        .level-card {
            position: relative;
            min-height: 390px;
            overflow: hidden;

            background:
                linear-gradient(
                    145deg,
                    rgba(9, 62, 82, .96),
                    rgba(3, 37, 54, .98)
                );

            border: 1px solid rgba(56, 207, 237, .18);
            border-radius: 22px;

            transition:
                transform .25s ease,
                border-color .25s ease,
                box-shadow .25s ease;
        }

        .level-card:hover {
            transform: translateY(-5px);
            border-color: rgba(56, 207, 237, .55);
            box-shadow: 0 20px 45px rgba(0, 0, 0, .25);
        }

        /* IMAGEN */

        .animal-area {
            height: 190px;
            display: flex;
            justify-content: center;
            align-items: center;

            background:
                radial-gradient(
                    circle,
                    rgba(48, 205, 237, .14),
                    transparent 65%
                );

            position: relative;
        }

        .animal-area::after {
            content: "";
            position: absolute;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 1px solid rgba(63, 211, 239, .15);
        }

        .animal-image {
            position: relative;
            z-index: 2;

            width: 145px;
            height: 145px;

            object-fit: contain;

            filter:
                drop-shadow(0 10px 20px rgba(0,0,0,.35));

            transition: transform .3s ease;
        }

        .level-card:hover .animal-image {
            transform: scale(1.08);
        }

        /* CONTENIDO */

        .level-content {
            padding: 20px 22px 22px;
        }

        .level-number {
            display: inline-block;
            color: #3dd5ef;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 2px;
            margin-bottom: 8px;
        }

        .level-name {
            font-size: 23px;
            font-weight: 800;
            margin-bottom: 8px;
        }

        .level-description {
            color: #88aebe;
            font-size: 13px;
            line-height: 1.55;
            min-height: 62px;
        }

        /* FOOTER CARD */

        .level-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;

            margin-top: 18px;
            padding-top: 15px;

            border-top: 1px solid rgba(255,255,255,.07);
        }

        .level-tag {
            color: #43d6ee;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 1px;
        }

        .view-btn {
            text-decoration: none;
            color: #fff;
            font-size: 12px;
            font-weight: 700;

            padding: 8px 13px;

            border-radius: 9px;

            background: rgba(54, 208, 238, .08);
            border: 1px solid rgba(54, 208, 238, .18);

            transition: .25s;
        }

        .view-btn:hover {
            background: #42d4ed;
            color: #03202e;
        }

        /* RESPONSIVE */

        @media (max-width: 1200px) {
            .levels-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 900px) {
            .page {
                padding: 25px;
            }

            .levels-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 600px) {

            .page {
                padding: 18px;
            }

            .header {
                flex-direction: column;
                align-items: flex-start;
            }

            .top-actions {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .levels-grid {
                grid-template-columns: 1fr;
            }

            .level-card {
                min-height: auto;
            }
        }
    </style>
</head>

<body>

<div class="page">

    <!-- ENCABEZADO -->

    <div class="header">

        <div class="header-left">

            <h1>Niveles de aprendizaje</h1>

            <p>
                Consulta los niveles de QUANTIKA POOL y sus habilidades de aprendizaje.
            </p>

        </div>

        <a href="{{ route('admin.dashboard') }}" class="back-btn">
            ← Regresar al dashboard
        </a>

    </div>


    <!-- ACCIONES -->

    <div class="top-actions">

        <div class="section-title">
            Niveles de nataciónnn
        </div>

        <button class="add-btn">
            + Agregar nivel
        </button>

    </div>


    <!-- NIVELES -->

    <div class="levels-grid">

        @php
            $levels = [
                [
                    'number' => 1,
                    'name' => 'Estrella',
                    'image' => 'images/Niveles/estrella.png',
                    'description' => 'Se estimula su confianza a través de juegos y su adaptación al agua. Aprende ejercicios básicos como respiración y el uso correcto del material para la enseñanza de la natación.',
                    'tag' => 'INICIAL',
                ],
                [
                    'number' => 2,
                    'name' => 'Caballito de mar',
                    'image' => 'images/Niveles/caballito-mar.png',
                    'description' => 'Los ejercicios incrementan la confianza aun en áreas más profundas de la alberca. Aprende flotación y desplazamientos con apoyo de material.',
                    'tag' => 'INICIAL',
                ],
                [
                    'number' => 3,
                    'name' => 'Medusa',
                    'image' => 'images/Niveles/medusa.png',
                    'description' => 'Incrementa la confianza para realizar desplazamientos con seguridad sin ayuda de material, trabajando flotación y desplazamientos más ágiles.',
                    'tag' => 'DESARROLLO',
                ],
                [
                    'number' => 4,
                    'name' => 'Pulpo',
                    'image' => 'images/Niveles/pulpo.png',
                    'description' => 'Aprendizaje de movimientos básicos e intermedios para el desplazamiento en agua en estilo crol. También se trabajan clavados hincados y sentados.',
                    'tag' => 'DESARROLLO',
                ],
                [
                    'number' => 5,
                    'name' => 'Pez',
                    'image' => 'images/Niveles/pez.png',
                    'description' => 'Dominio del estilo de crol y aprendizaje de movimientos básicos e intermedios para mejorar la eficiencia en el desplazamiento en estilo de dorso.',
                    'tag' => 'DESARROLLO',
                ],
                [
                    'number' => 6,
                    'name' => 'Mantarraya',
                    'image' => 'images/Niveles/mantarraya.png',
                    'description' => 'Dominio del estilo de dorso e incremento de la eficiencia en la práctica de los niveles anteriores.',
                    'tag' => 'INTERMEDIO',
                ],
                [
                    'number' => 7,
                    'name' => 'Tortuga',
                    'image' => 'images/Niveles/tortuga.png',
                    'description' => 'Nivel enfocado en el aprendizaje de habilidades y técnicas para el dominio del estilo de pecho.',
                    'tag' => 'INTERMEDIO',
                ],
                [
                    'number' => 8,
                    'name' => 'Foca',
                    'image' => 'images/Niveles/foca.png',
                    'description' => 'Domina los estilos de crol, dorso y pecho, incrementando velocidad, resistencia y habilidades mediante diferentes ejercicios.',
                    'tag' => 'INTERMEDIO',
                ],
                [
                    'number' => 9,
                    'name' => 'Delfín',
                    'image' => 'images/Niveles/delfin.png',
                    'description' => 'Aprendizaje de movimientos básicos e intermedios para el desplazamiento en el agua mediante el estilo de mariposa.',
                    'tag' => 'AVANZADO',
                ],
                [
                    'number' => 10,
                    'name' => 'Orca',
                    'image' => 'images/Niveles/orca.png',
                    'description' => 'Dominio de los cuatro estilos de natación: crol, dorso, pecho y mariposa. Se inicia la enseñanza de salida competitiva, vueltas y llegadas reglamentarias.',
                    'tag' => 'AVANZADO',
                ],
                [
                    'number' => 11,
                    'name' => 'Ballena',
                    'image' => 'images/Niveles/ballena.png',
                    'description' => 'Se comprueba que en los niveles anteriores se haya adquirido una técnica adecuada mediante pruebas de diferentes distancias y estilos.',
                    'tag' => 'AVANZADO',
                ],
                [
                    'number' => 12,
                    'name' => 'Tiburón',
                    'image' => 'images/Niveles/tiburon.png',
                    'description' => 'En este nivel se logra un dominio total dentro del agua, adquiriendo un alto nivel de destreza para nadar.',
                    'tag' => 'DOMINIO',
                ],
            ];
        @endphp

        @foreach ($levels as $level)
            <x-level-card
                :number="$level['number']"
                :name="$level['name']"
                :image="$level['image']"
                :description="$level['description']"
                :tag="$level['tag']"
            />
        @endforeach







    </div>

</div>

</body>
</html>