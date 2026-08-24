@extends('quantika.super-admin.layout')

@section('title', 'Carriles / Alberca')
@section('page-title', 'Carriles / Alberca')

@push('styles')
<style>
    .cards {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 18px;
    }

    .carril-card {
        background: linear-gradient(145deg, #07394e, #052d40);
        border: 1px solid rgba(69,207,234,.18);
        border-radius: 18px;
        padding: 22px;
    }

    .carril-card h3 { font-size: 19px; margin-bottom: 6px; }
    .carril-card .branch { color: #8db1c3; font-size: 13px; margin-bottom: 14px; }
    .carril-card .stat { color: #d8e8ee; font-size: 13px; margin-bottom: 4px; }

    .pill {
        display: inline-flex;
        padding: 5px 11px;
        border-radius: 30px;
        font-size: 11px;
        font-weight: 800;
        margin-bottom: 14px;
    }

    .pill.on { background: rgba(19,227,162,.10); color: #13e3a2; border: 1px solid rgba(19,227,162,.30); }
    .pill.off { background: rgba(255,95,109,.10); color: #ff5f6d; border: 1px solid rgba(255,95,109,.30); }

    .row-actions { display: flex; gap: 8px; margin-top: 14px; }

    .icon-btn {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        border: 1px solid rgba(255,255,255,.08);
        background: #08384d;
        color: white;
        cursor: pointer;
    }

    .icon-btn:hover { background: #0c526a; }

    .btn-secondary { border: 1px solid rgba(66,213,238,.40); color: white; background: rgba(2,29,43,.25); }
    .btn-secondary:hover { background: rgba(66,213,238,.10); }
</style>
@endpush

@section('content')

    <a href="{{ route('configuracion.index') }}" class="breadcrumb-back">
        ← Volver a configuración
    </a>

    <div class="section-header" style="margin-top:0;">
        <p style="color:var(--muted); font-size:14px;">
            Administra los carriles disponibles {{ $esVistaGlobal ? 'de ambas sucursales' : 'de tu sucursal' }}.
        </p>

        <button type="button" class="btn btn-primary" onclick="abrirModalCarril()">
            + Nuevo carril
        </button>
    </div>

    <div class="cards">

        @forelse ($carriles as $carril)
            <div class="carril-card"
                 data-id="{{ $carril->id }}"
                 data-nombre="{{ $carril->nombre }}"
                 data-capacidad="{{ $carril->capacidad_maxima }}"
                 data-activo="{{ $carril->activo ? 1 : 0 }}">

                <h3>{{ $carril->nombre }}</h3>
                <div class="branch">{{ $carril->sucursal->nombre }}</div>

                <span class="pill {{ $carril->activo ? 'on' : 'off' }}">
                    {{ $carril->activo ? 'Activo' : 'Inactivo' }}
                </span>

                <div class="stat">Capacidad máxima: {{ $carril->capacidad_maxima }} alumnos</div>
                <div class="stat">{{ $carril->horarios_count }} clase{{ $carril->horarios_count === 1 ? '' : 's' }} activa{{ $carril->horarios_count === 1 ? '' : 's' }}</div>

                <div class="row-actions">
                    <button type="button" class="icon-btn" title="Editar" onclick="abrirModalCarril(this.closest('.carril-card'))">✎</button>
                    <form action="{{ route('carriles.destroy', $carril) }}" method="POST" onsubmit="return confirm('¿Eliminar o desactivar este carril?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="icon-btn" title="Eliminar">🗑</button>
                    </form>
                </div>

            </div>
        @empty
            <div class="empty-state">No hay carriles registrados todavía.</div>
        @endforelse

    </div>


    <div id="modalCarril" style="display:none;position:fixed;inset:0;z-index:200;background:rgba(1,15,23,.7);align-items:center;justify-content:center;">

        <div style="width:100%;max-width:440px;background:#052d40;border:1px solid rgba(65,208,235,.25);border-radius:20px;padding:28px;">

            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;">
                <h2 id="modalCarrilTitulo" style="font-size:20px;">Nuevo carril</h2>
                <button type="button" onclick="cerrarModalCarril()" style="background:none;border:none;color:white;font-size:22px;cursor:pointer;">×</button>
            </div>

            @if ($errors->any())
                <div style="margin-bottom:14px;padding:12px 16px;border-radius:12px;background:rgba(255,95,109,.10);border:1px solid rgba(255,95,109,.35);color:#ff9aa1;font-size:12px;">
                    <ul style="margin:0 0 0 16px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <?php
                $campoEstilo = 'width:100%;margin-top:6px;margin-bottom:14px;height:48px;padding:0 14px;border-radius:12px;border:1px solid rgba(83,214,238,.20);background:#042337;color:white;';
            ?>

            <form id="formCarrilCrear" method="POST" action="{{ route('carriles.store') }}">
                @csrf

                <label>Nombre del carril</label>
                <input type="text" name="nombre" required style="{{ $campoEstilo }}" placeholder="Carril 1">

                <label>Capacidad máxima</label>
                <input type="number" name="capacidad_maxima" min="1" max="50" value="8" required style="{{ $campoEstilo }}">

                @if ($esVistaGlobal)
                    <label>Sucursal</label>
                    <select name="sucursal_id" required style="{{ $campoEstilo }}">
                        <option value="">Seleccionar sucursal</option>
                        @foreach ($sucursales as $sucursalOpcion)
                            <option value="{{ $sucursalOpcion->id }}">{{ $sucursalOpcion->nombre }}</option>
                        @endforeach
                    </select>
                @endif

                <div style="display:flex;gap:12px;margin-top:6px;">
                    <button type="submit" class="btn btn-primary">Crear carril</button>
                    <button type="button" class="btn btn-secondary" onclick="cerrarModalCarril()">Cancelar</button>
                </div>
            </form>

            <form id="formCarrilEditar" method="POST" style="display:none;">
                @csrf
                @method('PUT')

                <label>Nombre del carril</label>
                <input type="text" name="nombre" required style="{{ $campoEstilo }}">

                <label>Capacidad máxima</label>
                <input type="number" name="capacidad_maxima" min="1" max="50" required style="{{ $campoEstilo }}">

                <label style="display:flex;align-items:center;gap:8px;margin-bottom:14px;">
                    <input type="checkbox" name="activo" value="1" style="width:auto;">
                    Carril activo
                </label>

                <div style="display:flex;gap:12px;margin-top:6px;">
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    <button type="button" class="btn btn-secondary" onclick="cerrarModalCarril()">Cancelar</button>
                </div>
            </form>

        </div>

    </div>

@endsection

@push('scripts')
<script>

function abrirModalCarril(card) {

    const modal = document.getElementById('modalCarril');
    const formCrear = document.getElementById('formCarrilCrear');
    const formEditar = document.getElementById('formCarrilEditar');
    const titulo = document.getElementById('modalCarrilTitulo');

    if (card) {
        titulo.textContent = 'Editar carril';
        formCrear.style.display = 'none';
        formEditar.style.display = 'block';
        formEditar.action = '/configuracion/carriles/' + card.dataset.id;
        formEditar.querySelector('[name="nombre"]').value = card.dataset.nombre || '';
        formEditar.querySelector('[name="capacidad_maxima"]').value = card.dataset.capacidad || '';
        formEditar.querySelector('[name="activo"]').checked = card.dataset.activo === '1';
    } else {
        titulo.textContent = 'Nuevo carril';
        formCrear.style.display = 'block';
        formEditar.style.display = 'none';
    }

    modal.style.display = 'flex';
}

function cerrarModalCarril() {
    document.getElementById('modalCarril').style.display = 'none';
}

@if ($errors->any())
    document.addEventListener('DOMContentLoaded', function () { abrirModalCarril(); });
@endif

</script>
@endpush
