<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', 'QUANTIKA POOL')
    </title>

    <link rel="stylesheet"
          href="{{ asset('css/quantika.css') }}">

</head>

<body>

<div class="quantika-app">

    {{-- =====================================================
         SIDEBAR
         ===================================================== --}}

    <aside class="sidebar">

        <div class="sidebar-logo">

            <img
                src="{{ asset('images/quantika-logo.png') }}"
                alt="Quantika Pool">

        </div>

        {{-- PERFIL --}}

        <div class="profile-box">

            <div class="profile-avatar">
                DC
            </div>

            <div>

                <div class="profile-name">
                    Administrador
                </div>

                <div class="profile-role">
                    Panel de control
                </div>

            </div>

        </div>


        {{-- PRINCIPAL --}}

        <div class="menu-title">
            PRINCIPAL
        </div>

        <nav class="menu">

            <a
                href="{{ url('/admin') }}"
                class="menu-item {{ request()->is('admin') ? 'active' : '' }}"
            >

                <span class="menu-icon">⌂</span>

                Dashboard

            </a>


            <a
                href="{{ url('/alumnos') }}"
                class="menu-item {{ request()->is('alumnos*') ? 'active' : '' }}"
            >

                <span class="menu-icon">♟</span>

                Alumnos

            </a>


            <a href="#" class="menu-item">

                <span class="menu-icon">◉</span>

                Niveles

            </a>


            <a href="#" class="menu-item">

                <span class="menu-icon">≋</span>

                Clases

            </a>


            <a href="#" class="menu-item">

                <span class="menu-icon">✓</span>

                Evaluaciones

            </a>


            <a href="#" class="menu-item">

                <span class="menu-icon">▣</span>

                Horarios

            </a>

        </nav>


        {{-- ADMINISTRACIÓN --}}

        <div class="menu-title">
            ADMINISTRACIÓN
        </div>

        <nav class="menu">

            <a href="#" class="menu-item">

                <span class="menu-icon">♟</span>

                Instructores

            </a>


            <a href="#" class="menu-item">

                <span class="menu-icon">$</span>

                Pagos

            </a>


            <a href="#" class="menu-item">

                <span class="menu-icon">▥</span>

                Reportes

            </a>

        </nav>


        {{-- SISTEMA --}}

        <div class="menu-title">
            SISTEMA
        </div>

        <nav class="menu">

            <a href="#" class="menu-item">

                <span class="menu-icon">⌂</span>

                Sucursales

            </a>


            <a href="#" class="menu-item">

                <span class="menu-icon">▦</span>

                Carriles / Alberca

            </a>


            <a href="#" class="menu-item">

                <span class="menu-icon">⚙</span>

                Configuración

            </a>

        </nav>


        <div class="sidebar-footer">

            QUANTIKA POOL © {{ date('Y') }}

        </div>

    </aside>


    {{-- =====================================================
         MAIN
         ===================================================== --}}

    <main class="main">

        {{-- TOPBAR --}}

        <header class="topbar">

            <div class="page-heading">

                <small>
                    QUANTIKA POOL
                </small>

                <h1>
                    @yield('page-title', 'Dashboard')
                </h1>

            </div>


            <div class="topbar-right">

                {{-- SELECTOR DE SUCURSAL --}}

                <div class="branch-selector">

                    <button class="branch-button">

                        <span>
                            🏢
                        </span>

                        <div>

                            <span class="branch-label">
                                SUCURSAL ACTIVA
                            </span>

                            <span class="branch-name">
                                Quantika
                            </span>

                        </div>

                        <span class="branch-arrow">
                            ▾
                        </span>

                    </button>


                    <div class="branch-menu">

                        <a href="{{ url('/admin?sucursal=quantika') }}"
                           class="branch-option active">

                            <img
                                src="{{ asset('images/quantika-logo.png') }}"
                                class="branch-logo"
                            >

                            <div class="branch-info">

                                <strong>
                                    Quantika
                                </strong>

                                <span>
                                    Sucursal Principal
                                </span>

                            </div>

                        </a>


                        <a href="{{ url('/admin?sucursal=aqualix') }}"
                           class="branch-option">

                            <img
                                src="{{ asset('images/logo-sucursal-2.png') }}"
                                class="branch-logo"
                            >

                            <div class="branch-info">

                                <strong>
                                    Aqualix
                                </strong>

                                <span>
                                    Sucursal Norte
                                </span>

                            </div>

                        </a>

                    </div>

                </div>


                {{-- NOTIFICACIONES --}}

                <button class="notification">
                    🔔
                </button>


                {{-- USUARIO --}}

                <div class="user-top">

                    <div class="user-top-avatar">
                        DC
                    </div>

                    <strong>
                        Administrador
                    </strong>

                    <span>
                        ▾
                    </span>

                </div>

            </div>

        </header>


        @yield('content')

    </main>

</div>

</body>

</html>