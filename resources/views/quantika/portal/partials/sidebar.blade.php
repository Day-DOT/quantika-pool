{{--
    Sidebar simplificado del Portal de Alumnos / Tutores.
    Recibe la variable $activo con el nombre de sección actual:
    'dashboard' | 'reservar' | 'progreso' | 'cuenta'
--}}
@php
    $usuario = auth()->user();
    $iniciales = collect(preg_split('/\s+/', trim($usuario->name)))
        ->map(fn ($parte) => mb_substr($parte, 0, 1))
        ->take(2)
        ->implode('');
@endphp

<aside class="sidebar">

    <div class="sidebar-logo">
        <img src="{{ auth()->user()->logoUrl() }}" alt="Quantika Pool">
    </div>

    <div class="sidebar-user">
        <div class="avatar">{{ mb_strtoupper($iniciales) }}</div>
        <div>
            <strong>{{ $usuario->name }}</strong>
            <span>Portal de tutores</span>
        </div>
    </div>

    <div class="sidebar-scroll">

        <div class="menu-title">MI FAMILIA</div>

        <nav class="menu">

            <a href="{{ route('portal.dashboard') }}" class="menu-item {{ ($activo ?? '') === 'dashboard' ? 'active' : '' }}">
                <div class="menu-icon">⌂</div>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('portal.reservar.index') }}" class="menu-item {{ ($activo ?? '') === 'reservar' ? 'active' : '' }}">
                <div class="menu-icon">≋</div>
                <span>Reservar clase</span>
            </a>

            <a href="{{ route('portal.progreso') }}" class="menu-item {{ ($activo ?? '') === 'progreso' ? 'active' : '' }}">
                <div class="menu-icon">✓</div>
                <span>Mi progreso</span>
            </a>

            <a href="{{ route('portal.cuenta') }}" class="menu-item {{ ($activo ?? '') === 'cuenta' ? 'active' : '' }}">
                <div class="menu-icon">$</div>
                <span>Pagos y clases</span>
            </a>

            <a href="{{ route('portal.qr') }}" class="menu-item {{ ($activo ?? '') === 'qr' ? 'active' : '' }}">
                <div class="menu-icon">▦</div>
                <span>Mi código QR</span>
            </a>

        </nav>

        <div class="menu-title">SESIÓN</div>

        <nav class="menu">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="menu-item logout" style="width:100%; background:none; border:none; cursor:pointer; text-align:left;">
                    <div class="menu-icon">⏻</div>
                    <span>Cerrar sesión</span>
                </button>
            </form>
        </nav>

    </div>

    <div class="sidebar-footer">
        QUANTIKA POOL © {{ now()->year }}
    </div>

</aside>

<script>
    // Conserva la posición de scroll del menú entre navegaciones (cada clic
    // recarga la página completa, así que sin esto el menú siempre "brincaba"
    // hasta arriba al entrar a otra sección).
    (function () {
        const menu = document.querySelector('.sidebar-scroll');
        if (! menu) return;

        const key = 'sidebarScrollTop';
        const guardado = sessionStorage.getItem(key);
        if (guardado !== null) {
            menu.scrollTop = parseInt(guardado, 10);
        }

        menu.addEventListener('scroll', function () {
            sessionStorage.setItem(key, menu.scrollTop);
        });
    })();
</script>
