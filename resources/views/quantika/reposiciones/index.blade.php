@extends('quantika.super-admin.layout')

@section('title', 'Reposiciones de clases')
@section('page-title', 'Reposiciones de clases')

@section('content')

    @if ($solicitudesPendientes > 0)
        <div class="panel" style="margin-bottom:20px; border-color:rgba(66,216,239,.4);">
            Hay {{ $solicitudesPendientes }} solicitud(es) de reposición enviadas por tutores esperando respuesta.
            <a href="{{ route('reservas.index') }}" class="section-link">Revisarlas →</a>
        </div>
    @endif

    <div class="section-header">
        <h3>Faltas pendientes de reposición este mes</h3>
        <span style="color:var(--muted); font-size:12px;">
            Máximo {{ $maximoPorMes }} reposiciones por alumno al mes · solo niños y adultos (los bebés no reponen) · aquí puedes elegir cualquier grupo; los tutores solo ven los de su mismo nivel
        </span>
    </div>

    <div class="panel" style="margin-bottom:25px;">

        @if ($faltas->isEmpty())

            <div class="empty-state">No hay faltas pendientes de reposición este mes.</div>

        @else

            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Alumno</th>
                            <th>Clase perdida</th>
                            <th>Reponer en</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($faltas as $falta)
                            <tr>
                                <td><strong>{{ $falta->alumno->nombreCompleto() }}</strong></td>
                                <td>
                                    {{ $falta->horario->nombre_grupo }}
                                    <div style="color:var(--muted); font-size:12px;">
                                        {{ $falta->fecha->translatedFormat('d M Y') }} · {{ $falta->horario->dia_semana->label() }}
                                    </div>
                                    @foreach ($falta->solicitudesPendientes as $solicitud)
                                        <div style="color:var(--yellow); font-size:11px; font-weight:800; margin-top:4px;">
                                            El tutor pidió reponerla el {{ $solicitud->fecha->format('d/m') }} en {{ $solicitud->horario?->nombre_grupo }}.
                                            <a href="{{ route('reservas.index') }}" class="section-link">Revisar solicitud</a>
                                        </div>
                                    @endforeach
                                </td>
                                <td>
                                    @php
                                        // La escuela puede reponer en cualquier grupo de la
                                        // sucursal (los bebés no reponen). Los de su mismo nivel
                                        // se listan primero y se marcan.
                                        $nivelAlumno = $falta->alumno?->nivel?->nombre;
                                        $admite = \App\Support\Reposiciones::admiteReposicion($falta->alumno);
                                        $horariosCompatibles = $admite
                                            ? $horariosDisponibles
                                                ->filter(fn ($horario) => $horario->sucursal_id === $falta->sucursal_id && $horario->id !== $falta->horario_id)
                                                ->map(fn ($horario) => [
                                                    'horario' => $horario,
                                                    'mismoNivel' => \App\Support\Reposiciones::grupoCompatible($horario, $falta->alumno),
                                                ])
                                                ->sortBy(fn ($opcion) => $opcion['mismoNivel'] ? 0 : 1)
                                            : collect();
                                    @endphp
                                    <form method="POST" action="{{ route('citas.reponer', $falta) }}" style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                                        @csrf
                                        <select name="horario_id" class="form-select" style="font-size:12px;" required>
                                            <option value="">
                                                @if (! $admite)
                                                    Las clases de bebés no tienen reposición
                                                @elseif ($horariosCompatibles->isEmpty())
                                                    No hay grupos activos en la sucursal
                                                @else
                                                    Seleccionar grupo{{ $nivelAlumno ? ' · su nivel: '.$nivelAlumno : '' }}
                                                @endif
                                            </option>
                                            @foreach ($horariosCompatibles as $opcion)
                                                <option value="{{ $opcion['horario']->id }}">
                                                    {{ $opcion['mismoNivel'] ? '★ Mismo nivel · ' : '' }}{{ $opcion['horario']->etiqueta() }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="date" name="fecha" class="form-input" style="font-size:12px; width:150px;" max="{{ $falta->fecha->copy()->endOfMonth()->toDateString() }}" required>
                                        <button type="submit" class="btn btn-primary btn-sm">Programar</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        @endif

    </div>

    <div class="section-header">
        <h3>Reposiciones programadas</h3>
    </div>

    <div class="panel">

        @if ($reposiciones->isEmpty())

            <div class="empty-state">Aún no hay reposiciones programadas.</div>

        @else

            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Alumno</th>
                            <th>Falta original</th>
                            <th>Repuesta en</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reposiciones as $reposicion)
                            <tr>
                                <td><strong>{{ $reposicion->alumno->nombreCompleto() }}</strong></td>
                                <td>{{ optional($reposicion->citaOriginal?->fecha)->translatedFormat('d M Y') ?? '—' }}</td>
                                <td>
                                    {{ $reposicion->horario->nombre_grupo }} · {{ $reposicion->fecha->translatedFormat('d M Y') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        @endif

    </div>

@endsection
