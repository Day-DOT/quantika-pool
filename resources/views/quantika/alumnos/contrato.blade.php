@extends('quantika.super-admin.layout')

@section('title', 'Firmar contrato | QUANTIKA POOL')

@section('page-title', 'Firmar contrato')

@section('content')

    <div class="section-header">
        <div>
            <h3>Contrato de adhesión — {{ $alumno->nombreCompleto() }}</h3>
            <p style="color:var(--muted); font-size:13px; margin-top:6px;">
                Lee el contrato con el {{ $esMenorDeEdad ? 'padre, madre o tutor' : 'alumno' }} y captura las firmas de conformidad al final.
            </p>
        </div>
    </div>

    <div class="panel form-card">

        <div style="max-height:420px; overflow-y:auto; border:1px solid var(--border, #2a3f4c); border-radius:10px; padding:18px 22px; font-size:13px; line-height:1.6; color:var(--text);">
            @include('quantika.alumnos.partials.contrato-texto', [
                'alumno' => $alumno,
                'horario' => $horario,
                'esMenorDeEdad' => $esMenorDeEdad,
            ])
        </div>

        <form method="POST" action="{{ route('alumnos.contrato.store', $alumno) }}" id="formContrato" style="margin-top:25px;">
            @csrf

            <div class="form-grid">
                <div class="form-group">
                    <label>Cuota de inscripción anual (opcional)</label>
                    <input type="number" step="0.01" min="0" class="form-input" name="cuota_inscripcion" value="{{ old('cuota_inscripcion') }}">
                </div>

                <div class="form-group">
                    <label>Lugar</label>
                    <input type="text" class="form-input" name="lugar" value="{{ old('lugar') }}" placeholder="Ciudad">
                </div>
            </div>

            <h3 style="margin:25px 0 15px;">
                Firma de {{ $esMenorDeEdad ? 'padre, madre o tutor' : 'usuario / alumno mayor de edad' }}
            </h3>

            <div class="form-group full">
                <label>Nombre completo de quien firma</label>
                <input type="text" class="form-input" name="firma_titular_nombre" value="{{ old('firma_titular_nombre', $alumno->nombreTutor() ?? $alumno->nombreCompleto()) }}" required>
            </div>

            <div class="form-group full">
                <canvas id="padTitular" width="600" height="180" style="width:100%; max-width:600px; height:180px; border:1px solid var(--border, #2a3f4c); border-radius:10px; background:#fff; touch-action:none; cursor:crosshair;"></canvas>
                <div style="margin-top:8px;">
                    <button type="button" class="btn-outline" onclick="limpiarFirma('padTitular')">Limpiar firma</button>
                </div>
                <input type="hidden" name="firma_titular_imagen" id="firma_titular_imagen">
            </div>

            <h3 style="margin:25px 0 15px;">Firma del responsable de Quantika Pool</h3>

            <div class="form-group full">
                <label>Nombre completo de quien firma por Quantika Pool</label>
                <input type="text" class="form-input" name="firma_responsable_nombre" value="{{ old('firma_responsable_nombre', auth()->user()->name) }}" required>
            </div>

            <div class="form-group full">
                <canvas id="padResponsable" width="600" height="180" style="width:100%; max-width:600px; height:180px; border:1px solid var(--border, #2a3f4c); border-radius:10px; background:#fff; touch-action:none; cursor:crosshair;"></canvas>
                <div style="margin-top:8px;">
                    <button type="button" class="btn-outline" onclick="limpiarFirma('padResponsable')">Limpiar firma</button>
                </div>
                <input type="hidden" name="firma_responsable_imagen" id="firma_responsable_imagen">
            </div>

            @error('firma_titular_imagen')
                <p class="form-error">{{ $message }}</p>
            @enderror
            @error('firma_responsable_imagen')
                <p class="form-error">{{ $message }}</p>
            @enderror

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Guardar contrato firmado</button>
                <a href="{{ route('alumnos.show', $alumno) }}" class="btn btn-outline">Cancelar</a>
            </div>

        </form>

    </div>

@endsection

@push('scripts')
<script>
    const firmas = {};

    function iniciarFirma(idCanvas) {
        const canvas = document.getElementById(idCanvas);
        const ctx = canvas.getContext('2d');
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.strokeStyle = '#03202e';

        let dibujando = false;

        function posicion(evento) {
            const rect = canvas.getBoundingClientRect();
            const escalaX = canvas.width / rect.width;
            const escalaY = canvas.height / rect.height;
            const punto = evento.touches ? evento.touches[0] : evento;
            return {
                x: (punto.clientX - rect.left) * escalaX,
                y: (punto.clientY - rect.top) * escalaY,
            };
        }

        function empezar(evento) {
            evento.preventDefault();
            dibujando = true;
            const pos = posicion(evento);
            ctx.beginPath();
            ctx.moveTo(pos.x, pos.y);
        }

        function mover(evento) {
            if (!dibujando) return;
            evento.preventDefault();
            const pos = posicion(evento);
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
            firmas[idCanvas] = true;
        }

        function terminar() {
            dibujando = false;
        }

        canvas.addEventListener('pointerdown', empezar);
        canvas.addEventListener('pointermove', mover);
        canvas.addEventListener('pointerup', terminar);
        canvas.addEventListener('pointerleave', terminar);
    }

    function limpiarFirma(idCanvas) {
        const canvas = document.getElementById(idCanvas);
        canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
        firmas[idCanvas] = false;
    }

    iniciarFirma('padTitular');
    iniciarFirma('padResponsable');

    document.getElementById('formContrato').addEventListener('submit', function (evento) {
        if (!firmas['padTitular'] || !firmas['padResponsable']) {
            evento.preventDefault();
            alert('Ambas firmas son obligatorias antes de guardar el contrato.');
            return;
        }

        document.getElementById('firma_titular_imagen').value = document.getElementById('padTitular').toDataURL('image/png');
        document.getElementById('firma_responsable_imagen').value = document.getElementById('padResponsable').toDataURL('image/png');
    });
</script>
@endpush
