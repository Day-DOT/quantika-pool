@extends('quantika.super-admin.layout')

@section('title', 'Carriles')
@section('page-title', 'Carriles / alberca')

@section('content')

    <div class="section-header">
        <h3>Carriles por sucursal</h3>
        <a href="{{ route('super-admin.carriles.create') }}" class="btn btn-primary">+ Nuevo carril</a>
    </div>

    @if($carriles->isEmpty())

        <div class="panel">
            <div class="empty-state">No hay carriles registrados con este filtro.</div>
        </div>

    @else

        @foreach ($carriles->groupBy('sucursal_id') as $grupo)

            @php($sucursal = $grupo->first()->sucursal)

            <div class="panel" style="margin-bottom:20px;">

                <div class="panel-header">
                    <h2>{{ $sucursal->nombre ?? 'Sin sucursal' }}</h2>
                    <span>{{ $grupo->count() }} carril{{ $grupo->count() === 1 ? '' : 'es' }}</span>
                </div>

                <div class="table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Carril</th>
                                <th>Capacidad máxima</th>
                                <th>Horarios asignados</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($grupo as $carril)
                                <tr>
                                    <td><strong>{{ $carril->nombre }}</strong></td>
                                    <td>{{ $carril->capacidad_maxima }} personas</td>
                                    <td>{{ $carril->horarios_count }}</td>
                                    <td>
                                        @if($carril->activo)
                                            <span class="badge">● Activo</span>
                                        @else
                                            <span class="badge badge-muted">● Inactivo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div style="display:flex; gap:10px;">
                                            <a href="{{ route('super-admin.carriles.edit', $carril) }}" class="section-link">Editar</a>
                                            <form method="POST" action="{{ route('super-admin.carriles.destroy', $carril) }}"
                                                  onsubmit="return confirm('¿Eliminar este carril?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="section-link" style="background:none; border:none; cursor:pointer; color:var(--red);">
                                                    Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>

        @endforeach

    @endif

@endsection
