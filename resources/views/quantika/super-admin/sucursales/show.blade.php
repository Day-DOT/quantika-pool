@extends('quantika.super-admin.layout')

@section('title', $sucursal->nombre)
@section('page-title', $sucursal->nombre)

@section('content')

    <a href="{{ route('super-admin.sucursales.index') }}" class="breadcrumb-back">← Volver a sucursales</a>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-top"><span class="stat-name">Usuarios</span><div class="stat-icon">👤</div></div>
            <div class="stat-value">{{ $sucursal->usuarios_count }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-top"><span class="stat-name">Alumnos</span><div class="stat-icon">♟</div></div>
            <div class="stat-value">{{ $sucursal->alumnos_count }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-top"><span class="stat-name">Instructores</span><div class="stat-icon">🏊</div></div>
            <div class="stat-value">{{ $sucursal->instructores_count }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-top"><span class="stat-name">Carriles</span><div class="stat-icon">▦</div></div>
            <div class="stat-value">{{ $sucursal->carriles_count }}</div>
        </div>
    </div>

    <div class="section-header">
        <h3>Datos de la sucursal</h3>
        <a href="{{ route('super-admin.carriles.index', ['sucursal_id' => $sucursal->id]) }}" class="section-link">
            Ver carriles de esta sucursal →
        </a>
    </div>

    <div class="panel form-card">
        <form method="POST" action="{{ route('super-admin.sucursales.update', $sucursal) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-grid">

                @if($sucursal->logo_path)
                    <div class="form-group full" style="align-items:center;">
                        <img src="{{ asset($sucursal->logo_path) }}" alt="Logo {{ $sucursal->nombre }}" style="height:70px; object-fit:contain;">
                    </div>
                @endif

                <div class="form-group">
                    <label for="nombre">Nombre de la sucursal</label>
                    <input type="text" id="nombre" name="nombre" class="form-input" value="{{ old('nombre', $sucursal->nombre) }}" required>
                    @error('nombre') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="codigo">Código único</label>
                    <input type="text" id="codigo" name="codigo" class="form-input" value="{{ old('codigo', $sucursal->codigo) }}" required>
                    @error('codigo') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="direccion">Dirección</label>
                    <input type="text" id="direccion" name="direccion" class="form-input" value="{{ old('direccion', $sucursal->direccion) }}">
                    @error('direccion') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="text" id="telefono" name="telefono" class="form-input" value="{{ old('telefono', $sucursal->telefono) }}">
                    @error('telefono') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group full">
                    <label for="logo">Actualizar logo</label>
                    <input type="file" id="logo" name="logo" class="form-input" accept="image/*">
                    @error('logo') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>Disponibilidad</label>
                    <div class="checkbox-row">
                        <input type="hidden" name="activa" value="0">
                        <input type="checkbox" id="activa" name="activa" value="1" {{ old('activa', $sucursal->activa) ? 'checked' : '' }}>
                        <label for="activa" style="font-weight:600; color:var(--text);">Sucursal activa</label>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
                <a href="{{ route('super-admin.sucursales.index') }}" class="btn btn-outline">Cancelar</a>
            </div>
        </form>

        <form method="POST" action="{{ route('super-admin.sucursales.destroy', $sucursal) }}" style="margin-top:24px; border-top:1px solid rgba(255,255,255,.07); padding-top:20px;"
              onsubmit="return confirm('¿Eliminar esta sucursal? Sólo es posible si no tiene datos asociados.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Eliminar sucursal</button>
        </form>
    </div>

@endsection
