<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asistencia · QUANTIKA POOL</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <style>
        :root {
            --cyan: #42d5ed;
            --green: #13e3a2;
            --red: #ff6b6b;
            --dark: #022536;
            --muted: #7ea3b3;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(160deg, var(--dark), #011a27);
            color: #eaf6fb;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .card {
            width: 100%;
            max-width: 420px;
            background: linear-gradient(145deg, rgba(7,54,74,.96), rgba(3,35,51,.98));
            border: 1px solid rgba(66,213,238,.18);
            border-radius: 24px;
            padding: 34px 26px;
            text-align: center;
        }

        .icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
        }

        .icon.exito {
            background: rgba(19,227,162,.12);
            color: var(--green);
        }

        .icon.error {
            background: rgba(255,107,107,.12);
            color: var(--red);
        }

        h1 {
            font-size: 19px;
            margin: 0 0 8px;
        }

        p {
            color: var(--muted);
            font-size: 13px;
            line-height: 1.6;
            margin: 0 0 22px;
        }

        .btn {
            display: inline-block;
            padding: 13px 22px;
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
        <div class="icon {{ $exito ? 'exito' : 'error' }}">{{ $exito ? '✓' : '!' }}</div>
        <h1>{{ $alumno?->nombreCompleto() ?? 'Código QR' }}</h1>
        <p>{{ $mensaje }}</p>
        <a href="{{ route('asistencia.escanear') }}" class="btn">Escanear otro código</a>
    </div>

</body>
</html>
