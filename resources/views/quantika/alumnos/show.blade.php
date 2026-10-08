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
            <div class="stat-value" style="font-size:20px;">{{ $alumno->nombreNivelConSubNivel() ?? 'Sin asignar' }}</div>
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
                {{ $ultimaEvaluacion ? 'Última evaluación: '.$ultimaEvaluacion->fecha->format('d/m/Y') : 'Sin evaluaciones aún' }}
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

    @if ($documentosPendientes->isNotEmpty() || $pagosVencidos->isNotEmpty())
        <div class="panel" style="margin-top:20px;border-color:rgba(255,107,107,.4);">
            <strong style="color:#ff9aa0;">Pendientes importantes</strong>
            @if ($documentosPendientes->isNotEmpty())
                <div style="margin-top:8px;">
                    Documentos pendientes: {{ $documentosPendientes->join(', ') }}.
                </div>
            @endif
            @if ($pagosVencidos->isNotEmpty())
                <div style="margin-top:8px;">
                    Adeudos vencidos: {{ $pagosVencidos->count() }} pago(s), por
                    ${{ number_format((float) $pagosVencidos->sum('monto'), 2) }}.
                    <a href="{{ route('pagos.alumno', $alumno) }}" class="section-link">Ver pagos</a>
                </div>
            @endif
        </div>
    @endif


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

        <div class="section-header">
            <h3>Información médica y emergencia</h3>
        </div>
        <div class="panel">
            <div class="stats-grid">
                <div class="stat-card">
                    <span class="stat-name">TIPO DE SANGRE</span>
                    <div class="stat-value" style="font-size:18px;">{{ $alumno->tipo_sangre ?? 'No registrado' }}</div>
                </div>
                <div class="stat-card">
                    <span class="stat-name">CONTACTO DE EMERGENCIA</span>
                    <div class="stat-value" style="font-size:16px;">{{ $alumno->contacto_emergencia_nombre ?? 'No registrado' }}</div>
                    <div class="stat-change">{{ $alumno->contacto_emergencia_telefono ?? 'Sin teléfono' }}</div>
                </div>
                <div class="stat-card">
                    <span class="stat-name">OBSERVACIONES MÉDICAS</span>
                    <div class="stat-change" style="margin-top:8px;">{{ $alumno->observaciones_medicas ?? 'Sin observaciones' }}</div>
                </div>
            </div>
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
        <div id="qrCodeAlumno" class="qr-marco"></div>
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
        <h3>Clase extra</h3>
    </div>

    <div class="panel">
        <p style="color:var(--muted); font-size:13px; line-height:1.6; margin-bottom:16px;">
            Agenda una clase adicional pagada por separado. Es solo para la fecha que elijas: no cambia el plan ni los
            grupos de {{ $alumno->nombreCompleto() }}, y la semana siguiente ya no aparece en ese grupo.
        </p>

        @if ($alumno->estado->value !== 'activo')
            <div class="empty-state">Solo se pueden agendar clases extra a alumnos activos.</div>
        @elseif ($horariosDisponibles->isEmpty())
            <div class="empty-state">No hay grupos activos en esta sucursal.</div>
        @else
            <form method="POST" action="{{ route('alumnos.clase-extra.store', $alumno) }}">
                @csrf
                <div class="form-grid">
                    <div class="form-group">
                        <label>Grupo</label>
                        <select name="horario_id" id="claseExtraHorario" class="form-select" required>
                            <option value="">Selecciona un grupo</option>
                            @foreach ($horariosDisponibles as $horarioOpcion)
                                <option value="{{ $horarioOpcion->id }}" data-dia="{{ $horarioOpcion->dia_semana->value }}" @selected((int) old('horario_id') === $horarioOpcion->id)>
                                    {{ $horarioOpcion->nombre_grupo }} · {{ $horarioOpcion->dia_semana->label() }} {{ substr($horarioOpcion->hora_inicio, 0, 5) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Fecha de la clase</label>
                        <input type="date" name="fecha" id="claseExtraFecha" class="form-input" min="{{ today()->toDateString() }}" value="{{ old('fecha') }}" required>
                        <span class="form-hint">Al elegir el grupo se propone su próxima fecha.</span>
                    </div>
                    <div class="form-group">
                        <label>Monto cobrado (opcional)</label>
                        <input type="number" name="monto" class="form-input" min="0" step="0.01" value="{{ old('monto') }}" placeholder="0.00">
                        <span class="form-hint">Si lo capturas, se registra como pago recibido hoy.</span>
                    </div>
                    <div class="form-group">
                        <label>Método de pago</label>
                        <select name="metodo_pago" class="form-select">
                            @foreach (\App\Enums\MetodoPago::cases() as $metodo)
                                <option value="{{ $metodo->value }}" @selected(old('metodo_pago') === $metodo->value)>{{ $metodo->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Agendar clase extra</button>
            </form>
        @endif
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
                    <span class="stat-name">INE DEL TUTOR</span>
                    <div class="stat-icon">🪪</div>
                </div>
                @if ($alumno->ine_tutor_path || $alumno->ine_tutor_path_2)
                    <div class="stat-value" style="font-size:14px;">
                        @if ($alumno->ine_tutor_path)
                            <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($alumno->ine_tutor_path) }}" target="_blank">Frente</a>
                        @endif
                        @if ($alumno->ine_tutor_path_2)
                            · <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($alumno->ine_tutor_path_2) }}" target="_blank">Reverso</a>
                        @endif
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
                                    @if ($cita->reposicion_de_id)
                                        <div style="color:var(--cyan); font-size:11px; font-weight:800;">RECUPERACIÓN DE FALTA</div>
                                    @endif
                                    @if ($cita->es_extra)
                                        <div style="color:var(--cyan); font-size:11px; font-weight:800;">CLASE EXTRA</div>
                                    @endif
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
                                    @if ($cita->es_extra)
                                        <form method="POST" action="{{ route('citas.clase-extra.destroy', $cita) }}" style="margin-top:8px;" onsubmit="return confirm('¿Cancelar esta clase extra?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline btn-sm">Cancelar clase extra</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>


    <div class="section-header">
        <h3>Historial de asistencia</h3>
        <span style="color:var(--muted); font-size:12px;">
            {{ $citasAsistidas }} asistencia(s) · {{ $citasCompletadas - $citasAsistidas }} falta(s) en total
        </span>
    </div>

    <div class="panel">
        @if ($historialAsistencia->isEmpty())
            <div class="empty-state">Todavía no hay asistencias ni faltas registradas para este alumno.</div>
        @else
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Grupo</th>
                            <th>Resultado</th>
                            <th>Notas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($historialAsistencia as $cita)
                            <tr>
                                <td>{{ $cita->fecha->translatedFormat('D d M Y') }}</td>
                                <td>
                                    {{ $cita->horario?->nombre_grupo ?? '—' }}
                                    <div style="color:var(--muted); font-size:12px;">
                                        {{ substr($cita->hora_inicio, 0, 5) }}–{{ substr($cita->hora_fin, 0, 5) }}
                                        @if ($cita->es_extra) · Clase extra @endif
                                        @if ($cita->reposicion_de_id) · Reposición @endif
                                    </div>
                                </td>
                                <td>
                                    @if ($cita->asistio)
                                        <span class="badge badge-green">● Asistió</span>
                                    @else
                                        <span class="badge badge-red">● Falta</span>
                                        @if ($cita->reposicion)
                                            <div style="color:var(--muted); font-size:11px; margin-top:4px;">Repuesta el {{ $cita->reposicion->fecha->format('d/m/Y') }}</div>
                                        @endif
                                    @endif
                                </td>
                                <td>{{ $cita->notas ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($citasCompletadas > $historialAsistencia->count())
                <p style="color:var(--muted); font-size:12px; margin-top:12px;">Se muestran las {{ $historialAsistencia->count() }} clases más recientes.</p>
            @endif
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

    <div class="section-header" style="margin-top:30px;">
        <h3>Zona de desactivación</h3>
    </div>
    <div class="panel" style="display:flex; justify-content:flex-end; gap:10px;">
        <form action="{{ route('alumnos.destroy', $alumno) }}" method="POST"
              onsubmit="return confirm('¿Eliminar permanentemente a {{ $alumno->nombreCompleto() }}? Esta acción no se puede deshacer.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline btn-sm" style="color:#ff6b6b; border-color:#ff6b6b;">🗑 Eliminar permanentemente</button>
        </form>
    </div>

@endsection

@push('scripts')
<style>
    .qr-marco { width: 220px; padding: 16px; background: #ffffff; border-radius: 14px; line-height: 0; }
    .qr-marco img, .qr-marco canvas { width: 100%; height: auto; }
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    if (typeof QRCode === 'undefined') {
        document.getElementById('qrCodeAlumnoError').style.display = 'block';
    } else {
        // Se dibuja en alta resolución, en negro puro y con corrección de
        // errores media: el código queda con menos módulos y más grandes.
        new QRCode(document.getElementById('qrCodeAlumno'), {
            text: @json($alumno->qrUrl()),
            width: 640,
            height: 640,
            colorDark: '#000000',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.M,
        });
    }

    // La imagen descargada lleva margen blanco alrededor: sin esa zona
    // libre los lectores tardan en reconocer el código al imprimirlo.
    function descargarQrAlumno() {
        const origen = document.querySelector('#qrCodeAlumno canvas');
        if (! origen) return;

        const margen = 80;
        const lienzo = document.createElement('canvas');
        lienzo.width = origen.width + margen * 2;
        lienzo.height = origen.height + margen * 2;

        const ctx = lienzo.getContext('2d');
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, lienzo.width, lienzo.height);
        ctx.drawImage(origen, margen, margen);

        const enlace = document.createElement('a');
        enlace.href = lienzo.toDataURL('image/png');
        enlace.download = 'qr-{{ Str::slug($alumno->nombreCompleto()) }}.png';
        enlace.click();
    }

    // Clase extra: al elegir el grupo se propone su próxima fecha.
    (function () {
        const grupo = document.getElementById('claseExtraHorario');
        const fecha = document.getElementById('claseExtraFecha');
        if (! grupo || ! fecha) return;

        grupo.addEventListener('change', function () {
            const dia = parseInt(grupo.selectedOptions[0]?.dataset.dia || '', 10);
            if (! dia) return;

            const proxima = new Date();
            const hoy = proxima.getDay() === 0 ? 7 : proxima.getDay();
            proxima.setDate(proxima.getDate() + ((dia - hoy + 7) % 7));

            const dosDigitos = (n) => String(n).padStart(2, '0');
            fecha.value = proxima.getFullYear() + '-' + dosDigitos(proxima.getMonth() + 1) + '-' + dosDigitos(proxima.getDate());
        });
    })();
</script>
@endpush
