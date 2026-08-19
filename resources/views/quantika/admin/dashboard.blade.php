<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>QUANTIKA POOL · Sucursal 1</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

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
            --green: #16e0a4;
            --yellow: #ffbd20;
            --blue: #21c7ff;
            --purple: #c05cff;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            min-height: 100vh;
            background:
                radial-gradient(
                    circle at 80% 10%,
                    rgba(16, 113, 145, .15),
                    transparent 35%
                ),
                var(--bg);
            color: var(--text);
            font-family: Arial, Helvetica, sans-serif;
            overflow-x: hidden;
        }

        button,
        a {
            font-family: inherit;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        /* =========================================================
           ESTRUCTURA
        ========================================================= */

        .app {
            min-height: 100vh;
            display: flex;
        }

        /* =========================================================
           SIDEBAR
        ========================================================= */

        .sidebar {
            width: 255px;
            min-width: 255px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 100;
            display: flex;
            flex-direction: column;
            background:
                linear-gradient(
                    180deg,
                    #052b3e 0%,
                    #032638 55%,
                    #021d2c 100%
                );
            border-right: 1px solid rgba(67, 206, 235, .14);
        }

        .logo-area {
            height: 175px;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 25px;
            border-bottom: 1px solid rgba(255,255,255,.07);
        }

        .logo-area img {
            width: 190px;
            max-width: 100%;
            height: auto;
            object-fit: contain;
        }

        .sidebar-content {
            flex: 1;
            overflow-y: auto;
            padding: 22px 16px 25px;
        }

        .sidebar-content::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar-content::-webkit-scrollbar-thumb {
            background: rgba(66, 213, 238, .35);
            border-radius: 20px;
        }

        .menu-title {
            margin: 5px 10px 12px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 2px;
            color: #6d99ac;
            text-transform: uppercase;
        }

        .menu {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        .menu-item {
            min-height: 48px;
            display: flex;
            align-items: center;
            gap: 13px;
            padding: 8px 13px;
            border-radius: 15px;
            color: #86a7b6;
            font-size: 14px;
            font-weight: 700;
            transition: .2s ease;
            position: relative;
        }

        .menu-item:hover {
            background: rgba(66,213,238,.08);
            color: white;
        }

        .menu-item.active {
            color: var(--cyan);
            background: rgba(17, 119, 150, .30);
        }

        .menu-item.active::before {
            content: "";
            position: absolute;
            left: 0;
            top: 9px;
            bottom: 9px;
            width: 4px;
            border-radius: 0 5px 5px 0;
            background: var(--cyan);
            box-shadow: 0 0 12px rgba(66,213,238,.8);
        }

        .menu-icon {
            width: 35px;
            height: 35px;
            min-width: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 11px;
            background: rgba(25, 96, 120, .32);
            color: #9ac0ce;
            font-size: 16px;
        }

        .menu-item.active .menu-icon {
            background: var(--cyan);
            color: #053248;
        }

        .sidebar-footer {
            height: 54px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-top: 1px solid rgba(255,255,255,.07);
            color: #577e90;
            font-size: 11px;
        }

        /* =========================================================
           CONTENIDO
        ========================================================= */

        .main {
            width: calc(100% - 255px);
            margin-left: 255px;
            min-height: 100vh;
        }

        /* =========================================================
           TOPBAR
        ========================================================= */

        .topbar {
            height: 92px;
            padding: 0 34px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255,255,255,.07);
            background: rgba(2, 28, 42, .88);
            backdrop-filter: blur(14px);
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .page-heading small {
            display: block;
            color: var(--cyan);
            font-size: 11px;
            letter-spacing: 3px;
            font-weight: 900;
            margin-bottom: 6px;
        }

        .page-heading h1 {
            font-size: 28px;
            line-height: 1;
            font-weight: 900;
        }

        .top-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .branch-select {
            min-width: 210px;
            height: 48px;
            padding: 0 17px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            border: 1px solid rgba(66,213,238,.22);
            background: #07374b;
            border-radius: 15px;
            color: white;
            cursor: pointer;
        }

        .branch-left {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            font-size: 14px;
        }

        .branch-dot {
            width: 9px;
            height: 9px;
            background: var(--cyan);
            border-radius: 50%;
            box-shadow: 0 0 12px var(--cyan);
        }

        .branch-arrow {
            color: #91b4c2;
            font-size: 15px;
        }

        .notification {
            width: 48px;
            height: 48px;
            border-radius: 15px;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #07374b;
            border: 1px solid rgba(66,213,238,.20);
            font-size: 19px;
        }

        /* =========================================================
           CONTENIDO PRINCIPAL
        ========================================================= */

        .content {
            max-width: 1500px;
            margin: 0 auto;
            padding: 32px 36px 55px;
        }

        /* =========================================================
           HERO
        ========================================================= */

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

        .hero-text {
            max-width: 650px;
        }

        .status {
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

        .status span {
            width: 9px;
            height: 9px;
            background: var(--cyan);
            border-radius: 50%;
            box-shadow: 0 0 13px var(--cyan);
        }

        .hero h2 {
            font-size: clamp(42px, 4.4vw, 68px);
            line-height: .94;
            letter-spacing: -3px;
            font-weight: 950;
            margin-bottom: 18px;
        }

        .hero h2 .cyan {
            color: var(--cyan);
        }

        .hero-description {
            max-width: 600px;
            color: #d4e6ed;
            font-size: 16px;
            line-height: 1.5;
            margin-bottom: 25px;
        }

        .hero-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

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
            transition: .2s ease;
        }

        .btn-primary {
            background: var(--cyan);
            color: #023146;
            box-shadow: 0 10px 25px rgba(66,213,238,.18);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 30px rgba(66,213,238,.25);
        }

        .btn-outline {
            border: 1px solid rgba(66,213,238,.40);
            color: white;
            background: rgba(2,29,43,.25);
        }

        .btn-outline:hover {
            background: rgba(66,213,238,.10);
        }

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

        /* =========================================================
           TARJETAS ESTADISTICAS
        ========================================================= */

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 18px;
            margin-top: 22px;
        }

        .stat-card {
            min-height: 150px;
            position: relative;
            overflow: hidden;
            padding: 21px;
            border-radius: 20px;
            background:
                linear-gradient(
                    145deg,
                    rgba(7,54,74,.96),
                    rgba(4,42,59,.94)
                );
            border: 1px solid rgba(66,213,238,.17);
            transition: .2s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            border-color: rgba(66,213,238,.35);
        }

        .stat-card::after {
            content: "";
            position: absolute;
            width: 95px;
            height: 95px;
            right: -35px;
            bottom: -35px;
            border-radius: 50%;
            background: rgba(66,213,238,.06);
        }

        .stat-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }

        .stat-title {
            color: #82aebf;
            font-size: 12px;
            font-weight: 800;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: rgba(66,213,238,.10);
            color: var(--cyan);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .stat-number {
            margin-top: 15px;
            font-size: 31px;
            font-weight: 950;
            line-height: 1;
        }

        .stat-extra {
            margin-top: 7px;
            color: var(--green);
            font-size: 11px;
            font-weight: 800;
        }

        .money .stat-icon {
            color: var(--green);
            background: rgba(22,224,164,.10);
        }

        .money .stat-extra {
            color: var(--green);
        }

        /* =========================================================
           GRAFICAS
        ========================================================= */

        .charts-grid {
            display: grid;
            grid-template-columns: 1.4fr .8fr;
            gap: 20px;
            margin-top: 24px;
        }

        .panel {
            border-radius: 22px;
            padding: 23px;
            background: rgba(5, 45, 62, .82);
            border: 1px solid rgba(66,213,238,.15);
        }

        .panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 25px;
        }

        .panel-header h3 {
            font-size: 16px;
            font-weight: 900;
        }

        .panel-header span {
            color: var(--cyan);
            font-size: 11px;
            font-weight: 800;
        }

        .chart-bars {
            height: 180px;
            display: flex;
            align-items: end;
            justify-content: space-around;
            gap: 16px;
            padding: 0 10px;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }

        .bar-column {
            flex: 1;
            max-width: 65px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: end;
            align-items: center;
            gap: 8px;
        }

        .bar-value {
            font-size: 10px;
            color: #b5d3de;
            font-weight: 800;
        }

        .bar {
            width: 100%;
            max-width: 38px;
            min-height: 15px;
            border-radius: 9px 9px 3px 3px;
            background: linear-gradient(
                180deg,
                var(--cyan),
                rgba(66,213,238,.25)
            );
            box-shadow: 0 0 18px rgba(66,213,238,.08);
        }

        .bar-label {
            font-size: 10px;
            color: #7198aa;
            margin-top: 6px;
        }

        .attendance {
            display: flex;
            align-items: center;
            gap: 24px;
        }

        .circle-chart {
            width: 145px;
            height: 145px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            background:
                conic-gradient(
                    var(--green) 0deg 341deg,
                    rgba(255,255,255,.07) 341deg 360deg
                );
            position: relative;
        }

        .circle-chart::before {
            content: "";
            position: absolute;
            width: 105px;
            height: 105px;
            border-radius: 50%;
            background: #07364a;
        }

        .circle-value {
            position: relative;
            z-index: 1;
            font-size: 23px;
            font-weight: 950;
        }

        .attendance-info strong {
            display: block;
            font-size: 25px;
            margin-bottom: 5px;
        }

        .attendance-info p {
            color: var(--muted);
            font-size: 12px;
            line-height: 1.5;
        }

        /* =========================================================
           NIVELES
        ========================================================= */

        .section {
            margin-top: 28px;
        }

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 15px;
        }

        .section-header h3 {
            font-size: 20px;
            font-weight: 950;
        }

        .section-header a {
            color: var(--cyan);
            font-size: 11px;
            font-weight: 900;
        }

        .levels-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 17px;
        }

        .level-card {
            position: relative;
            min-height: 210px;
            overflow: hidden;
            padding: 20px;
            border-radius: 21px;
            background:
                linear-gradient(
                    145deg,
                    rgba(7,54,74,.98),
                    rgba(3,35,51,.98)
                );
            border: 1px solid rgba(66,213,238,.17);
            transition: .25s ease;
        }

        .level-card:hover {
            transform: translateY(-5px);
            border-color: rgba(66,213,238,.45);
        }

        .level-head {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .animal-circle {
            width: 70px;
            height: 70px;
            min-width: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(2,27,41,.88);
            border: 2px solid var(--level-color);
            box-shadow:
                0 0 18px color-mix(
                    in srgb,
                    var(--level-color) 30%,
                    transparent
                );
        }

        .animal-circle img {
            width: 49px;
            height: 49px;
            object-fit: contain;
        }

        .level-number {
            color: var(--level-color);
            font-size: 10px;
            font-weight: 950;
            letter-spacing: 1.5px;
            margin-bottom: 4px;
        }

        .level-name {
            font-size: 19px;
            font-weight: 950;
            margin-bottom: 3px;
        }

        .level-description {
            color: #79a5b7;
            font-size: 11px;
        }

        .progress-info {
            display: flex;
            justify-content: space-between;
            margin-top: 24px;
            margin-bottom: 8px;
            font-size: 10px;
            font-weight: 900;
        }

        .progress-info span:last-child {
            color: var(--level-color);
        }

        .progress {
            width: 100%;
            height: 7px;
            border-radius: 20px;
            background: rgba(255,255,255,.07);
            overflow: hidden;
        }

        .progress span {
            display: block;
            height: 100%;
            width: var(--progress);
            border-radius: inherit;
            background: var(--level-color);
            box-shadow: 0 0 12px color-mix(
                in srgb,
                var(--level-color) 40%,
                transparent
            );
        }

        /* =========================================================
           ALUMNOS RECIENTES
        ========================================================= */

        .students-panel {
            overflow-x: auto;
        }

        .students-table {
            width: 100%;
            min-width: 750px;
            border-collapse: collapse;
        }

        .students-table th {
            padding: 12px 10px;
            text-align: left;
            color: #668e9f;
            font-size: 9px;
            letter-spacing: 1px;
            text-transform: uppercase;
            border-bottom: 1px solid rgba(255,255,255,.07);
        }

        .students-table td {
            padding: 13px 10px;
            border-bottom: 1px solid rgba(255,255,255,.055);
            font-size: 11px;
        }

        .student {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .avatar {
            width: 36px;
            height: 36px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 10px;
            background: #a7cad7;
            color: #123749;
            font-weight: 950;
        }

        .student-name {
            font-weight: 800;
        }

        .student-email {
            color: #668e9f;
            font-size: 9px;
            margin-top: 3px;
        }

        .level-mini {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: #dcecf2;
        }

        .level-mini img {
            width: 30px;
            height: 30px;
            object-fit: contain;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 9px;
            border-radius: 30px;
            color: var(--green);
            background: rgba(22,224,164,.07);
            border: 1px solid rgba(22,224,164,.25);
            font-size: 9px;
            font-weight: 900;
        }

        .attendance-line {
            width: 100px;
            height: 5px;
            border-radius: 20px;
            background: rgba(255,255,255,.07);
            overflow: hidden;
        }

        .attendance-line span {
            display: block;
            height: 100%;
            background: var(--cyan);
        }

        /* =========================================================
           RESPONSIVE
        ========================================================= */

        @media (max-width: 1250px) {

            .sidebar {
                width: 225px;
                min-width: 225px;
            }

            .main {
                width: calc(100% - 225px);
                margin-left: 225px;
            }

            .content {
                padding: 28px 25px 45px;
            }

            .stats-grid {
                grid-template-columns: repeat(3, 1fr);
            }

            .levels-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .hero-content {
                padding: 40px;
            }
        }

        @media (max-width: 950px) {

            .sidebar {
                width: 78px;
                min-width: 78px;
            }

            .main {
                width: calc(100% - 78px);
                margin-left: 78px;
            }

            .logo-area {
                height: 105px;
                padding: 15px;
            }

            .logo-area img {
                width: 55px;
                object-fit: contain;
                object-position: left;
            }

            .sidebar-content {
                padding: 15px 10px;
            }

            .menu-title {
                display: none;
            }

            .menu-item {
                justify-content: center;
                padding: 7px;
            }

            .menu-item span:not(.menu-icon) {
                display: none;
            }

            .menu-icon {
                margin: 0;
            }

            .sidebar-footer {
                font-size: 0;
            }

            .topbar {
                padding: 0 20px;
            }

            .hero-content {
                grid-template-columns: 1fr;
            }

            .hero-logo-box {
                display: none;
            }

            .charts-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 700px) {

            .topbar {
                height: auto;
                min-height: 78px;
                padding: 15px;
                gap: 12px;
            }

            .page-heading small {
                font-size: 8px;
                letter-spacing: 2px;
            }

            .page-heading h1 {
                font-size: 22px;
            }

            .top-actions {
                gap: 7px;
            }

            .branch-select {
                min-width: auto;
                width: 48px;
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
                width: 42px;
                height: 42px;
            }

            .content {
                padding: 18px 13px 35px;
            }

            .hero {
                min-height: auto;
                border-radius: 21px;
            }

            .hero-content {
                min-height: auto;
                padding: 30px 25px;
            }

            .hero h2 {
                font-size: 40px;
                letter-spacing: -2px;
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

            .stats-grid {
                grid-template-columns: 1fr 1fr;
                gap: 10px;
            }

            .stat-card {
                min-height: 130px;
                padding: 16px;
            }

            .stat-number {
                font-size: 25px;
            }

            .levels-grid {
                grid-template-columns: 1fr;
            }

            .attendance {
                flex-direction: column;
                align-items: flex-start;
            }

            .circle-chart {
                width: 120px;
                height: 120px;
            }

            .circle-chart::before {
                width: 88px;
                height: 88px;
            }
        }

        @media (max-width: 480px) {

            .sidebar {
                width: 64px;
                min-width: 64px;
            }

            .main {
                width: calc(100% - 64px);
                margin-left: 64px;
            }

            .logo-area {
                height: 85px;
            }

            .logo-area img {
                width: 45px;
            }

            .menu-item {
                min-height: 45px;
            }

            .topbar {
                flex-wrap: wrap;
            }

            .top-actions {
                width: 100%;
                justify-content: flex-end;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .hero h2 {
                font-size: 34px;
            }

            .section-header h3 {
                font-size: 17px;
            }
        }
    </style>
</head>

<body>

<div class="app">

    <!-- =========================================================
         SIDEBAR
    ========================================================== -->

    <aside class="sidebar">

        <div class="logo-area">
            <img
                src="{{ asset('images/quantika-logo.png') }}"
                alt="Quantika Pool"
            >
        </div>

        <div class="sidebar-content">

            <div class="menu-title">Principal</div>

            <nav class="menu">

                <a href="{{ url('/admin') }}" class="menu-item active">
                    <span class="menu-icon">⌂</span>
                    <span>Dashboard</span>
                </a>

                <a href="{{ url('/alumnos') }}" class="menu-item">
                    <span class="menu-icon">♙</span>
                    <span>Alumnos</span>
                </a>

                <a href="{{ url('/niveles') }}" class="menu-item">
                    <span class="menu-icon">◉</span>
                    <span>Niveles</span>
                </a>

                

                <a href="{{ url('/evaluaciones') }}" class="menu-item">
                    <span class="menu-icon">✓</span>
                    <span>Evaluaciones</span>
                </a>

                <a href="{{ url('/horarios') }}" class="menu-item">
                    <span class="menu-icon">▦</span>
                    <span>Horarios</span>
                </a>

            </nav>

            <div class="menu-title" style="margin-top:30px;">
                Administración
            </div>

            <nav class="menu">

                <a href="{{ route('instructores.index') }}" class="menu-item">

    <div class="menu-icon">
        ♟
    </div>

    Instructores

</a>
                <a href="{{ url('/pagos') }}" class="menu-item">
                    <span class="menu-icon">$</span>
                    <span>Pagos</span>
                </a>

               

          
            <div class="menu-title" style="margin-top:30px;">
                Sistema
            </div>

            <nav class="menu">

                

                <a href="{{ url('/configuracion') }}" class="menu-item">
                    <span class="menu-icon">⚙</span>
                    <span>Configuración</span>
                </a>

            </nav>

        </div>

        <div class="sidebar-footer">
            QUANTIKA POOL © 2026
        </div>

    </aside>


    <!-- =========================================================
         MAIN
    ========================================================== -->

    <main class="main">

        <!-- TOPBAR -->

        <header class="topbar">

            <div class="page-heading">

                <small>
                    QUANTIKA POOL · SUCURSAL 1
                </small>

                <h1>
                    Dashboard
                </h1>

            </div>

            <div class="top-actions">

                <button class="branch-select" type="button">

                    <div class="branch-left">
                        <span class="branch-dot"></span>
                        <span>Sucursal 1</span>
                    </div>

                    <span class="branch-arrow">▼</span>

                </button>

                <button class="notification" type="button">
                    🔔
                </button>

            </div>

        </header>


        <!-- CONTENIDO -->

        <div class="content">


            <!-- =================================================
                 HERO
            ================================================== -->

            <section class="hero">

                <div class="hero-content">

                    <div class="hero-text">

                        <div class="status">
                            <span></span>
                            SISTEMA ACTIVO
                        </div>

                        <h2>
                            El progreso<br>
                            comienza<br>
                            <span class="cyan">
                                en el agua.
                            </span>
                        </h2>

                        <p class="hero-description">
                            Administra alumnos, clases, evaluaciones y niveles
                            desde un solo lugar.
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
                                href="{{ url('/niveles') }}"
                                class="btn btn-outline"
                            >
                                Explorar niveles
                                <span>→</span>
                            </a>

                        </div>

                    </div>


                    <div class="hero-logo-box">

                        <img
                            src="{{ asset('images/quantika-logo.png') }}"
                            alt="Quantika Pool"
                        >

                    </div>

                </div>

            </section>


            <!-- =================================================
                 ESTADISTICAS
            ================================================== -->

            <section class="stats-grid">

                <div class="stat-card">

                    <div class="stat-top">
                        <span class="stat-title">
                            Alumnos activos
                        </span>

                        <span class="stat-icon">
                            ♟
                        </span>
                    </div>

                    <div class="stat-number">
                        248
                    </div>

                    <div class="stat-extra">
                        ↑ 12 este mes
                    </div>

                </div>


                <div class="stat-card">

                    <div class="stat-top">
                        <span class="stat-title">
                            Instructoresss
                        </span>

                        <span class="stat-icon">
                            ♙
                        </span>
                    </div>

                    <div class="stat-number">
                        18
                    </div>

                    <div class="stat-extra">
                        16 disponibles
                    </div>

                </div>


                <div class="stat-card">

                    <div class="stat-top">
                        <span class="stat-title">
                            Clases de hoy
                        </span>

                        <span class="stat-icon">
                            ≋
                        </span>
                    </div>

                    <div class="stat-number">
                        24
                    </div>

                    <div class="stat-extra">
                        6 en curso
                    </div>

                </div>


                <div class="stat-card">

                    <div class="stat-top">
                        <span class="stat-title">
                            Asistencia
                        </span>

                        <span class="stat-icon">
                            ✓
                        </span>
                    </div>

                    <div class="stat-number">
                        94.8%
                    </div>

                    <div class="stat-extra">
                        Excelente
                    </div>

                </div>


                <!-- DINERO -->

                <div class="stat-card money">

                    <div class="stat-top">

                        <span class="stat-title">
                            Ingresos del mes
                        </span>

                        <span class="stat-icon">
                            $
                        </span>

                    </div>

                    <div class="stat-number">
                        $48,650
                    </div>

                    <div class="stat-extra">
                        ↑ 8.4% este mes
                    </div>

                </div>

            </section>


            <!-- =================================================
                 GRAFICAS
            ================================================== -->

            <section class="charts-grid">

                <div class="panel">

                    <div class="panel-header">

                        <h3>
                            Actividad semanal
                        </h3>

                        <span>
                            Últimos 7 días
                        </span>

                    </div>

                    <div class="chart-bars">

                        <div class="bar-column">
                            <span class="bar-value">38</span>
                            <div class="bar" style="height:45%;"></div>
                            <span class="bar-label">Lun</span>
                        </div>

                        <div class="bar-column">
                            <span class="bar-value">52</span>
                            <div class="bar" style="height:62%;"></div>
                            <span class="bar-label">Mar</span>
                        </div>

                        <div class="bar-column">
                            <span class="bar-value">67</span>
                            <div class="bar" style="height:80%;"></div>
                            <span class="bar-label">Mié</span>
                        </div>

                        <div class="bar-column">
                            <span class="bar-value">59</span>
                            <div class="bar" style="height:70%;"></div>
                            <span class="bar-label">Jue</span>
                        </div>

                        <div class="bar-column">
                            <span class="bar-value">74</span>
                            <div class="bar" style="height:88%;"></div>
                            <span class="bar-label">Vie</span>
                        </div>

                        <div class="bar-column">
                            <span class="bar-value">48</span>
                            <div class="bar" style="height:58%;"></div>
                            <span class="bar-label">Sáb</span>
                        </div>

                        <div class="bar-column">
                            <span class="bar-value">35</span>
                            <div class="bar" style="height:42%;"></div>
                            <span class="bar-label">Dom</span>
                        </div>

                    </div>

                </div>


                <div class="panel">

                    <div class="panel-header">

                        <h3>
                            Asistencia general
                        </h3>

                        <span>
                            Este mes
                        </span>

                    </div>

                    <div class="attendance">

                        <div class="circle-chart">

                            <div class="circle-value">
                                94.8%
                            </div>

                        </div>

                        <div class="attendance-info">

                            <strong>
                                Excelente
                            </strong>

                            <p>
                                La asistencia promedio de los alumnos
                                se mantiene por encima del objetivo.
                            </p>

                        </div>

                    </div>

                </div>

            </section>


            <!-- =================================================
                 NIVELES
            ================================================== -->

            <section class="section">

                <div class="section-header">

                    <h3>
                        Niveles de aprendizaje
                    </h3>

                    <a href="{{ url('/niveles') }}">
                        Ver todos los niveles →
                    </a>

                </div>


                <div class="levels-grid">


                    <!-- TORTUGA -->

                    <article
                        class="level-card"
                        style="
                            --level-color:#16e0a4;
                            --progress:60%;
                        "
                    >

                        <div class="level-head">

                            <div class="animal-circle">

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


                        <div class="progress-info">

                            <span>
                                Progreso
                            </span>

                            <span>
                                60%
                            </span>

                        </div>

                        <div class="progress">
                            <span></span>
                        </div>

                    </article>


                    <!-- PEZ -->

                    <article
                        class="level-card"
                        style="
                            --level-color:#ffbd20;
                            --progress:45%;
                        "
                    >

                        <div class="level-head">

                            <div class="animal-circle">

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


                        <div class="progress-info">

                            <span>
                                Progreso
                            </span>

                            <span>
                                45%
                            </span>

                        </div>

                        <div class="progress">
                            <span></span>
                        </div>

                    </article>


                    <!-- DELFIN -->

                    <article
                        class="level-card"
                        style="
                            --level-color:#21c7ff;
                            --progress:70%;
                        "
                    >

                        <div class="level-head">

                            <div class="animal-circle">

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


                        <div class="progress-info">

                            <span>
                                Progreso
                            </span>

                            <span>
                                70%
                            </span>

                        </div>

                        <div class="progress">
                            <span></span>
                        </div>

                    </article>


                    <!-- TIBURON -->

                    <article
                        class="level-card"
                        style="
                            --level-color:#c05cff;
                            --progress:30%;
                        "
                    >

                        <div class="level-head">

                            <div class="animal-circle">

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


                        <div class="progress-info">

                            <span>
                                Progreso
                            </span>

                            <span>
                                30%
                            </span>

                        </div>

                        <div class="progress">
                            <span></span>
                        </div>

                    </article>

                </div>

            </section>


            <!-- =================================================
                 ALUMNOS RECIENTES
            ================================================== -->

            <section class="section">

                <div class="section-header">

                    <h3>
                        Alumnos recientes
                    </h3>

                    <a href="{{ url('/alumnos') }}">
                        Ver todos los alumnos →
                    </a>

                </div>


                <div class="panel students-panel">

                    <table class="students-table">

                        <thead>

                            <tr>
                                <th>Alumno</th>
                                <th>Nivel</th>
                                <th>Sucursal</th>
                                <th>Asistencia</th>
                                <th>Estado</th>
                                <th>Acciones</th>
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

                                            <div class="student-email">
                                                maria.gonzalez@email.com
                                            </div>

                                        </div>

                                    </div>

                                </td>

                                <td>

                                    <div class="level-mini">

                                        <img
                                            src="{{ asset('images/Niveles/delfin.png') }}"
                                            alt="Delfín"
                                        >

                                        <span>
                                            Delfín
                                        </span>

                                    </div>

                                </td>

                                <td>
                                    Quantika
                                </td>

                                <td>

                                    <div style="display:flex;align-items:center;gap:8px;">
                                        <span>95%</span>

                                        <div class="attendance-line">
                                            <span style="width:95%;"></span>
                                        </div>
                                    </div>

                                </td>

                                <td>
                                    <span class="badge">
                                        ● Activo
                                    </span>
                                </td>

                                <td>
                                    👁 &nbsp; ✎
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

                                            <div class="student-email">
                                                carlos.ramirez@email.com
                                            </div>

                                        </div>

                                    </div>

                                </td>

                                <td>

                                    <div class="level-mini">

                                        <img
                                            src="{{ asset('images/Niveles/pez.png') }}"
                                            alt="Pez"
                                        >

                                        <span>
                                            Pez
                                        </span>

                                    </div>

                                </td>

                                <td>
                                    Aqualix
                                </td>

                                <td>

                                    <div style="display:flex;align-items:center;gap:8px;">
                                        <span>88%</span>

                                        <div class="attendance-line">
                                            <span style="width:88%;"></span>
                                        </div>
                                    </div>

                                </td>

                                <td>
                                    <span class="badge">
                                        ● Activo
                                    </span>
                                </td>

                                <td>
                                    👁 &nbsp; ✎
                                </td>

                            </tr>


                            <tr>

                                <td>

                                    <div class="student">

                                        <div class="avatar">
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

                                    <div class="level-mini">

                                        <img
                                            src="{{ asset('images/Niveles/tortuga.png') }}"
                                            alt="Tortuga"
                                        >

                                        <span>
                                            Tortuga
                                        </span>

                                    </div>

                                </td>

                                <td>
                                    Quantika
                                </td>

                                <td>

                                    <div style="display:flex;align-items:center;gap:8px;">
                                        <span>76%</span>

                                        <div class="attendance-line">
                                            <span style="width:76%;"></span>
                                        </div>
                                    </div>

                                </td>

                                <td>
                                    <span class="badge">
                                        ● Activo
                                    </span>
                                </td>

                                <td>
                                    👁 &nbsp; ✎
                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </section>


        </div>

    </main>

</div>

</body>
</html>