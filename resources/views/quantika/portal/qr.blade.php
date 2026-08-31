<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi código QR · QUANTIKA POOL</title>
    @include('quantika.portal.partials.styles')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>
<body>

<div class="quantika-app">

    @include('quantika.portal.partials.sidebar', ['activo' => 'qr'])

    <main class="main">

        @include('quantika.portal.partials.topbar', [
            'titulo' => 'Mi código QR',
            'alumnos' => $alumnos,
            'alumno' => $alumno,
        ])

        <div class="content">

            @if (! $alumno)

                <div class="empty-state">
                    <h3>Aún no tienes alumnos registrados</h3>
                    <p>Cuando la escuela registre a tu hijo o hija, podrás ver aquí su código QR de asistencia.</p>
                </div>

            @else

                <div class="section-header">
                    <h3>Código QR de {{ $alumno->nombreCompleto() }}</h3>
                </div>

                <p style="color:var(--muted); font-size:13px; margin-bottom:20px; max-width:640px;">
                    Muestra este código al llegar a la alberca para que el instructor o la administración
                    registren la asistencia de {{ $alumno->nombreCompleto() }}.
                </p>

                <div class="data-card" style="padding:30px; display:flex; flex-direction:column; align-items:center; gap:16px; max-width:340px;">
                    <div id="qrCode"></div>
                    <p id="qrCodeError" style="display:none; color:var(--red,#ff6b6b); font-size:12px; text-align:center;">
                        No se pudo cargar el generador de códigos QR. Verifica tu conexión a internet y vuelve a intentar.
                    </p>
                    <div style="font-weight:800; text-align:center;">{{ $alumno->nombreCompleto() }}</div>
                </div>

                <script>
                    if (typeof QRCode === 'undefined') {
                        document.getElementById('qrCodeError').style.display = 'block';
                    } else {
                        new QRCode(document.getElementById('qrCode'), {
                            text: @json($alumno->qrUrl()),
                            width: 220,
                            height: 220,
                            colorDark: '#022536',
                            colorLight: '#ffffff',
                        });
                    }
                </script>

            @endif

        </div>

    </main>

</div>

</body>
</html>
