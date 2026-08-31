@extends('quantika.super-admin.layout')

@section('title', 'Sucursales')
@section('page-title', 'Gestión multisucursal')

@section('content')

    <div class="section-header">
        <h3>Sucursales de Quantika Pool</h3>
        <a href="{{ route('super-admin.sucursales.create') }}" class="btn btn-primary">+ Nueva sucursal</a>
    </div>

    <div class="stats-grid cols-{{ min(max($sucursales->count(), 1), 4) }}">
        @foreach ($sucursales as $sucursal)
            <div class="stat-card">
                <div class="stat-top">
                    <span class="stat-name">{{ $sucursal->nombre }}</span>
                    <div class="stat-icon">🏢</div>
                </div>
                <div class="stat-value">{{ $sucursal->alumnos_count }}</div>
                <div class="stat-change">alumnos registrados</div>
            </div>
        @endforeach
    </div>

    <div class="section-header">
        <h3>Directorio de sucursales</h3>
    </div>

    <div class="panel">
        <div class="table-wrap">
            @if($sucursales->isEmpty())
                <div class="empty-state">No hay sucursales registradas todavía.</div>
            @else
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Sucursal</th>
                            <th>Código</th>
                            <th>Dirección</th>
                            <th>Teléfono</th>
                            <th>Usuarios</th>
                            <th>Carriles</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sucursales as $sucursal)
                            <tr>
                                <td>
                                    <div style="display:flex; align-items:center; gap:10px;">
                                        <div class="avatar" style="width:34px; height:34px; border-radius:10px; font-size:11px;">
                                            {{ $sucursal->logo_path ? '' : \Illuminate\Support\Str::substr($sucursal->nombre, 0, 2) }}
                                        </div>
                                        <strong>{{ $sucursal->nombre }}</strong>
                                    </div>
                                </td>
                                <td>{{ $sucursal->codigo }}</td>
                                <td>{{ $sucursal->direccion ?? '—' }}</td>
                                <td>{{ $sucursal->telefono ?? '—' }}</td>
                                <td>{{ $sucursal->usuarios_count }}</td>
                                <td>{{ $sucursal->carriles_count }}</td>
                                <td>
                                    @if($sucursal->activa)
                                        <span class="badge">● Activa</span>
                                    @else
                                        <span class="badge badge-muted">● Inactiva</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('super-admin.sucursales.show', $sucursal) }}" class="section-link">Ver / editar →</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

@endsection
