@extends('quantika.super-admin.layout')

@section('title', 'Usuarios')
@section('page-title', 'Gestión de usuarios')

@section('content')

    <div class="section-header">
        <h3>Todos los usuarios del sistema</h3>
        <a href="{{ route('super-admin.usuarios.create') }}" class="btn btn-primary">+ Nuevo usuario</a>
    </div>

    <form method="GET" action="{{ route('super-admin.usuarios.index') }}" class="filters-bar panel">
        <div class="form-group">
            <label for="buscar">Buscar</label>
            <input type="text" id="buscar" name="buscar" class="form-input" placeholder="Nombre o correo" value="{{ $filtros['buscar'] ?? '' }}" oninput="autoBuscarUsuarios()">
        </div>

        <div class="form-group">
            <label for="role">Rol</label>
            <select id="role" name="role" class="form-select" onchange="this.form.submit()">
                <option value="">Todos</option>
                @foreach ($roles as $rol)
                    <option value="{{ $rol->value }}" {{ ($filtros['role'] ?? '') === $rol->value ? 'selected' : '' }}>
                        {{ $rol->label() }}
                    </option>
                @endforeach
            </select>
        </div>

        <noscript><button type="submit" class="btn btn-outline">Filtrar</button></noscript>
        <a href="{{ route('super-admin.usuarios.index') }}" class="btn btn-outline">Limpiar</a>
    </form>

    <div class="panel">
        <div class="table-wrap">
            @if($usuarios->isEmpty())
                <div class="empty-state">No se encontraron usuarios con esos filtros.</div>
            @else
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Rol</th>
                            <th>Sucursal</th>
                            <th>Teléfono</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($usuarios as $usuario)
                            <tr>
                                <td>
                                    <strong>{{ $usuario->name }}</strong>
                                    <div style="color:var(--muted-2); font-size:10.5px; margin-top:2px;">{{ $usuario->email }}</div>
                                </td>
                                <td>
                                    <span class="badge badge-blue">{{ $usuario->role->label() }}</span>
                                </td>
                                <td>
                                    {{ $usuario->sucursal->nombre ?? $usuario->instructor?->sucursal?->nombre ?? '—' }}
                                </td>
                                <td>{{ $usuario->telefono ?? '—' }}</td>
                                <td>
                                    @if($usuario->activo)
                                        <span class="badge">● Activo</span>
                                    @else
                                        <span class="badge badge-muted">● Inactivo</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="display:flex; gap:10px; align-items:center;">
                                        <a href="{{ route('super-admin.usuarios.edit', $usuario) }}" class="section-link">Editar</a>
                                        @if($usuario->id !== auth()->id())
                                            <form method="POST" action="{{ route('super-admin.usuarios.estado', $usuario) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="section-link" style="background:none; border:none; cursor:pointer; color:{{ $usuario->activo ? 'var(--red)' : 'var(--green)' }};">
                                                    {{ $usuario->activo ? 'Desactivar' : 'Activar' }}
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="pagination-wrap">
                    {{ $usuarios->links() }}
                </div>
            @endif
        </div>
    </div>

@endsection

@push('scripts')
<script>
    let buscarUsuariosTimeout;

    function autoBuscarUsuarios() {
        clearTimeout(buscarUsuariosTimeout);
        buscarUsuariosTimeout = setTimeout(function () {
            document.getElementById('buscar').closest('form').submit();
        }, 450);
    }
</script>
@endpush
