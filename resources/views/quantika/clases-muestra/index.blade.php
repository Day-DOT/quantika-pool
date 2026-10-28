@extends('quantika.super-admin.layout')

@section('title', 'Clases muestra')
@section('page-title', 'Clases muestra')

@section('content')

    <div class="section-header">
        <h3>Historial de clases muestra</h3>
        <a href="{{ route('horarios.index') }}#clase-muestra" class="btn btn-primary btn-sm">★ Agendar clase muestra</a>
    </div>

    <p style="color:var(--muted); font-size:12px; margin:-6px 0 16px;">
        La clase muestra es gratuita una sola vez. Al agendar una nueva, el sistema avisa si el teléfono, el correo o el
        nombre del alumno ya aparecen aquí. Las canceladas no cuentan como tomadas.
    </p>

    <form method="GET" action="{{ route('clases-muestra.index') }}" style="display:flex; gap:8px; margin-bottom:16px; flex-wrap:wrap;">
        <input type="search" name="q" value="{{ $busqueda }}" class="form-input" style="flex:1; min-width:220px;"
               placeholder="Buscar por alumno, tutor, teléfono o correo...">
        <button type="submit" class="btn btn-outline btn-sm">Buscar</button>
        @if ($busqueda !== '')
            <a href="{{ route('clases-muestra.index') }}" class="btn btn-outline btn-sm">Limpiar</a>
        @endif
    </form>

    <div class="panel">

        @if ($clases->isEmpty())

            <div class="empty-state">
                {{ $busqueda !== '' ? 'Nadie con esos datos ha tomado una clase muestra.' : 'Todavía no hay clases muestra registradas.' }}
            </div>

        @else

            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Alumno</th>
                            <th>Tutor y contacto</th>
                            <th>Clase</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($clases as $clase)
                            <tr>
                                <td>
                                    <strong>{{ $clase->nombre_alumno }}</strong>
                                    <div style="color:var(--muted); font-size:12px;">{{ $clase->edad }} {{ $clase->edad === 1 ? 'año' : 'años' }}</div>
                                </td>
                                <td>
                                    {{ $clase->nombre_tutor }}
                                    <div style="color:var(--muted); font-size:12px;">
                                        {{ $clase->telefono }}{{ $clase->email ? ' · '.$clase->email : '' }}
                                    </div>
                                </td>
                                <td>
                                    {{ $clase->fecha->translatedFormat('D d M Y') }} · {{ substr((string) $clase->hora_inicio, 0, 5) }}
                                    <div style="color:var(--muted); font-size:12px;">
                                        {{ $clase->horario?->nombre_grupo ?? 'Grupo eliminado' }}
                                        @if ($clase->horario)
                                            · {{ $clase->horario->carril?->nombre ?? 'Sin carril' }}
                                            · {{ $clase->horario->instructor?->user?->name ?? 'Sin instructor' }}
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge {{ match ($clase->estado) {
                                        \App\Enums\EstadoClaseMuestra::Asistio => '',
                                        \App\Enums\EstadoClaseMuestra::NoAsistio => 'badge-red',
                                        \App\Enums\EstadoClaseMuestra::Cancelada => 'badge-muted',
                                        default => 'badge-yellow',
                                    } }}">{{ $clase->estado->label() }}</span>
                                </td>
                                <td>
                                    <div style="display:flex; gap:6px; flex-wrap:wrap;">
                                        @if ($clase->estado === \App\Enums\EstadoClaseMuestra::Programada)
                                            @unless ($clase->fecha->isFuture())
                                                <form method="POST" action="{{ route('clases-muestra.estado', $clase) }}">
                                                    @csrf @method('PATCH')
                                                    <button type="submit" name="estado" value="asistio" class="btn btn-outline btn-sm">Asistió</button>
                                                </form>
                                                <form method="POST" action="{{ route('clases-muestra.estado', $clase) }}">
                                                    @csrf @method('PATCH')
                                                    <button type="submit" name="estado" value="no_asistio" class="btn btn-outline btn-sm">No asistió</button>
                                                </form>
                                            @endunless
                                            <form method="POST" action="{{ route('clases-muestra.estado', $clase) }}"
                                                  onsubmit="return confirm(@js('¿Cancelar la clase muestra de '.$clase->nombre_alumno.'? Se libera su lugar y no contará como tomada.'));">
                                                @csrf @method('PATCH')
                                                <button type="submit" name="estado" value="cancelada" class="btn btn-danger btn-sm">Cancelar</button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('clases-muestra.estado', $clase) }}">
                                                @csrf @method('PATCH')
                                                <button type="submit" name="estado" value="programada" class="btn btn-outline btn-sm">Deshacer</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrap">
                {{ $clases->links() }}
            </div>

        @endif

    </div>

@endsection
