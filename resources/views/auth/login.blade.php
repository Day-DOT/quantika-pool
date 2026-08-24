<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Iniciar sesión | QUANTIKA POOL</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@600;700;800;900&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg: #031f2f;
            --card: #073349;
            --cyan: #42d8ef;
            --cyan-2: #22c8e8;
            --text: #f4fbff;
            --muted: #86aabd;
            --border: rgba(69, 215, 239, .18);
            --danger: #ff6b6b;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background:
                radial-gradient(circle at 75% 10%, rgba(20, 110, 145, .18), transparent 35%),
                var(--bg);
            color: var(--text);
            font-family: 'Inter', sans-serif;
            padding: 24px;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            padding: 40px 36px;
            border-radius: 26px;
            background: linear-gradient(145deg, rgba(7, 54, 74, .97), rgba(4, 37, 55, .97));
            border: 1px solid var(--border);
            box-shadow: 0 30px 70px rgba(0, 0, 0, .35);
        }

        .login-logo {
            display: flex;
            justify-content: center;
            margin-bottom: 22px;
        }

        .login-logo img {
            width: 150px;
            max-width: 100%;
            object-fit: contain;
        }

        .login-card h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 24px;
            font-weight: 800;
            text-align: center;
        }

        .login-card p.subtitle {
            margin-top: 6px;
            text-align: center;
            color: var(--muted);
            font-size: 13px;
        }

        form { margin-top: 30px; display: flex; flex-direction: column; gap: 18px; }

        label {
            display: block;
            margin-bottom: 7px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .5px;
            color: var(--muted);
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            height: 50px;
            padding: 0 16px;
            border-radius: 14px;
            border: 1px solid var(--border);
            background: rgba(3, 31, 47, .55);
            color: var(--text);
            font-size: 14px;
            outline: none;
            transition: border-color .2s ease;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: var(--cyan);
        }

        .remember-row {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: var(--muted);
        }

        button.btn-submit {
            height: 52px;
            border-radius: 15px;
            border: none;
            background: var(--cyan);
            color: #033047;
            font-size: 14px;
            font-weight: 900;
            cursor: pointer;
            transition: transform .2s ease, box-shadow .2s ease;
        }

        button.btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 35px rgba(66, 216, 239, .28);
        }

        .errors {
            padding: 12px 16px;
            border-radius: 12px;
            background: rgba(255, 107, 107, .1);
            border: 1px solid rgba(255, 107, 107, .35);
            color: var(--danger);
            font-size: 13px;
        }

        .status {
            padding: 12px 16px;
            border-radius: 12px;
            background: rgba(20, 231, 173, .1);
            border: 1px solid rgba(20, 231, 173, .35);
            color: #14e7ad;
            font-size: 13px;
        }

        .login-footer {
            margin-top: 26px;
            text-align: center;
            color: #50778b;
            font-size: 11px;
        }
    </style>
</head>
<body>

    <div class="login-card">

        <div class="login-logo">
            <img src="{{ asset('images/quantika-logo.png') }}" alt="Quantika Pool">
        </div>

        <h1>Bienvenido de nuevo</h1>
        <p class="subtitle">Inicia sesión para continuar en QUANTIKA POOL</p>

        @if (session('status'))
            <div class="status" style="margin-top:20px">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="errors" style="margin-top:20px">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <label for="email">Correo electrónico</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
            </div>

            <div>
                <label for="password">Contraseña</label>
                <input id="password" type="password" name="password" required autocomplete="current-password">
            </div>

            <div class="remember-row">
                <input type="checkbox" name="remember" id="remember">
                <label for="remember" style="margin:0">Recordarme en este dispositivo</label>
            </div>

            <button type="submit" class="btn-submit">Iniciar sesión</button>
        </form>

        <div class="login-footer">QUANTIKA POOL © {{ date('Y') }}</div>

    </div>

</body>
</html>
