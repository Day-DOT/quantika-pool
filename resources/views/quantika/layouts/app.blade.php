<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>QUANTIKA POOL</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@500;600;700;800;900&display=swap" rel="stylesheet">

    <style>

        :root {
            --bg: #031f2f;
            --bg-2: #05293c;
            --sidebar: #052b40;
            --card: #073349;
            --card-2: #062c41;

            --cyan: #42d8ef;
            --cyan-2: #22c8e8;

            --text: #f4fbff;
            --muted: #86aabd;
            --muted-2: #63879a;

            --border: rgba(69, 215, 239, .18);

            --green: #14e7ad;
            --yellow: #ffc229;
            --purple: #bb55ff;
            --blue: #16cfff;
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
                radial-gradient(
                    circle at 75% 10%,
                    rgba(20, 110, 145, .14),
                    transparent 35%
                ),
                var(--bg);

            color: var(--text);

            font-family: 'Inter', sans-serif;

            overflow-x: hidden;
        }

        button,
        input,
        select {
            font-family: inherit;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        /* =========================================
           LAYOUT GENERAL
        ========================================= */

        .quantika-app {
            min-height: 100vh;
            display: flex;
        }

        /* =========================================
           SIDEBAR
        ========================================= */

        .sidebar {
            width: 260px;
            min-width: 260px;
            height: 100vh;

            position: fixed;
            left: 0;
            top: 0;

            background:
                linear-gradient(
                    180deg,
                    #062e44 0%,
                    #04263a 55%,
                    #031f31 100%
                );

            border-right: 1px solid rgba(93, 217, 238, .13);

            display: flex;
            flex-direction: column;

            z-index: 100;
        }

        .sidebar-logo {
            height: 180px;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 25px;

            border-bottom: 1px solid rgba(255,255,255,.07);
        }

        .sidebar-logo img {
            width: 205px;
            max-width: 100%;
            height: auto;
            object-fit: contain;
        }

        /* usuario */

        .sidebar-user {
            margin: 28px 22px 20px;

            padding: 18px;

            border-radius: 22px;

            background:
                linear-gradient(
                    135deg,
                    rgba(20, 89, 116, .60),
                    rgba(4, 44, 65, .65)
                );

            border: 1px solid rgba(76, 213, 239, .17);

            display: flex;
            align-items: center;
            gap: 14px;
        }

        .avatar {
            width: 58px;
            height: 58px;

            border-radius: 16px;

            display: flex;
            align-items: center;
            justify-content: center;

            background: var(--cyan);

            color: #043047;

            font-size: 21px;
            font-weight: 900;

            flex-shrink: 0;

            box-shadow:
                0 8px 25px rgba(66, 216, 239, .18);
        }

        .sidebar-user strong {
            display: block;

            font-family: 'Outfit', sans-serif;

            font-size: 16px;
            font-weight: 800;
        }

        .sidebar-user span {
            display: block;

            color: var(--muted);

            font-size: 12px;

            margin-top: 4px;
        }

        /* navegación */

        .sidebar-scroll {
            flex: 1;

            overflow-y: auto;

            padding: 5px 15px 20px;

            scrollbar-width: thin;
            scrollbar-color: #176a83 transparent;
        }

        .sidebar-scroll::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: #176a83;
            border-radius: 20px;
        }

        .menu-title {
            padding: 14px 15px 10px;

            color: #6f96a9;

            font-size: 11px;

            letter-spacing: 3px;

            font-weight: 800;
        }

        .menu {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

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

            transition:
                .25s ease;

            position: relative;
        }

        .menu-item:hover {
            background: rgba(39, 124, 153, .17);
            color: #fff;
            transform: translateX(2px);
        }

        .menu-item.active {
            color: var(--cyan);

            background:
                linear-gradient(
                    90deg,
                    rgba(29, 137, 165, .32),
                    rgba(17, 78, 102, .40)
                );
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

            box-shadow:
                0 0 12px rgba(66, 216, 239, .6);
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

        .menu-item.active .menu-icon {
            background: var(--cyan);
            color: #053147;
        }

        .sidebar-footer {
            height: 54px;

            border-top: 1px solid rgba(255,255,255,.07);

            display: flex;
            align-items: center;
            justify-content: center;

            color: #50778b;

            font-size: 11px;
        }

        /* =========================================
           CONTENIDO
        ========================================= */

        .main {
            width: calc(100% - 260px);

            margin-left: 260px;

            min-height: 100vh;
        }

        /* =========================================
           TOPBAR
        ========================================= */

        .topbar {
            min-height: 102px;

            padding: 20px 34px;

            display: flex;
            align-items: center;
            justify-content: space-between;

            gap: 20px;

            border-bottom: 1px solid rgba(255,255,255,.07);

            background: rgba(3, 31, 47, .82);

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

        .page-title h1 {
            font-family: 'Outfit', sans-serif;

            font-size: 32px;

            line-height: 1;

            font-weight: 900;
        }

        .top-actions {
            display: flex;
            align-items: center;

            gap: 12px;
        }

        /* selector sucursal */

        .branch-select {
            height: 54px;

            min-width: 220px;

            padding: 0 17px;

            border-radius: 18px;

            border: 1px solid rgba(66,216,239,.20);

            background:
                linear-gradient(
                    135deg,
                    rgba(17, 82, 106, .72),
                    rgba(7, 47, 66, .90)
                );

            color: white;

            display: flex;
            align-items: center;

            justify-content: space-between;

            cursor: pointer;

            font-size: 14px;

            font-weight: 800;
        }

        .branch-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .branch-dot {
            width: 9px;
            height: 9px;

            border-radius: 50%;

            background: var(--cyan);

            box-shadow:
                0 0 12px var(--cyan);
        }

        .branch-arrow {
            color: #9db5c2;
            font-size: 15px;
        }

        .notification {
            width: 54px;
            height: 54px;

            border-radius: 18px;

            background: rgba(8, 57, 77, .78);

            border: 1px solid rgba(66,216,239,.16);

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 20px;

            cursor: pointer;
        }

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

        .top-user .avatar {
            width: 42px;
            height: 42px;

            border-radius: 13px;

            font-size: 14px;
        }

        /* =========================================
           CONTENIDO
        ========================================= */

        .content {
            padding: 34px;
            max-width: 1700px;
            margin: auto;
        }

        /* =========================================
           HERO
        ========================================= */

        .hero {
            position: relative;

            min-height: 420px;
            max-height: 500px;

            overflow: hidden;

            border-radius: 28px;

            border: 1px solid rgba(71, 208, 235, .20);

            background:
                linear-gradient(
                    90deg,
                    rgba(1, 25, 40, .91) 0%,
                    rgba(2, 45, 67, .72) 48%,
                    rgba(1, 40, 62, .55) 100%
                ),
                url('/images/quantika-pool-bg.jpg');

            background-size: cover;
            background-position: center;

            box-shadow:
                inset 0 0 80px rgba(0, 0, 0, .25),
                0 25px 70px rgba(0, 0, 0, .18);
        }

        .hero::after {
            content: "";

            position: absolute;

            inset: 0;

            background:
                radial-gradient(
                    circle at 85% 30%,
                    rgba(40, 204, 235, .13),
                    transparent 30%
                );

            pointer-events: none;
        }

        .hero-content {
            position: relative;

            z-index: 2;

            height: 100%;

            min-height: 420px;

            padding: 58px;

            display: flex;

            justify-content: space-between;

            gap: 35px;
        }

        .hero-left {
            max-width: 670px;

            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .status {
            width: fit-content;

            display: flex;
            align-items: center;

            gap: 9px;

            padding: 10px 17px;

            margin-bottom: 25px;

            border: 1px solid rgba(67, 218, 240, .45);

            border-radius: 30px;

            background: rgba(3, 45, 62, .45);

            color: #bcecf6;

            font-size: 11px;

            letter-spacing: 2px;

            font-weight: 900;
        }

        .status-dot {
            width: 9px;
            height: 9px;

            border-radius: 50%;

            background: var(--cyan);

            box-shadow: 0 0 12px var(--cyan);
        }

        .hero h2 {
            font-family: 'Outfit', sans-serif;

            font-size: clamp(42px, 4vw, 67px);

            line-height: .94;

            letter-spacing: -2px;

            font-weight: 900;

            max-width: 650px;
        }

        .hero h2 span {
            color: var(--cyan);
        }

        .hero-description {
            margin-top: 22px;

            color: #c6e1eb;

            font-size: 16px;

            line-height: 1.6;

            max-width: 580px;
        }

        .hero-buttons {
            display: flex;

            gap: 14px;

            margin-top: 28px;

            flex-wrap: wrap;
        }

        .btn {
            min-height: 50px;

            padding: 0 23px;

            border-radius: 15px;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            gap: 12px;

            font-size: 14px;

            font-weight: 900;

            transition: .25s ease;
        }

        .btn-primary {
            background: var(--cyan);

            color: #033047;

            box-shadow:
                0 12px 30px rgba(66,216,239,.18);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow:
                0 16px 35px rgba(66,216,239,.28);
        }

        .btn-outline {
            border: 1px solid rgba(66,216,239,.38);

            color: white;

            background: rgba(2, 34, 49, .42);
        }

        .btn-outline:hover {
            background: rgba(66,216,239,.10);
        }

        /* logo */

        .hero-logo {
            width: 280px;
            height: 280px;

            margin: auto 20px auto 0;

            border-radius: 30px;

            background:
                linear-gradient(
                    145deg,
                    rgba(3, 40, 56, .88),
                    rgba(2, 31, 46, .75)
                );

            border: 1px solid rgba(74, 216, 239, .35);

            box-shadow:
                0 20px 60px rgba(0,0,0,.28),
                inset 0 0 35px rgba(72,216,239,.05);

            display: flex;
            align-items: center;
            justify-content: center;

            position: relative;
        }

        .hero-logo::after {
            content: "";

            position: absolute;

            inset: -1px;

            border-radius: inherit;

            box-shadow:
                0 0 25px rgba(66,216,239,.12);

            pointer-events: none;
        }

        .hero-logo img {
            width: 72%;

            max-height: 72%;

            object-fit: contain;
        }

        /* estadísticas hero */

        .hero-stats {
            display: flex;

            gap: 0;

            margin-top: 30px;

            border-top: 1px solid rgba(255,255,255,.15);

            padding-top: 18px;
        }

        .hero-stat {
            min-width: 145px;

            padding-right: 28px;
            margin-right: 28px;

            border-right: 1px solid rgba(255,255,255,.13);
        }

        .hero-stat:last-child {
            border-right: none;
        }

        .hero-stat strong {
            display: block;

            font-family: 'Outfit', sans-serif;

            font-size: 28px;

            line-height: 1;
        }

        .hero-stat span {
            display: block;

            margin-top: 6px;

            color: #80a7b8;

            font-size: 9px;

            letter-spacing: 2px;

            font-weight: 900;
        }

        /* =========================================
           ESTADÍSTICAS
        ========================================= */

        .stats-grid {
            display: grid;

            grid-template-columns:
                repeat(4, minmax(0, 1fr));

            gap: 16px;

            margin-top: 22px;
        }

        .stat-card {
            min-height: 125px;

            padding: 20px;

            border-radius: 20px;

            background:
                linear-gradient(
                    145deg,
                    rgba(7, 54, 74, .95),
                    rgba(4, 37, 55, .95)
                );

            border: 1px solid rgba(65, 210, 237, .15);

            position: relative;

            overflow: hidden;

            transition: .25s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);

            border-color: rgba(65, 210, 237, .30);
        }

        .stat-card::after {
            content: "";

            width: 90px;
            height: 90px;

            border-radius: 50%;

            position: absolute;

            right: -35px;
            bottom: -45px;

            background: rgba(45, 207, 235, .07);
        }

        .stat-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .stat-name {
            color: #80a7b8;

            font-size: 11px;

            font-weight: 700;
        }

        .stat-icon {
            width: 40px;
            height: 40px;

            border-radius: 13px;

            display: flex;
            align-items: center;
            justify-content: center;

            background: rgba(66,216,239,.12);

            color: var(--cyan);
        }

        .stat-value {
            margin-top: 8px;

            font-family: 'Outfit', sans-serif;

            font-size: 29px;

            font-weight: 900;
        }

        .stat-change {
            color: var(--green);

            font-size: 10px;

            font-weight: 800;
        }

        /* =========================================
           TITULOS DE SECCIÓN
        ========================================= */

        .section-header {
            display: flex;

            justify-content: space-between;
            align-items: center;

            margin: 32px 0 14px;
        }

        .section-header h3 {
            font-family: 'Outfit', sans-serif;

            font-size: 20px;

            font-weight: 900;
        }

        .section-link {
            color: var(--cyan);

            font-size: 11px;

            font-weight: 800;
        }

        /* =========================================
           NIVELES
        ========================================= */

        .levels-grid {
            display: grid;

            grid-template-columns:
                repeat(4, minmax(0, 1fr));

            gap: 16px;
        }

        .level-card {
            min-height: 195px;

            padding: 20px;

            border-radius: 22px;

            background:
                linear-gradient(
                    145deg,
                    rgba(7, 51, 70, .98),
                    rgba(4, 37, 55, .98)
                );

            border: 1px solid rgba(65, 210, 237, .17);

            position: relative;

            overflow: hidden;

            transition:
                transform .25s ease,
                border .25s ease,
                box-shadow .25s ease;
        }

        .level-card:hover {
            transform: translateY(-4px);

            border-color: rgba(65, 210, 237, .35);

            box-shadow:
                0 20px 40px rgba(0,0,0,.16);
        }

        .level-card::before {
            content: "";

            position: absolute;

            width: 120px;
            height: 120px;

            border-radius: 50%;

            right: -45px;
            bottom: -55px;

            background: var(--level-color);

            opacity: .07;

            filter: blur(4px);
        }

        .level-top {
            display: flex;

            align-items: center;

            gap: 14px;
        }

        .animal {
            width: 58px;
            height: 58px;

            min-width: 58px;

            border-radius: 50%;

            display: flex;
            align-items: center;
            justify-content: center;

            border: 2px solid var(--level-color);

            background:
                radial-gradient(
                    circle,
                    rgba(255,255,255,.08),
                    rgba(1,32,47,.75)
                );

            box-shadow:
                0 0 22px color-mix(
                    in srgb,
                    var(--level-color),
                    transparent 65%
                );
        }

        .animal img {
            width: 38px;
            height: 38px;

            object-fit: contain;

            display: block;
        }

        .level-number {
            color: var(--level-color);

            font-size: 9px;

            letter-spacing: 2px;

            font-weight: 900;

            margin-bottom: 4px;
        }

        .level-name {
            font-family: 'Outfit', sans-serif;

            font-size: 20px;

            font-weight: 900;

            line-height: 1.1;
        }

        .level-description {
            margin-top: 3px;

            color: #779aaa;

            font-size: 11px;
        }

        .progress-row {
            display: flex;

            justify-content: space-between;

            margin-top: 23px;

            margin-bottom: 7px;

            font-size: 10px;

            font-weight: 800;
        }

        .progress-row span:last-child {
            color: var(--level-color);
        }

        .progress {
            width: 100%;
            height: 6px;

            background: #123c4e;

            border-radius: 20px;

            overflow: hidden;
        }

        .progress span {
            display: block;

            height: 100%;

            border-radius: inherit;

            background: var(--level-color);

            box-shadow:
                0 0 10px color-mix(
                    in srgb,
                    var(--level-color),
                    transparent 45%
                );
        }

        /* =========================================
           ALUMNOS
        ========================================= */

        .students-card {
            background:
                linear-gradient(
                    145deg,
                    rgba(7, 51, 70, .97),
                    rgba(4, 35, 53, .97)
                );

            border: 1px solid rgba(65, 210, 237, .15);

            border-radius: 22px;

            overflow: hidden;
        }

        .students-table {
            width: 100%;

            border-collapse: collapse;
        }

        .students-table th {
            padding: 15px 18px;

            text-align: left;

            color: #688b9d;

            font-size: 9px;

            letter-spacing: 1.5px;

            font-weight: 900;

            border-bottom: 1px solid rgba(255,255,255,.07);
        }

        .students-table td {
            padding: 14px 18px;

            border-bottom: 1px solid rgba(255,255,255,.05);

            font-size: 12px;
        }

        .students-table tr:last-child td {
            border-bottom: none;
        }

        .student-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .student-avatar {
            width: 36px;
            height: 36px;

            border-radius: 11px;

            display: flex;
            align-items: center;
            justify-content: center;

            background: #a9d1df;

            color: #173d4d;

            font-weight: 900;

            font-size: 10px;
        }

        .student-name {
            font-weight: 800;
        }

        .student-email {
            color: #64889b;

            font-size: 9px;

            margin-top: 3px;
        }

        .mini-level {
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .mini-animal {
            width: 30px;
            height: 30px;

            border-radius: 50%;

            background: rgba(66,216,239,.08);

            display: flex;
            align-items: center;
            justify-content: center;
        }

        .mini-animal img {
            width: 21px;
            height: 21px;

            object-fit: contain;
        }

        .badge {
            display: inline-flex;

            padding: 5px 10px;

            border-radius: 30px;

            color: #16e6aa;

            background: rgba(20,231,173,.08);

            border: 1px solid rgba(20,231,173,.25);

            font-size: 9px;

            font-weight: 800;
        }

        /* =========================================
           RESPONSIVE
        ========================================= */

        @media (max-width: 1250px) {

            .sidebar {
                width: 230px;
                min-width: 230px;
            }

            .main {
                width: calc(100% - 230px);
                margin-left: 230px;
            }

            .levels-grid {
                grid-template-columns:
                    repeat(2, minmax(0, 1fr));
            }

            .stats-grid {
                grid-template-columns:
                    repeat(2, minmax(0, 1fr));
            }

            .hero-logo {
                width: 230px;
                height: 230px;
            }

            .hero h2 {
                font-size: 52px;
            }
        }

        @media (max-width: 1000px) {

            .sidebar {
                width: 78px;
                min-width: 78px;
            }

            .main {
                width: calc(100% - 78px);
                margin-left: 78px;
            }

            .sidebar-logo {
                height: 105px;
                padding: 14px;
            }

            .sidebar-logo img {
                width: 60px;
            }

            .sidebar-user {
                margin: 15px 10px;
                padding: 10px;
                justify-content: center;
            }

            .sidebar-user .avatar {
                width: 46px;
                height: 46px;
            }

            .sidebar-user div:last-child {
                display: none;
            }

            .menu-title {
                display: none;
            }

            .menu-item {
                justify-content: center;
                padding: 7px;
            }

            .menu-item span:last-child {
                display: none;
            }

            .menu-icon {
                width: 44px;
                height: 44px;
            }

            .sidebar-footer {
                font-size: 0;
            }

            .sidebar-footer::after {
                content: "QP";
                font-size: 10px;
            }

            .content {
                padding: 24px;
            }

            .hero-content {
                padding: 40px;
            }

            .hero-logo {
                width: 200px;
                height: 200px;
            }
        }

        @media (max-width: 800px) {

            .topbar {
                padding: 16px 20px;
            }

            .page-title small {
                font-size: 9px;
                letter-spacing: 2px;
            }

            .page-title h1 {
                font-size: 25px;
            }

            .top-user {
                padding-right: 8px;
            }

            .top-user span {
                display: none;
            }

            .branch-select {
                min-width: 150px;
            }

            .hero {
                max-height: none;
            }

            .hero-content {
                min-height: auto;

                padding: 35px 30px;

                flex-direction: column;
            }

            .hero-left {
                max-width: none;
            }

            .hero h2 {
                font-size: clamp(38px, 9vw, 52px);
            }

            .hero-logo {
                width: 170px;
                height: 170px;

                margin: 10px auto 0;
            }

            .hero-stats {
                flex-wrap: wrap;
            }

            .hero-stat {
                min-width: 110px;
                margin-bottom: 10px;
            }

            .stats-grid {
                grid-template-columns:
                    repeat(2, minmax(0, 1fr));
            }

            .levels-grid {
                grid-template-columns:
                    repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 600px) {

            .sidebar {
                width: 64px;
                min-width: 64px;
            }

            .main {
                width: calc(100% - 64px);
                margin-left: 64px;
            }

            .content {
                padding: 15px;
            }

            .topbar {
                padding: 12px;
                gap: 7px;
            }

            .page-title small {
                display: none;
            }

            .page-title h1 {
                font-size: 20px;
            }

            .top-actions {
                gap: 5px;
            }

            .branch-select {
                min-width: 46px;
                width: 46px;

                padding: 0;

                justify-content: center;
            }

            .branch-left span {
                display: none;
            }

            .branch-arrow {
                display: none;
            }

            .notification {
                width: 46px;
                height: 46px;
            }

            .top-user {
                width: 46px;
                height: 46px;

                padding: 2px;

                justify-content: center;
            }

            .top-user .avatar {
                width: 40px;
                height: 40px;
            }

            .hero-content {
                padding: 28px 20px;
            }

            .hero h2 {
                font-size: 38px;
                letter-spacing: -1px;
            }

            .hero-description {
                font-size: 13px;
            }

            .hero-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }

            .hero-logo {
                width: 150px;
                height: 150px;
            }

            .hero-stats {
                display: grid;

                grid-template-columns:
                    repeat(3, 1fr);

                gap: 5px;
            }

            .hero-stat {
                min-width: 0;

                padding-right: 5px;

                margin-right: 5px;
            }

            .hero-stat strong {
                font-size: 21px;
            }

            .hero-stat span {
                font-size: 7px;
                letter-spacing: 1px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .levels-grid {
                grid-template-columns: 1fr;
            }

            .students-card {
                overflow-x: auto;
            }

            .students-table {
                min-width: 700px;
            }

            .section-header h3 {
                font-size: 18px;
            }
        }

    </style>
</head>

<body>

<div class="quantika-app">

    {{-- SIDEBAR --}}
    <aside class="sidebar">

        <div class="sidebar-logo">
            <img
                id="sidebarLogo"
                src="{{ asset('images/quantika-logo.png') }}"
                alt="Quantika Pool"
            >
        </div>

        <div class="sidebar-user">

            <div class="avatar">
                DC
            </div>

            <div>
                <strong>Administrador</strong>
                <span>Panel de control</span>
            </div>

        </div>

        <div class="sidebar-scroll">

            <div class="menu-title">
                PRINCIPAL
            </div>

            <nav class="menu">

                <a href="{{ url('/admin') }}"
                   class="menu-item active">

                    <div class="menu-icon">⌂</div>

                    <span>Dashboard</span>

                </a>

                <a href="{{ url('/alumnos') }}"
                   class="menu-item">

                    <div class="menu-icon">♙</div>

                    <span>Alumnos</span>

                </a>

                <a href="#niveles"
                   class="menu-item">

                    <div class="menu-icon">◉</div>

                    <span>Niveles</span>

                </a>

                <a href="#clases"
                   class="menu-item">

                    <div class="menu-icon">▣</div>

                    <span>Clases</span>

                </a>

                <a href="#evaluaciones"
                   class="menu-item">

                    <div class="menu-icon">✓</div>

                    <span>Evaluaciones</span>

                </a>

                <a href="#horarios"
                   class="menu-item">

                    <div class="menu-icon">▣</div>

                    <span>Horarios</span>

                </a>

            </nav>

            <div class="menu-title">
                ADMINISTRACIÓN
            </div>

            <nav class="menu">

                <a href="#" class="menu-item">
                    <div class="menu-icon">♙</div>
                    <span>Instructores</span>
                </a>

                <a href="#" class="menu-item">
                    <div class="menu-icon">$</div>
                    <span>Pagos</span>
                </a>

                <a href="#" class="menu-item">
                    <div class="menu-icon">▣</div>
                    <span>Reportes</span>
                </a>

            </nav>

            <div class="menu-title">
                SISTEMA
            </div>

            <nav class="menu">

                <a href="#" class="menu-item">
                    <div class="menu-icon">⌂</div>
                    <span>Sucursales</span>
                </a>

                <a href="#" class="menu-item">
                    <div class="menu-icon">▦</div>
                    <span>Carriles / Alberca</span>
                </a>

                <a href="#" class="menu-item">
                    <div class="menu-icon">⚙</div>
                    <span>Configuración</span>
                </a>

            </nav>

        </div>

        <div class="sidebar-footer">
            QUANTIKA POOL © 2026
        </div>

    </aside>


    {{-- CONTENIDO PRINCIPAL --}}
    <main class="main">

        {{-- TOPBAR --}}
        <header class="topbar">

            <div class="page-title">

                <small id="branchTitle">
                    QUANTIKA POOL · SUCURSAL 1
                </small>

                <h1>
                    Dashboard
                </h1>

            </div>


            <div class="top-actions">

                {{-- SUCURSAL --}}
                <button
                    type="button"
                    class="branch-select"
                    onclick="changeBranch()"
                    id="branchButton"
                >

                    <div class="branch-left">

                        <span class="branch-dot"></span>

                        <span id="branchName">
                            Sucursal 1
                        </span>

                    </div>

                    <span class="branch-arrow">
                        ▼
                    </span>

                </button>


                {{-- NOTIFICACIONES --}}
                <button class="notification">
                    🔔
                </button>


                {{-- USUARIO --}}
                <div class="top-user">

                    <div class="avatar">
                        DC
                    </div>

                    <span>
                        Administrador
                    </span>

                </div>

            </div>

        </header>


        <div class="content">

            {{-- HERO --}}
            <section class="hero">

                <div class="hero-content">

                    <div class="hero-left">

                        <div class="status">

                            <span class="status-dot"></span>

                            SISTEMA ACTIVO

                        </div>


                        <h2>
                            El progreso
                            <br>
                            comienza
                            <br>
                            <span>en el agua.</span>
                        </h2>


                        <p class="hero-description">

                            Administra alumnos, clases, evaluaciones
                            y niveles desde un solo lugar.

                        </p>


                        <div class="hero-buttons">

                            <a
                                href="{{ url('/alumnos/crear') }}"
                                class="btn btn-primary"
                            >
                                + Registrar alumno
                                <span>→</span>
                            </a>

                            <a
                                href="#niveles"
                                class="btn btn-outline"
                            >
                                Explorar niveles
                                <span>→</span>
                            </a>

                        </div>


                        <div class="hero-stats">

                            <div class="hero-stat">

                                
                            </div>

                            <div class="hero-stat">

                                

                           
                                <span>
                                    ASISTENCIA
                                </span>

                            </div>

                        </div>

                    </div>


                    {{-- LOGO SIN IMAGEN DE FONDO --}}
                    <div class="hero-logo">

                        <img
                            id="heroLogo"
                            src="{{ asset('images/quantika-logo.png') }}"
                            alt="Quantika Pool"
                        >

                    </div>

                </div>

            </section>


            {{-- ESTADÍSTICAS --}}
            <section class="stats-grid">

                <div class="stat-card">

                    <div class="stat-top">

                        <span class="stat-name">
                            Alumnos activos
                        </span>

                        <div class="stat-icon">
                            ♟
                        </div>

                    </div>

                    <div class="stat-value">
                        248
                    </div>

                    <div class="stat-change">
                        ↑ 12 este mes
                    </div>

                </div>


                <div class="stat-card">

                    <div class="stat-top">

                        <span class="stat-name">
                            Instructores
                        </span>

                        <div class="stat-icon">
                            ♙
                        </div>

                    </div>

                    <div class="stat-value">
                        18
                    </div>

                    <div class="stat-change">
                        16 disponibles
                    </div>

                </div>


                <div class="stat-card">

                    <div class="stat-top">

                        <span class="stat-name">
                            Clases de hoy
                        </span>

                        <div class="stat-icon">
                            ≋
                        </div>

                    </div>

                    <div class="stat-value">
                        24
                    </div>

                    <div class="stat-change">
                        6 en curso
                    </div>

                </div>


                <div class="stat-card">

                    <div class="stat-top">

                        <span class="stat-name">
                            Asistencia
                        </span>

                        <div class="stat-icon">
                            ✓
                        </div>

                    </div>

                    <div class="stat-value">
                        94.8%
                    </div>

                    <div class="stat-change">
                        Excelente
                    </div>

                </div>

            </section>


            {{-- =========================================
                 NIVELES DE APRENDIZAJE
            ========================================= --}}

            <section id="niveles">

                <div class="section-header">

                    <h3>
                        Niveles de aprendizaje
                    </h3>

                    <a href="#" class="section-link">
                        Ver todos los niveles →
                    </a>

                </div>


                <div class="levels-grid">

                    {{-- NIVEL 01 --}}
                    <div
                        class="level-card"
                        style="--level-color:#12e8ad"
                    >

                        <div class="level-top">

                            <div class="animal">

                                <img
                                    src="{{ asset('images/Niveles/tortuga.png') }}"
                                    alt="Tortuga"
                                >

                            </div>

                            <div>

                                <div class="level-number">
                                    NIVEL 01
                                </div>

                                <div class="level-name">
                                    Tortuga
                                </div>

                                <div class="level-description">
                                    Dominio de pecho
                                </div>

                            </div>

                        </div>


                        <div class="progress-row">

                            <span>
                                Progreso
                            </span>

                            <span>
                                60%
                            </span>

                        </div>

                        <div class="progress">
                            <span style="width:60%"></span>
                        </div>

                    </div>


                    {{-- NIVEL 02 --}}
                    <div
                        class="level-card"
                        style="--level-color:#ffc229"
                    >

                        <div class="level-top">

                            <div class="animal">

                                <img
                                    src="{{ asset('images/Niveles/pez.png') }}"
                                    alt="Pez"
                                >

                            </div>

                            <div>

                                <div class="level-number">
                                    NIVEL 02
                                </div>

                                <div class="level-name">
                                    Pez
                                </div>

                                <div class="level-description">
                                    Dominio de crol
                                </div>

                            </div>

                        </div>

                        <div class="progress-row">

                            <span>
                                Progreso
                            </span>

                            <span>
                                45%
                            </span>

                        </div>

                        <div class="progress">
                            <span style="width:45%"></span>
                        </div>

                    </div>


                    {{-- NIVEL 03 --}}
                    <div
                        class="level-card"
                        style="--level-color:#15ccff"
                    >

                        <div class="level-top">

                            <div class="animal">

                                <img
                                    src="{{ asset('images/Niveles/delfin.png') }}"
                                    alt="Delfín"
                                >

                            </div>

                            <div>

                                <div class="level-number">
                                    NIVEL 03
                                </div>

                                <div class="level-name">
                                    Delfín
                                </div>

                                <div class="level-description">
                                    Estilo mariposa
                                </div>

                            </div>

                        </div>

                        <div class="progress-row">

                            <span>
                                Progreso
                            </span>

                            <span>
                                70%
                            </span>

                        </div>

                        <div class="progress">
                            <span style="width:70%"></span>
                        </div>

                    </div>


                    {{-- NIVEL 04 --}}
                    <div
                        class="level-card"
                        style="--level-color:#bd51ff"
                    >

                        <div class="level-top">

                            <div class="animal">

                                <img
                                    src="{{ asset('images/Niveles/tiburon.png') }}"
                                    alt="Tiburón"
                                >

                            </div>

                            <div>

                                <div class="level-number">
                                    NIVEL 04
                                </div>

                                <div class="level-name">
                                    Tiburón
                                </div>

                                <div class="level-description">
                                    Dominio avanzado
                                </div>

                            </div>

                        </div>

                        <div class="progress-row">

                            <span>
                                Progreso
                            </span>

                            <span>
                                30%
                            </span>

                        </div>

                        <div class="progress">
                            <span style="width:30%"></span>
                        </div>

                    </div>


                    {{-- NIVEL 05 --}}
                    <div
                        class="level-card"
                        style="--level-color:#ff6b6b"
                    >

                        <div class="level-top">

                            <div class="animal">

                                <img
                                    src="{{ asset('images/Niveles/ballena.png') }}"
                                    alt="Ballena"
                                >

                            </div>

                            <div>

                                <div class="level-number">
                                    NIVEL 05
                                </div>

                                <div class="level-name">
                                    Ballena
                                </div>

                                <div class="level-description">
                                    Desarrollo acuático
                                </div>

                            </div>

                        </div>

                        <div class="progress-row">

                            <span>Progreso</span>
                            <span>0%</span>

                        </div>

                        <div class="progress">
                            <span style="width:0%"></span>
                        </div>

                    </div>


                    {{-- NIVEL 06 --}}
                    <div
                        class="level-card"
                        style="--level-color:#ff8c42"
                    >

                        <div class="level-top">

                            <div class="animal">

                                <img
                                    src="{{ asset('images/Niveles/caballito-mar.png') }}"
                                    alt="Caballito de mar"
                                >

                            </div>

                            <div>

                                <div class="level-number">
                                    NIVEL 06
                                </div>

                                <div class="level-name">
                                    Caballito
                                </div>

                                <div class="level-description">
                                    Técnica acuática
                                </div>

                            </div>

                        </div>

                        <div class="progress-row">
                            <span>Progreso</span>
                            <span>0%</span>
                        </div>

                        <div class="progress">
                            <span style="width:0%"></span>
                        </div>

                    </div>


                    {{-- NIVEL 07 --}}
                    <div
                        class="level-card"
                        style="--level-color:#ef58c8"
                    >

                        <div class="level-top">

                            <div class="animal">

                                <img
                                    src="{{ asset('images/Niveles/estrella.png') }}"
                                    alt="Estrella de mar"
                                >

                            </div>

                            <div>

                                <div class="level-number">
                                    NIVEL 07
                                </div>

                                <div class="level-name">
                                    Estrella
                                </div>

                                <div class="level-description">
                                    Control acuático
                                </div>

                            </div>

                        </div>

                        <div class="progress-row">
                            <span>Progreso</span>
                            <span>0%</span>
                        </div>

                        <div class="progress">
                            <span style="width:0%"></span>
                        </div>

                    </div>


                    {{-- NIVEL 08 --}}
                    <div
                        class="level-card"
                        style="--level-color:#55e6ff"
                    >

                        <div class="level-top">

                            <div class="animal">

                                <img
                                    src="{{ asset('images/Niveles/foca.png') }}"
                                    alt="Foca"
                                >

                            </div>

                            <div>

                                <div class="level-number">
                                    NIVEL 08
                                </div>

                                <div class="level-name">
                                    Foca
                                </div>

                                <div class="level-description">
                                    Perfeccionamiento
                                </div>

                            </div>

                        </div>

                        <div class="progress-row">
                            <span>Progreso</span>
                            <span>0%</span>
                        </div>

                        <div class="progress">
                            <span style="width:0%"></span>
                        </div>

                    </div>


                    {{-- NIVEL 09 --}}
                    <div
                        class="level-card"
                        style="--level-color:#37d4a0"
                    >

                        <div class="level-top">

                            <div class="animal">

                                <img
                                    src="{{ asset('images/Niveles/mantarraya.png') }}"
                                    alt="Mantarraya"
                                >

                            </div>

                            <div>

                                <div class="level-number">
                                    NIVEL 09
                                </div>

                                <div class="level-name">
                                    Mantarraya
                                </div>

                                <div class="level-description">
                                    Técnica avanzada
                                </div>

                            </div>

                        </div>

                        <div class="progress-row">
                            <span>Progreso</span>
                            <span>0%</span>
                        </div>

                        <div class="progress">
                            <span style="width:0%"></span>
                        </div>

                    </div>


                    {{-- NIVEL 10 --}}
                    <div
                        class="level-card"
                        style="--level-color:#ff68ad"
                    >

                        <div class="level-top">

                            <div class="animal">

                                <img
                                    src="{{ asset('images/Niveles/medusa.png') }}"
                                    alt="Medusa"
                                >

                            </div>

                            <div>

                                <div class="level-number">
                                    NIVEL 10
                                </div>

                                <div class="level-name">
                                    Medusa
                                </div>

                                <div class="level-description">
                                    Dominio técnico
                                </div>

                            </div>

                        </div>

                        <div class="progress-row">
                            <span>Progreso</span>
                            <span>0%</span>
                        </div>

                        <div class="progress">
                            <span style="width:0%"></span>
                        </div>

                    </div>


                    {{-- NIVEL 11 --}}
                    <div
                        class="level-card"
                        style="--level-color:#8b78ff"
                    >

                        <div class="level-top">

                            <div class="animal">

                                <img
                                    src="{{ asset('images/Niveles/orca.png') }}"
                                    alt="Orca"
                                >

                            </div>

                            <div>

                                <div class="level-number">
                                    NIVEL 11
                                </div>

                                <div class="level-name">
                                    Orca
                                </div>

                                <div class="level-description">
                                    Alto rendimiento
                                </div>

                            </div>

                        </div>

                        <div class="progress-row">
                            <span>Progreso</span>
                            <span>0%</span>
                        </div>

                        <div class="progress">
                            <span style="width:0%"></span>
                        </div>

                    </div>


                    {{-- NIVEL 12 --}}
                    <div
                        class="level-card"
                        style="--level-color:#00cfff"
                    >

                        <div class="level-top">

                            <div class="animal">

                                <img
                                    src="{{ asset('images/Niveles/pulpo.png') }}"
                                    alt="Pulpo"
                                >

                            </div>

                            <div>

                                
                                <div class="level-name">
                                    Pulpo
                                </div>

                                <div class="level-description">
                                    Dominio avanzado
                                </div>

                            </div>

                        </div>

                        <div class="progress-row">
                            <span>Progreso</span>
                            <span>0%</span>
                        </div>

                        <div class="progress">
                            <span style="width:0%"></span>
                        </div>

                    </div>

                </div>

            </section>


            {{-- =========================================
                 ALUMNOS RECIENTES
            ========================================= --}}

            <section>

                <div class="section-header">

                    <h3>
                        Alumnos recientes
                    </h3>

                    <a
                        href="{{ url('/alumnos') }}"
                        class="section-link"
                    >
                        Ver todos los alumnos →
                    </a>

                </div>


                <div class="students-card">

                    <table class="students-table">

                        <thead>

                            <tr>

                                <th>
                                    ALUMNO
                                </th>

                                <th>
                                    NIVEL
                                </th>

                                <th>
                                    SUCURSAL
                                </th>

                                <th>
                                    ASISTENCIA
                                </th>

                                <th>
                                    ESTADO
                                </th>

                                <th>
                                    ACCIONES
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            <tr>

                                <td>

                                    <div class="student-info">

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

                                    <div class="mini-level">

                                        <div class="mini-animal">

                                            <img
                                                src="{{ asset('images/Niveles/delfin.png') }}"
                                                alt=""
                                            >

                                        </div>

                                        <span>
                                            Delfín
                                        </span>

                                    </div>

                                </td>


                                <td>
                                    Quantika
                                </td>


                                <td>
                                    95%
                                </td>


                                <td>

                                    <span class="badge">
                                        ● Activo
                                    </span>

                                </td>


                                <td>
                                    👁　✎　•••
                                </td>

                            </tr>


                            <tr>

                                <td>

                                    <div class="student-info">

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

                                    <div class="mini-level">

                                        <div class="mini-animal">

                                            <img
                                                src="{{ asset('images/Niveles/pez.png') }}"
                                                alt=""
                                            >

                                        </div>

                                        <span>
                                            Pez
                                        </span>

                                    </div>

                                </td>


                                <td>
                                    Quantika
                                </td>


                                <td>
                                    88%
                                </td>


                                <td>

                                    <span class="badge">
                                        ● Activo
                                    </span>

                                </td>


                                <td>
                                    👁　✎　•••
                                </td>

                            </tr>


                            <tr>

                                <td>

                                    <div class="student-info">

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

                                    <div class="mini-level">

                                        <div class="mini-animal">

                                            <img
                                                src="{{ asset('images/Niveles/tortuga.png') }}"
                                                alt=""
                                            >

                                        </div>

                                        <span>
                                            Tortuga
                                        </span>

                                    </div>

                                </td>


                                <td>
                                    Sucursal 1
                                </td>


                                <td>
                                    76%
                                </td>


                                <td>

                                    <span class="badge">
                                        ● Activo
                                    </span>

                                </td>


                                <td>
                                    👁　✎　•••
                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </section>

        </div>

    </main>

</div>


<script>

    /*
    |--------------------------------------------------------------------------
    | CAMBIO DE SUCURSAL
    |--------------------------------------------------------------------------
    |
    | Sucursal 1 = QUANTIKA POOL
    | Sucursal 2 = QUANTIKA
    |
    */

    let currentBranch = 1;

    function changeBranch() {

        currentBranch = currentBranch === 1 ? 2 : 1;

        const branchName =
            document.getElementById('branchName');

        const branchTitle =
            document.getElementById('branchTitle');

        const heroLogo =
            document.getElementById('heroLogo');

        const sidebarLogo =
            document.getElementById('sidebarLogo');


        if (currentBranch === 1) {

            branchName.textContent =
                'Sucursal 1';

            branchTitle.textContent =
                'QUANTIKA POOL · SUCURSAL 1';

            heroLogo.src =
                "{{ asset('images/quantika-logo.png') }}";

            sidebarLogo.src =
                "{{ asset('images/quantika-logo.png') }}";

        } else {

            branchName.textContent =
                'Quantika';

            branchTitle.textContent =
                'QUANTIKA POOL · QUANTIKA';

            heroLogo.src =
                "{{ asset('images/logo-sucursal-2.png') }}";

            sidebarLogo.src =
                "{{ asset('images/logo-sucursal-2.png') }}";

        }

    }

</script>

</body>
</html>