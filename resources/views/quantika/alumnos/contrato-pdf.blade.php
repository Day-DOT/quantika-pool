<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1a1a1a; font-size: 12px; }
        h4 { margin-bottom: 4px; color: #093e52; }
        p { margin-top: 0; margin-bottom: 10px; }
        .firma-box { width: 45%; display: inline-block; vertical-align: top; margin-right: 4%; }
        .firma-img { height: 70px; border-bottom: 1px solid #333; }
        .firma-linea { border-top: 1px solid #333; margin-top: 4px; padding-top: 4px; }
    </style>
</head>
<body>

    @include('quantika.alumnos.partials.contrato-texto', [
        'alumno' => $alumno,
        'horario' => $horario,
        'esMenorDeEdad' => $esMenorDeEdad,
    ])

    @if ($cuotaInscripcion)
        <p><strong>Cuota de inscripción anual:</strong> ${{ number_format($cuotaInscripcion, 2) }}</p>
    @endif

    <h4>XXVIII. FIRMAS DE CONFORMIDAD</h4>
    <p>Las partes manifiestan su conformidad con el contenido del presente documento y firman para constancia.</p>

    <div>
        <div class="firma-box">
            <div><strong>{{ $esMenorDeEdad ? 'Padre, madre o tutor' : 'Usuario / alumno mayor de edad' }}</strong></div>
            <img src="{{ $firmaTitularImagen }}" class="firma-img" alt="Firma">
            <div class="firma-linea">
                Nombre completo: {{ $firmaTitularNombre }}<br>
                Fecha: {{ $fechaFirma->format('d/m/Y H:i') }}
            </div>
        </div>

        <div class="firma-box">
            <div><strong>Responsable de Quantika Pool</strong></div>
            <img src="{{ $firmaResponsableImagen }}" class="firma-img" alt="Firma">
            <div class="firma-linea">
                Nombre completo: {{ $firmaResponsableNombre }}<br>
                Fecha: {{ $fechaFirma->format('d/m/Y H:i') }}
            </div>
        </div>
    </div>

    <p style="margin-top:20px;">
        Nombre del establecimiento: Quantika Pool<br>
        Lugar: {{ $lugar ?? '—' }} • Fecha: {{ $fechaFirma->format('d/m/Y') }}
    </p>

    <p style="margin-top:30px; font-size:10px; color:#666; text-align:center;">
        Documento firmado electrónicamente desde el sistema Quantika Pool el {{ $fechaFirma->format('d/m/Y H:i') }}.
    </p>

</body>
</html>
