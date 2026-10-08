<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escanear asistencia · QUANTIKA POOL</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
    <style>
        :root {
            --cyan: #42d5ed;
            --dark: #022536;
            --dark-2: #07374c;
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
            padding: 26px;
            text-align: center;
        }

        h1 {
            font-size: 20px;
            margin: 0 0 6px;
        }

        p {
            color: var(--muted);
            font-size: 13px;
            margin: 0 0 20px;
        }

        #reader {
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid rgba(66,213,238,.25);
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: var(--cyan);
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
        }

        #estado {
            margin-top: 14px;
            font-size: 12px;
            color: var(--muted);
            min-height: 18px;
        }
    </style>
</head>
<body>

    <div class="card">
        <h1>Escanear asistencia</h1>
        <p>Apunta la cámara al código QR del alumno para registrar su asistencia de hoy.</p>

        <div id="reader" style="width:100%;"></div>
        <div id="estado"></div>

        <a href="{{ url()->previous() }}" class="back-link">← Volver</a>
    </div>

    <script>
        const estado = document.getElementById('estado');

        if (typeof Html5Qrcode === 'undefined') {
            estado.textContent = 'No se pudo cargar el lector de códigos QR. Verifica tu conexión a internet y recarga la página.';
        } else {
            // Solo se buscan códigos QR y, si el navegador lo trae, se usa su
            // detector nativo: ambos ajustes aceleran bastante la lectura.
            const html5QrCode = new Html5Qrcode('reader', {
                formatsToSupport: [Html5QrcodeSupportedFormats.QR_CODE],
                experimentalFeatures: { useBarCodeDetectorIfSupported: true },
            });

            const rutaRegistro = @json(url('/q'));
            let procesando = false;

            html5QrCode.start(
                { facingMode: 'environment' },
                {
                    fps: 20,
                    qrbox: function (ancho, alto) {
                        const lado = Math.floor(Math.min(ancho, alto) * 0.8);

                        return { width: lado, height: lado };
                    },
                    videoConstraints: {
                        facingMode: 'environment',
                        width: { ideal: 1920 },
                        height: { ideal: 1080 },
                    },
                },
                function (decodedText) {
                    if (procesando) return;

                    // Solo se aceptan códigos de asistencia de la escuela: se
                    // toma el identificador del alumno y se abre siempre en
                    // este mismo sitio, sin seguir direcciones ajenas.
                    const coincidencia = decodedText.trim().match(/\/(?:asistencia\/qr|q)\/([A-Za-z0-9]{20,64})\/?$/);

                    if (! coincidencia) {
                        estado.textContent = 'Ese código no es un QR de asistencia de la escuela. Intenta con el del alumno.';

                        return;
                    }

                    procesando = true;
                    estado.textContent = 'Código detectado, registrando...';
                    html5QrCode.stop().finally(function () {
                        window.location.href = rutaRegistro + '/' + coincidencia[1];
                    });
                },
                function () {
                    // Se dispara constantemente mientras no detecta un QR: se ignora.
                }
            ).catch(function (err) {
                estado.textContent = 'No se pudo acceder a la cámara: ' + err;
            });
        }
    </script>

</body>
</html>
