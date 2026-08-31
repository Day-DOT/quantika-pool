@extends('quantika.super-admin.layout')

@section('title', 'Reservas pendientes')
@section('page-title', 'Reservas pendientes de aprobación')

@section('content')

    <div class="section-header">
        <h3>Reservas hechas por alumnos/tutores</h3>
        <span style="color:var(--muted); font-size:12px;">{{ $reservas->count() }} pendiente{{ $reservas->count() === 1 ? '' : 's' }}</span>
    </div>

    <div class="panel">

        @if($reservas->isEmpty())

            <div class="empty-state">No hay reservas pendientes de aprobación.</div>

        @else

            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Alumno</th>
                            <th>Grupo / Horario</th>
                            <th>Instructor</th>
                            <th>Solicitada</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reservas as $reserva)
                            <tr>
                                <td><strong>{{ $reserva->alumno->nombreCompleto() }}</strong></td>
                                <td>
                                    {{ $reserva->horario->nombre_grupo }}
                                    <div style="color:var(--muted); font-size:12px;">
                                        {{ $reserva->horario->dia_semana->label() }} · {{ substr($reserva->horario->hora_inicio, 0, 5) }}–{{ substr($reserva->horario->hora_fin, 0, 5) }}
                                        · {{ $reserva->horario->nivel?->nombre }}
                                    </div>
                                </td>
                                <td>{{ $reserva->horario->instructor?->user?->name ?? 'Sin instructor' }}</td>
                                <td>{{ $reserva->created_at->translatedFormat('d M Y, H:i') }}</td>
                                <td>
                                    <div style="display:flex; gap:10px;">
                                        <form method="POST" action="{{ route('reservas.aprobar', $reserva) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-primary btn-sm">Aprobar</button>
                                        </form>
                                        <form method="POST" action="{{ route('reservas.rechazar', $reserva) }}"
                                              onsubmit="return confirm('¿Rechazar esta reserva?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-outline btn-sm">Rechazar</button>
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
