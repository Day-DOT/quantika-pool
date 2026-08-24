<aside class="quantika-sidebar">

    <div class="sidebar-logo">

        <img
            src="{{ asset('images/quantika-logo.png') }}"
            alt="Quantika Pool"
        >

    </div>


    <div class="sidebar-user">

        <div class="sidebar-avatar">
            DC
        </div>

        <div class="sidebar-user-info">

            <strong>Administrador</strong>

            <span>
                Panel de control
            </span>

        </div>

    </div>


    <nav class="sidebar-navigation">

        <div class="nav-section-title">
            PRINCIPAL
        </div>


        <a
            href="{{ url('/admin') }}"
            class="nav-item {{ request()->is('admin') ? 'active' : '' }}"
        >

            <span class="nav-icon">
                ⌂
            </span>

            <span>
                Dashboard
            </span>

        </a>


        <a
            href="{{ url('/alumnos') }}"
            class="nav-item {{ request()->is('alumnos*') ? 'active' : '' }}"
        >

            <span class="nav-icon">
                ♟
            </span>

            <span>
                Alumnos
            </span>

        </a>


        <a href="#" class="nav-item">

            <span class="nav-icon">
                🐙
            </span>

            <span>
                Niveles
            </span>

        </a>


        <a href="#" class="nav-item">

            <span class="nav-icon">
                🏊
            </span>

            <span>
                Clases
            </span>

        </a>


        <a href="#" class="nav-item">

            <span class="nav-icon">
                ✓
            </span>

            <span>
                Evaluaciones
            </span>

        </a>


        <a href="#" class="nav-item">

            <span class="nav-icon">
                ◫
            </span>

            <span>
                Horarios
            </span>

        </a>


        <div class="nav-section-title">
            ADMINISTRACIÓN
        </div>


        <a href="#" class="nav-item">

            <span class="nav-icon">
                ♟
            </span>

            <span>
                Instructores
            </span>

        </a>


        <a href="#" class="nav-item">

            <span class="nav-icon">
                $
            </span>

            <span>
                Pagos
            </span>

        </a>


        <a href="#" class="nav-item">

            <span class="nav-icon">
                ▥
            </span>

            <span>
                Reportes
            </span>

        </a>


        <div class="nav-section-title">
            SISTEMA
        </div>


        <a href="#" class="nav-item">

            <span class="nav-icon">
                ◉
            </span>

            <span>
                Sucursales
            </span>

        </a>


        <a href="#" class="nav-item">

            <span class="nav-icon">
                ▦
            </span>

            <span>
                Carriles / Alberca
            </span>

        </a>


        <a href="#" class="nav-item">

            <span class="nav-icon">
                ⚙
            </span>

            <span>
                Configuración
            </span>

        </a>


        <form method="POST" action="{{ route('logout') }}" style="margin:0;">
            @csrf
            <button
                type="submit"
                class="nav-item"
                style="width:100%;background:none;border:none;cursor:pointer;text-align:left;font-family:inherit;">

                <span class="nav-icon">
                    ⎋
                </span>

                <span>
                    Cerrar sesión
                </span>

            </button>
        </form>

    </nav>


    <div class="sidebar-footer">

        QUANTIKA POOL © {{ date('Y') }}

    </div>

</aside>