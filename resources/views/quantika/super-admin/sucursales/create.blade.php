@extends('quantika.super-admin.layout')

@section('title', 'Nueva sucursal')
@section('page-title', 'Nueva sucursal')

@section('content')

    <a href="{{ route('super-admin.sucursales.index') }}" class="breadcrumb-back">← Volver a sucursales</a>

    <div class="panel form-card">
        <form method="POST" action="{{ route('super-admin.sucursales.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="form-grid">
                <div class="form-group">
                    <label for="nombre">Nombre de la sucursal</label>
                    <input type="text" id="nombre" name="nombre" class="form-input" value="{{ old('nombre') }}" required>
                    @error('nombre') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="codigo">Código único</label>
                    <input type="text" id="codigo" name="codigo" class="form-input" value="{{ old('codigo') }}" placeholder="p.ej. SUC3" required>
                    @error('codigo') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="direccion">Dirección</label>
                    <input type="text" id="direccion" name="direccion" class="form-input" value="{{ old('direccion') }}">
                    @error('direccion') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="text" id="telefono" name="telefono" class="form-input" value="{{ old('telefono') }}">
                    @error('telefono') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group full">
                    <label for="logo">Logo (opcional)</label>
                    <input type="file" id="logo" name="logo" class="form-input" accept="image/*">
                    @error('logo') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>Disponibilidad</label>
                    <div class="checkbox-row">
                        <input type="hidden" name="activa" value="0">
                        <input type="checkbox" id="activa" name="activa" value="1" checked>
                        <label for="activa" style="font-weight:600; color:var(--text);">Sucursal activa</label>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Guardar sucursal</button>
                <a href="{{ route('super-admin.sucursales.index') }}" class="btn btn-outline">Cancelar</a>
            </div>
        </form>
    </div>

@endsection
