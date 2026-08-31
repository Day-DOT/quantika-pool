<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $codigo }} · Quantika Pool</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <style>
        :root {
            --cyan: #42d5ed;
            --dark: #022536;
            --muted: #82a7b8;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, Helvetica, sans-serif;
            background: linear-gradient(160deg, var(--dark), #011a27);
            color: #eaf6fb;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .card {
            width: 100%;
            max-width: 460px;
            background: linear-gradient(145deg, rgba(7,54,74,.96), rgba(3,35,51,.98));
            border: 1px solid rgba(66,213,238,.18);
            border-radius: 24px;
            padding: 40px 32px;
            text-align: center;
        }

        .codigo {
            font-size: 15px;
            font-weight: 900;
            letter-spacing: 3px;
            color: var(--cyan);
            margin-bottom: 12px;
        }

        h1 { font-size: 22px; margin: 0 0 10px; }

        p {
            color: var(--muted);
            font-size: 14px;
            line-height: 1.6;
            margin: 0 0 26px;
        }

        .btn {
            display: inline-block;
            padding: 13px 24px;
            border-radius: 13px;
            background: var(--cyan);
            color: #022536;
            font-weight: 800;
            font-size: 13px;
            text-decoration: none;
        }
    </style>
</head>
<body>

    <div class="card">
        <div class="codigo">ERROR {{ $codigo }}</div>
        <h1>{{ $titulo }}</h1>
        <p>{{ $mensaje }}</p>
        <a href="{{ url('/') }}" class="btn">Volver al inicio</a>
    </div>

</body>
</html>
