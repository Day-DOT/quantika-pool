@extends('quantika.super-admin.layout')

@section('title', 'Criterios de evaluación')
@section('page-title', 'Criterios de evaluación')

@section('content')

    <div class="section-header">
        <h3>Rubros que evalúan los instructores</h3>
        <a href="{{ route('super-admin.criterios.create') }}" class="btn btn-primary">+ Nuevo criterio</a>
    </div>

    <form method="GET" action="{{ route('super-admin.criterios.index') }}" class="filters-bar panel">
        <div class="form-group">
            <label for="nivel_id">Nivel</label>
            <select id="nivel_id" name="nivel_id" class="form-select" onchange="this.form.submit()">
                <option value="">Todos los niveles</option>
                @foreach ($niveles as $nivel)
                    <option value="{{ $nivel->id }}" {{ (string) $nivelId === (string) $nivel->id ? 'selected' : '' }}>
                        {{ str_pad((string) $nivel->orden, 2, '0', STR_PAD_LEFT) }} · {{ $nivel->nombre }}
                    </option>
                @endforeach
            </select>
        </div>
        <a href="{{ route('super-admin.criterios.index') }}" class="btn btn-outline">Limpiar filtro</a>
    </form>

    @if($criterios->isEmpty())

        <div class="panel">
            <div class="empty-state">No hay criterios configurados con este filtro.</div>
        </div>

    @else

        @foreach ($criterios->groupBy('nivel_id') as $grupo)

            @php($nivel = $grupo->first()->nivel)

            <div class="panel" style="margin-bottom:20px;">

                <div class="panel-header">
                    <h2>
                        {{ $nivel ? str_pad((string) $nivel->orden, 2, '0', STR_PAD_LEFT).' · '.$nivel->nombre : 'Sin nivel' }}
                    </h2>
                    <span>{{ $grupo->count() }} criterio{{ $grupo->count() === 1 ? '' : 's' }}</span>
                </div>

                <div class="table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Orden</th>
                                <th>Criterio</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($grupo as $criterio)
                                <tr>
                                    <td>{{ $criterio->orden }}</td>
                                    <td>
                                        <strong>{{ $criterio->nombre }}</strong>
                                        @if($criterio->descripcion)
                                            <div style="color:var(--muted-2); font-size:10.5px;">{{ $criterio->descripcion }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($criterio->activo)
                                            <span class="badge">● Activo</span>
                                        @else
                                            <span class="badge badge-muted">● Inactivo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div style="display:flex; gap:10px;">
                                            <a href="{{ route('super-admin.criterios.edit', $criterio) }}" class="section-link">Editar</a>
                                            <form method="POST" action="{{ route('super-admin.criterios.destroy', $criterio) }}"
                                                  onsubmit="return confirm('¿Eliminar este criterio de evaluación?');">
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
