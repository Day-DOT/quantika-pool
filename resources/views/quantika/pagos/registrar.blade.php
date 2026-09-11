@extends('quantika.super-admin.layout')

@php($pago = $pago ?? null)
@section('title', $pago ? 'Editar pago' : 'Registrar fecha de pago')
@section('page-title', $pago ? 'Editar pago' : 'Registrar fecha de pago')

@push('styles')
<style>
    .status-options {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .status-option {
        flex: 1;
        min-width: 130px;
    }

    .status-option input {
        display: none;
    }

    .status-option span {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 45px;
        border-radius: 13px;
        background: rgba(0,28,42,.5);
        border: 1px solid rgba(255,255,255,.07);
        color: #789fac;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: .2s;
    }

    .status-option input:checked + span {
        color: #42d5ed;
        border-color: #32cce7;
        background: rgba(40,202,228,.08);
    }

    .student-search {
        margin-bottom: 8px;
    }

    @media(max-width:650px) {
        .form-actions { flex-direction: column; }
        .form-actions .btn { width: 100%; }
    }
</style>
@endpush

@section('content')

    <a href="{{ route('pagos.index') }}" class="breadcrumb-back">← Volver a pagos</a>

    <p style="color:var(--muted); font-size:14px; margin-bottom:20px;">
        Registra una mensualidad, inscripción o concepto adicional.
    </p>

    <form method="POST" action="{{ $pago ? route('pagos.update', $pago) : route('pagos.store') }}" enctype="multipart/form-data">
        @csrf
        @if ($pago)
            @method('PUT')
        @endif

        <div class="panel form-card">

            <div class="form-grid">

                <div class="form-group">
                    <label>Alumno</label>
                    <input type="search" id="buscar-alumno" class="form-input student-search" placeholder="Buscar alumno por nombre..." autocomplete="off">
                    <select name="alumno_id" id="alumno-id" class="form-select" required>
                        <option value="">Selecciona un alumno</option>
                        @foreach ($alumnos as $alumnoOpcion)
                            <option value="{{ $alumnoOpcion->id }}" data-nombre="{{ \Illuminate\Support\Str::lower($alumnoOpcion->nombreCompleto()) }}" @selected(old('alumno_id', $alumnoSeleccionado) == $alumnoOpcion->id)>
                                {{ $alumnoOpcion->nombreCompleto() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Periodo</label>
                    <input type="text" name="periodo" class="form-input" value="{{ old('periodo', $periodoSugerido) }}" placeholder="2026-08">
                </div>

                <div class="form-group">
                    <label>Concepto</label>
                    <select name="concepto" class="form-select" required>
                        @foreach ($conceptos as $conceptoOpcion)
                            <option value="{{ $conceptoOpcion->value }}" @selected(old('concepto', $pago?->concepto?->value) === $conceptoOpcion->value)>{{ $conceptoOpcion->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Monto</label>
                    <input type="number" name="monto" step="0.01" min="0" value="{{ old('monto', $pago?->monto) }}" placeholder="$0.00" class="form-input" style="font-size:22px;font-weight:800;" required>
                </div>

                <div class="form-group">
                    <label>Fecha de vencimiento</label>
                    <input type="date" name="fecha_vencimiento" value="{{ old('fecha_vencimiento', $pago?->fecha_vencimiento?->format('Y-m-d')) }}" class="form-input">
                </div>

                <div class="form-group">
                    <label>Fecha de pago</label>
                    <input type="date" name="fecha_pago" value="{{ old('fecha_pago', $pago?->fecha_pago?->format('Y-m-d')) }}" class="form-input">
                </div>

                <div class="form-group full">
                    <label>Estado</label>

                    <div class="status-options">
                        @foreach ($estados as $estadoOpcion)
                            <label class="status-option">
                                <input type="radio" name="estado" value="{{ $estadoOpcion->value }}" @checked(old('estado', $pago?->estado?->value ?? 'pendiente') === $estadoOpcion->value)>
                                <span>
                                    {{ match($estadoOpcion->value) {
                                        'pendiente' => '🟡',
                                        'pagado' => '🟢',
                                        'en_revision' => '🔵',
                                        default => '⚪',
                                    } }} {{ $estadoOpcion->label() }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="form-group full">
                    <label>Comprobante</label>
                    <input type="file" name="comprobante" class="form-input" accept=".jpg,.jpeg,.png,.pdf">
                </div>

                <div class="form-group full">
                    <label>Observaciones</label>
                    <textarea name="observaciones" class="form-textarea" placeholder="Agrega alguna observación...">{{ old('observaciones', $pago?->observaciones) }}</textarea>
                </div>

            </div>

            <div class="form-actions">
                <a href="{{ route('pagos.index') }}" class="btn btn-outline">Cancelar</a>
                <button type="submit" class="btn btn-primary">{{ $pago ? 'Guardar cambios' : 'Registrar fecha de pago' }}</button>
            </div>

        </div>

    </form>

@endsection

@push('scripts')
<script>
    const buscadorAlumno = document.getElementById('buscar-alumno');
    const selectorAlumno = document.getElementById('alumno-id');

    buscadorAlumno?.addEventListener('input', function () {
        const busqueda = this.value.trim().toLowerCase();

        Array.from(selectorAlumno.options).forEach(function (opcion) {
            opcion.hidden = Boolean(opcion.value) && !opcion.dataset.nombre.includes(busqueda);
        });

        const seleccionActual = selectorAlumno.selectedOptions[0];
        if (seleccionActual?.hidden) {
            selectorAlumno.value = '';
        }
    });
</script>
@endpush
