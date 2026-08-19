<header class="topbar">

    <div class="topbar-left">

        <button
            class="mobile-menu"
            onclick="toggleSidebar()"
        >
            ☰
        </button>

        <div>

            <div class="brand-small">
                QUANTIKA POOL
            </div>

            <h1>
                @yield('page-title', 'Dashboard')
            </h1>

        </div>

    </div>


    <div class="topbar-right">

        {{-- NOTIFICACIONES --}}
        <button class="notification-button">

            🔔

            <span class="notification-badge">
                3
            </span>

        </button>


        {{-- PERFIL --}}
        <div class="profile-box">

            <div class="avatar avatar-cyan">
                DC
            </div>

            <span>
                Administrador
            </span>

            <span class="profile-arrow">
               ⌄
            </span>

        </div>

    </div>

</header>