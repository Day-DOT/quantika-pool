<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', 'Quantika Pool')
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet"
    >
</head>

<body class="quantika-body">

    <div class="quantika-app">

        @include('components.sidebar')

        <main class="quantika-main">

            <header class="quantika-topbar">

                <div class="topbar-title">

                    <span>QUANTIKA POOL</span>

                    <h1>
                        @yield('page-title', 'Dashboard')
                    </h1>

                </div>

                <div class="topbar-actions">

                    <button class="notification-button">
                        🔔
                    </button>

                    <div class="admin-profile">

                        <div class="admin-avatar">
                            DC
                        </div>

                        <div>
                            <strong>Administrador</strong>
                        </div>

                    </div>

                </div>

            </header>


            <section class="quantika-content">

                @yield('content')

            </section>

        </main>

    </div>

</body>
</html>