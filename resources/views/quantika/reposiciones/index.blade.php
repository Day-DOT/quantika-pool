@extends('quantika.super-admin.layout')

@section('title', 'Reposiciones de clases')
@section('page-title', 'Reposiciones de clases')

@section('content')

    <div class="section-header">
        <h3>Faltas pendientes de reposición este mes</h3>
        <span style="color:var(--muted); font-size:12px;">
            Máximo {{ $maximoPorMes }} reposiciones por alumno al mes · no aplican en clases de bebés
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
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('citas.reponer', $falta) }}" style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                                        @csrf
                                        <select name="horario_id" class="form-select" style="font-size:12px;" required>
                                            <option value="">Seleccionar horario</option>
                                            @foreach ($horariosDisponibles as $horarioOpcion)
                                                <option value="{{ $horarioOpcion->id }}">
                                                    {{ $horarioOpcion->nombre_grupo }} · {{ $horarioOpcion->dia_semana->label() }} {{ substr($horarioOpcion->hora_inicio, 0, 5) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="date" name="fecha" class="form-input" style="font-size:12px; width:150px;" required>
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
