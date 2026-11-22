@extends('quantika.super-admin.layout')

@section('title', 'Fechas bloqueadas')
@section('page-title', 'Fechas bloqueadas')

@section('content')

    <p style="color:var(--muted); font-size:13px; margin-bottom:18px;">
        Días festivos, vacaciones o mantenimiento. En estas fechas no se pueden programar reposiciones, ni desde el portal del alumno ni desde la administración.
    </p>

    <div class="panel" style="margin-bottom:24px;">
        <div class="section-header" style="margin-top:0;">
            <h3>{{ $editando ? 'Editar fecha bloqueada' : 'Agregar fecha o periodo' }}</h3>
        </div>

        <form method="POST" action="{{ $editando ? route('fechas-bloqueadas.update', $editando) : route('fechas-bloqueadas.store') }}"
              style="display:flex; gap:10px; flex-wrap:wrap; align-items:flex-end;">
            @csrf
            @if ($editando) @method('PUT') @endif

            <div class="form-group" style="flex:1; min-width:150px;">
                <label>Desde</label>
                <input type="date" name="fecha_inicio" class="form-input" required value="{{ old('fecha_inicio', $editando?->fecha_inicio?->toDateString()) }}">
                @error('fecha_inicio') <span class="form-error">{{ $message }}</span> @enderror
            </div>
            <div class="form-group" style="flex:1; min-width:150px;">
                <label>Hasta <small style="color:var(--muted);">(vacío = un solo día)</small></label>
                <input type="date" name="fecha_fin" class="form-input" value="{{ old('fecha_fin', $editando && ! $editando->fecha_fin->isSameDay($editando->fecha_inicio) ? $editando->fecha_fin->toDateString() : '') }}">
                @error('fecha_fin') <span class="form-error">{{ $message }}</span> @enderror
            </div>
            <div class="form-group" style="flex:2; min-width:200px;">
                <label>Motivo</label>
                <input type="text" name="motivo" class="form-input" required maxlength="150" placeholder="Ej. Vacaciones de diciembre" value="{{ old('motivo', $editando?->motivo) }}">
                @error('motivo') <span class="form-error">{{ $message }}</span> @enderror
            </div>
            @if ($esVistaGlobal)
                <div class="form-group" style="flex:1; min-width:160px;">
                    <label>Sucursal</label>
                    <select name="sucursal_id" class="form-select">
                        <option value="">Todas las sucursales</option>
                        @foreach ($sucursales as $sucursal)
                            <option value="{{ $sucursal->id }}" @selected((string) old('sucursal_id', $editando?->sucursal_id) === (string) $sucursal->id)>{{ $sucursal->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div style="display:flex; gap:8px;">
                <button type="submit" class="btn btn-primary">{{ $editando ? 'Guardar' : 'Agregar' }}</button>
                @if ($editando)
                    <a href="{{ route('fechas-bloqueadas.index') }}" class="btn btn-outline">Cancelar</a>
                @endif
            </div>
        </form>
    </div>

    <div class="panel">
        @if ($bloqueos->isEmpty())
            <div class="empty-state">No hay fechas bloqueadas.</div>
        @else
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr><th>Fechas</th><th>Motivo</th><th>Aplica a</th><th></th></tr>
                    </thead>
                    <tbody>
                        @foreach ($bloqueos as $bloqueo)
                            <tr>
                                <td>
                                    <strong>{{ $bloqueo->descripcionDeFechas() }}</strong>
                                    @if ($bloqueo->fecha_fin->isPast())
                                        <span class="badge badge-muted">Pasada</span>
                                    @endif
                                </td>
                                <td>{{ $bloqueo->motivo }}</td>
                                <td>{{ $bloqueo->sucursal?->nombre ?? 'Todas las sucursales' }}</td>
                                <td>
                                    <div style="display:flex; gap:6px;">
                                        <a href="{{ route('fechas-bloqueadas.edit', $bloqueo) }}" class="btn btn-outline btn-sm">Editar</a>
                                        <form method="POST" action="{{ route('fechas-bloqueadas.destroy', $bloqueo) }}" onsubmit="return confirm('¿Quitar este bloqueo?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

@endsection
