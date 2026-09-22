@extends('quantika.super-admin.layout')

@section('title', 'Planes')
@section('page-title', 'Planes de mensualidad')

@section('content')

    <div class="section-header">
        <h3>Planes disponibles</h3>
        <a href="{{ route('super-admin.planes.create') }}" class="btn btn-primary">+ Nuevo plan</a>
    </div>

    <div class="panel">

        @if($planes->isEmpty())

            <div class="empty-state">No hay planes registrados.</div>

        @else

            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Plan</th>
                            <th>Clases por semana</th>
                            <th>Precio</th>
                            <th>Alumnos con este plan</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($planes as $plan)
                            <tr>
                                <td><strong>{{ $plan->nombre }}</strong></td>
                                <td>{{ $plan->clases_por_semana }} clase{{ $plan->clases_por_semana === 1 ? '' : 's' }}/semana</td>
                                <td>{{ $plan->precio !== null ? '$'.number_format((float) $plan->precio, 2) : '—' }}</td>
                                <td>{{ $plan->alumnos_count }}</td>
                                <td>
                                    @if($plan->activo)
                                        <span class="badge">● Activo</span>
                                    @else
                                        <span class="badge badge-muted">● Inactivo</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="display:flex;gap:10px;align-items:center;">
                                        <a href="{{ route('super-admin.planes.edit', $plan) }}" class="section-link">Editar</a>
                                        <form method="POST" action="{{ route('super-admin.planes.destroy', $plan) }}"
                                              onsubmit="return confirm('¿Eliminar este plan?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="section-link" style="background:none;border:none;cursor:pointer;color:var(--red);">
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

        @endif

    </div>

@endsection
