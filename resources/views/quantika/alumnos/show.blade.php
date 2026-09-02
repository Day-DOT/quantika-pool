@extends('quantika.super-admin.layout')

@section('title', $alumno->nombreCompleto().' | Quantika Pool')

@section('page-title', 'Perfil del alumno')

@section('content')

    <div class="section-header">
        <div>
            <div style="color:var(--cyan); font-size:11px; font-weight:900; letter-spacing:2px; margin-bottom:6px;">
                PERFIL DEL ALUMNO
            </div>
            <h2 style="font-size:24px;">{{ $alumno->nombreCompleto() }}</h2>
        </div>

        <div style="display:flex;gap:10px;">
            <a href="{{ route('alumnos.edit', $alumno) }}" class="btn btn-outline btn-sm">✎ Editar</a>
            <a href="{{ url('/alumnos') }}" class="btn btn-outline btn-sm">← Regresar</a>
            <form
                action="{{ route('alumnos.destroy', $alumno) }}"
                method="POST"
                onsubmit="return confirm('¿Eliminar permanentemente a {{ $alumno->nombreCompleto() }}? Esto borra también su historial de citas, pagos y evaluaciones. Esta acción no se puede deshacer.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline btn-sm" style="color:#ff6b6b; border-color:#ff6b6b;">🗑 Eliminar</button>
            </form>
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
            <div class="stat-value" style="font-size:16px;">{{ $alumno->nombreTutor() ?? 'Sin tutor asignado' }}</div>
            <div class="stat-change">{{ $alumno->tutorUser?->email }}{{ $alumno->telefonoTutor() ? ' · '.$alumno->telefonoTutor() : '' }}</div>
        </div>

        <div class="stat-card">
            <span class="stat-name">ESTADO</span>
            <div class="stat-value" style="font-size:18px;">{{ $alumno->estado->label() }}</div>
            <div class="stat-change">Inscrito el {{ $alumno->fecha_inscripcion?->format('d/m/Y') }}</div>
        </div>

        <div class="stat-card">
            <span class="stat-name">PLAN DE MENSUALIDAD</span>
            <div class="stat-value" style="font-size:18px;">{{ $alumno->plan?->nombre ?? 'Sin plan asignado' }}</div>
            <div class="stat-change">
                @if ($alumno->plan)
                    {{ $alumno->clasesEstaSemana() }} de {{ $alumno->plan->clases_por_semana }} clases usadas esta semana
                @else
                    Sin límite semanal configurado
                @endif
            </div>
        </div>

    </div>


    <div class="section-header">
        <h3>Código QR de asistencia</h3>
    </div>

    <div class="panel" style="display:flex; align-items:center; gap:24px; flex-wrap:wrap;">
        <div id="qrCodeAlumno"></div>
        <p id="qrCodeAlumnoError" style="display:none; color:var(--red); font-size:12px;">
            No se pudo cargar el generador de códigos QR. Verifica tu conexión a internet y recarga la página.
        </p>
        <div style="flex:1; min-width:220px;">
            <p style="color:var(--muted); font-size:13px; line-height:1.6; margin-bottom:14px;">
                Comparte este código con {{ $alumno->nombreTutor() ?? 'el tutor' }} (también lo puede ver desde
                su portal) o imprímelo. Al escanearlo desde
                <a href="{{ route('asistencia.escanear') }}" class="section-link">Escanear asistencia</a>
                se registra la asistencia de {{ $alumno->nombreCompleto() }} en la clase que tenga programada hoy.
            </p>
            <button type="button" class="btn btn-outline btn-sm" onclick="descargarQrAlumno()">Descargar imagen</button>
        </div>
    </div>


    <div class="section-header">
        <h3>Documentos</h3>
        <a href="{{ route('alumnos.edit', $alumno) }}" class="section-link">Subir o reemplazar →</a>
    </div>

    <div class="panel">
        <div class="stats-grid cols-3">

            <div class="stat-card">
                <div class="stat-top">
                    <span class="stat-name">CERTIFICADO MÉDICO</span>
                    <div class="stat-icon">🩺</div>
                </div>
                @if ($alumno->certificado_medico_path)
                    <div class="stat-value" style="font-size:14px;">
                        <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($alumno->certificado_medico_path) }}" target="_blank">Ver archivo</a>
                    </div>
                @else
                    <div class="stat-change">Sin archivo</div>
                @endif
            </div>

            <div class="stat-card">
                <div class="stat-top">
                    <span class="stat-name">IDENTIFICACIÓN / CURP</span>
                    <div class="stat-icon">🪪</div>
                </div>
                @if ($alumno->identificacion_path)
                    <div class="stat-value" style="font-size:14px;">
                        <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($alumno->identificacion_path) }}" target="_blank">Ver archivo</a>
                    </div>
                @else
                    <div class="stat-change">Sin archivo</div>
                @endif
            </div>

            <div class="stat-card">
                <div class="stat-top">
                    <span class="stat-name">FOTO DEL ALUMNO</span>
                    <div class="stat-icon">🖼</div>
                </div>
                @if ($alumno->foto_path)
                    <div class="stat-value" style="font-size:14px;">
                        <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($alumno->foto_path) }}" target="_blank">Ver archivo</a>
                    </div>
                @else
                    <div class="stat-change">Sin archivo</div>
                @endif
            </div>

            <div class="stat-card">
                <div class="stat-top">
                    <span class="stat-name">CONTRATO FIRMADO</span>
                    <div class="stat-icon">✍️</div>
                </div>
                @if ($alumno->contrato_firmado_path)
                    <div class="stat-value" style="font-size:14px;">
                        <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($alumno->contrato_firmado_path) }}" target="_blank">Ver archivo</a>
                    </div>
                @else
                    <div class="stat-change">Sin archivo</div>
                @endif
                <div style="margin-top:10px;">
                    <a href="{{ route('alumnos.contrato.create', $alumno) }}" class="btn btn-outline btn-sm">
                        {{ $alumno->contrato_firmado_path ? 'Volver a firmar en el sistema' : 'Firmar contrato en el sistema' }}
                    </a>
                </div>
            </div>

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

                    @if ($inscripcion->horario)
                        <form method="POST" action="{{ route('horarios.cambiar-instructor', $inscripcion->horario) }}" style="display:flex; gap:6px; margin-top:8px;">
                            @csrf
                            @method('PATCH')
                            <select name="instructor_id" class="form-select" style="flex:1; font-size:12px;" required>
                                @foreach ($instructoresDisponibles as $instructorOpcion)
                                    <option value="{{ $instructorOpcion->id }}" @selected($instructorOpcion->id === $inscripcion->horario->instructor_id)>
                                        {{ $instructorOpcion->user?->name }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-outline btn-sm">Cambiar instructor</button>
                        </form>
                    @endif
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

    </div>


    <div class="section-header">
        <h3>Próximas clases</h3>
    </div>

    <div class="panel">
        @if ($proximasCitas->isEmpty())
            <div class="empty-state">Este alumno no tiene clases próximas agendadas.</div>
        @else
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Grupo / Horario actual</th>
                            <th>Reagendar a</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($proximasCitas as $cita)
                            <tr>
                                <td>{{ $cita->fecha->translatedFormat('d M Y') }}</td>
                                <td>
                                    {{ $cita->horario?->nombre_grupo }}
                                    <div style="color:var(--muted); font-size:12px;">
                                        {{ substr($cita->hora_inicio, 0, 5) }}–{{ substr($cita->hora_fin, 0, 5) }}
                                    </div>
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('citas.reagendar', $cita) }}" style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                                        @csrf
                                        @method('PATCH')
                                        <select name="horario_id" class="form-select" style="font-size:12px;" required>
                                            @foreach ($horariosDisponibles as $horarioOpcion)
                                                <option value="{{ $horarioOpcion->id }}" @selected($horarioOpcion->id === $cita->horario_id)>
                                                    {{ $horarioOpcion->nombre_grupo }} · {{ $horarioOpcion->dia_semana->label() }} {{ substr($horarioOpcion->hora_inicio, 0, 5) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="date" name="fecha" class="form-input" style="font-size:12px; width:150px;" value="{{ $cita->fecha->toDateString() }}" required>
                                        <button type="submit" class="btn btn-outline btn-sm">Reagendar</button>
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
        <h3>Acciones</h3>
    </div>

    <div class="stats-grid">

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

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    if (typeof QRCode === 'undefined') {
        document.getElementById('qrCodeAlumnoError').style.display = 'block';
    } else {
        new QRCode(document.getElementById('qrCodeAlumno'), {
            text: @json($alumno->qrUrl()),
            width: 160,
            height: 160,
            colorDark: '#022536',
            colorLight: '#ffffff',
        });
    }

    function descargarQrAlumno() {
        const img = document.querySelector('#qrCodeAlumno img');
        if (! img) return;

        const enlace = document.createElement('a');
        enlace.href = img.src;
        enlace.download = 'qr-{{ Str::slug($alumno->nombreCompleto()) }}.png';
        enlace.click();
    }
</script>
@endpush
