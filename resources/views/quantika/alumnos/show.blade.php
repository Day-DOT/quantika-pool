@extends('quantika.super-admin.layout')

@section('title', $alumno->nombreCompleto().' | Quantika Pool')

@section('page-title', 'Perfil del alumno')

@section('content')

    <div class="section-header">
        <div>
            <div style="color:var(--cyan); font-size:11px; font-weight:900; letter-spacing:2px; margin-bottom:6px;">
                PERFIL DEL ALUMNO
            </div>
            <h3 style="font-size:24px;">{{ $alumno->nombreCompleto() }}</h3>
        </div>

        <div style="display:flex;gap:10px;">
            <a href="{{ route('alumnos.edit', $alumno) }}" class="btn btn-outline btn-sm">✎ Editar</a>
            <a href="{{ url('/alumnos') }}" class="btn btn-outline btn-sm">← Regresar</a>
        </div>
    </div>


    <section class="stats-grid">

        <div class="stat-card">
            <div class="stat-top">
                <span class="stat-name">ALUMNO</span>
                <div class="stat-icon">👤</div>
            </div>
            <div class="stat-value" style="font-size:20px;">{{ $alumno->nombre }}</div>
            <div class="stat-change">{{ $alumno->apellidos }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-top">
                <span class="stat-name">NIVEL ACTUAL</span>
                <div class="stat-icon">🐬</div>
            </div>
            <div class="stat-value" style="font-size:20px;">{{ $alumno->nivel?->nombre ?? 'Sin asignar' }}</div>
            <div class="stat-change">
                {{ $alumno->nivel ? 'Nivel '.str_pad((string) $alumno->nivel->orden, 2, '0', STR_PAD_LEFT) : 'Aún sin nivel' }}
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-top">
                <span class="stat-name">PROGRESO</span>
                <div class="stat-icon">📈</div>
            </div>
            <div class="stat-value">{{ $progresoNivel }}%</div>
            <div class="stat-change">
                {{ $alumno->evaluaciones->first()?->fecha?->format('d/m/Y') ? 'Última evaluación: '.$alumno->evaluaciones->first()->fecha->format('d/m/Y') : 'Sin evaluaciones aún' }}
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-top">
                <span class="stat-name">ASISTENCIA</span>
                <div class="stat-icon">✓</div>
            </div>
            <div class="stat-value">{{ $asistenciaPct !== null ? $asistenciaPct.'%' : 'N/D' }}</div>
            <div class="stat-change">{{ $citasAsistidas }} de {{ $citasCompletadas }} clases</div>
        </div>

    </section>


    <div class="section-header">
        <h3>Avance del alumno</h3>
    </div>

    <div class="panel">
        <div style="display:flex; justify-content:space-between; margin-bottom:12px;">
            <strong>Progreso del nivel</strong>
            <span style="color:var(--cyan); font-weight:800;">{{ $progresoNivel }}%</span>
        </div>

        <div style="width:100%; height:12px; border-radius:20px; background:#062337; overflow:hidden;">
            <div style="width:{{ $progresoNivel }}%; height:100%; border-radius:20px; background:linear-gradient(90deg, #20cbe9, #70e9fa);"></div>
        </div>
    </div>


    <div class="section-header">
        <h3>Contacto y tutor</h3>
    </div>

    <div class="stats-grid">

        <div class="stat-card">
            <span class="stat-name">SUCURSAL</span>
            <div class="stat-value" style="font-size:18px;">{{ $alumno->sucursal->nombre }}</div>
        </div>

        <div class="stat-card">
            <span class="stat-name">TELÉFONO / CORREO</span>
            <div class="stat-value" style="font-size:16px;">{{ $alumno->telefono ?? 'Sin teléfono' }}</div>
            <div class="stat-change">{{ $alumno->email ?? 'Sin correo propio' }}</div>
        </div>

        <div class="stat-card">
            <span class="stat-name">TUTOR / RESPONSABLE</span>
            <div class="stat-value" style="font-size:16px;">{{ $alumno->tutorUser?->name ?? 'Sin tutor' }}</div>
            <div class="stat-change">{{ $alumno->tutorUser?->email }}{{ $alumno->tutorUser?->telefono ? ' · '.$alumno->tutorUser->telefono : '' }}</div>
        </div>

        <div class="stat-card">
            <span class="stat-name">ESTADO</span>
            <div class="stat-value" style="font-size:18px;">{{ $alumno->estado->label() }}</div>
            <div class="stat-change">Inscrito el {{ $alumno->fecha_inscripcion?->format('d/m/Y') }}</div>
        </div>

    </div>


    <div class="section-header">
        <h3>Progresión de niveles</h3>
    </div>

    <div class="panel">
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nivel</th>
                        <th>Desde</th>
                        <th>Hasta</th>
                        <th>Promovido por</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($alumno->historialNiveles as $historial)
                        <tr>
                            <td>{{ $historial->nivel?->nombre ?? 'Sin nivel' }}</td>
                            <td>{{ $historial->fecha_inicio?->format('d/m/Y') }}</td>
                            <td>{{ $historial->fecha_fin?->format('d/m/Y') ?? 'Vigente' }}</td>
                            <td>{{ $historial->promovidoPor?->name ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">Sin historial de niveles registrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>


    <div class="section-header">
        <h3>Clases, evaluaciones y pagos</h3>
    </div>

    <div class="stats-grid">

        <div class="stat-card">
            <span class="stat-name">GRUPO ACTUAL</span>
            @if ($alumno->inscripciones->isNotEmpty())
                @foreach ($alumno->inscripciones as $inscripcion)
                    <div class="stat-value" style="font-size:16px;">{{ $inscripcion->horario?->nombre_grupo }}</div>
                    <div class="stat-change">
                        {{ $inscripcion->horario?->instructor?->user?->name ?? 'Sin instructor' }} ·
                        {{ $inscripcion->horario?->dia_semana?->label() }} {{ $inscripcion->horario?->hora_inicio }}
                    </div>
                @endforeach
            @else
                <div class="stat-value" style="font-size:16px;">Sin grupo asignado</div>
            @endif
        </div>

        <div class="stat-card">
            <span class="stat-name">EVALUACIONES</span>
            <div class="stat-value" style="font-size:16px;">{{ $alumno->evaluaciones->count() }} registradas</div>
            <div class="stat-change">
                <a href="{{ route('evaluaciones.alumno', $alumno) }}" class="section-link">Ver historial de evaluaciones →</a>
            </div>
        </div>

        <div class="stat-card">
            <span class="stat-name">PAGOS</span>
            <div class="stat-value" style="font-size:16px;">{{ $alumno->pagos->count() }} registrados</div>
            <div class="stat-change">
                <a href="{{ route('pagos.alumno', $alumno) }}" class="section-link">Ver historial de pagos →</a>
            </div>
        </div>

        <div class="stat-card">
            <span class="stat-name">ACCIONES</span>
            @if ($alumno->estado->value === 'activo')
                <form action="{{ route('alumnos.baja', $alumno) }}" method="POST" onsubmit="return confirm('¿Deseas dar de baja a {{ $alumno->nombreCompleto() }}?');">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-outline" style="margin-top:8px;">Dar de baja</button>
                </form>
            @else
                <form action="{{ route('alumnos.reactivar', $alumno) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-primary" style="margin-top:8px;">Reactivar alumno</button>
                </form>
            @endif
        </div>

    </div>

@endsection
