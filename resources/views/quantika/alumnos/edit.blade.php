@extends('quantika.super-admin.layout')

@section('title', 'Editar alumno | QUANTIKA POOL')

@section('page-title', 'Editar alumno')

@section('content')

    <div class="section-header">
        <div>
            <h3>Editar alumno</h3>
            <p style="color:var(--muted); font-size:13px; margin-top:6px;">
                Actualiza los datos de {{ $alumno->nombreCompleto() }}.
            </p>
        </div>
    </div>

    <div class="panel form-card">

        <h3 style="margin-bottom:25px;">Información del alumno</h3>

        <form method="POST" action="{{ route('alumnos.update', $alumno) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-grid">

                <div class="form-group">
                    <label>Nombre</label>
                    <input type="text" class="form-input" name="nombre" value="{{ old('nombre', $alumno->nombre) }}" required>
                </div>

                <div class="form-group">
                    <label>Apellidos</label>
                    <input type="text" class="form-input" name="apellidos" value="{{ old('apellidos', $alumno->apellidos) }}" required>
                </div>

                <div class="form-group">
                    <label>Fecha de nacimiento</label>
                    <input type="date" class="form-input" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', optional($alumno->fecha_nacimiento)->format('Y-m-d')) }}" required>
                </div>

                <div class="form-group">
                    <label>Teléfono</label>
                    <input type="text" class="form-input" name="telefono" value="{{ old('telefono', $alumno->telefono) }}">
                </div>

                <div class="form-group">
                    <label>Correo electrónico del alumno</label>
                    <input type="email" class="form-input" name="email" value="{{ old('email', $alumno->email) }}">
                </div>

                <div class="form-group">
                    <label>Estado</label>
                    <select name="estado" class="form-select" required>
                        @foreach ($estados as $estadoOpcion)
                            <option value="{{ $estadoOpcion->value }}" @selected(old('estado', $alumno->estado->value) === $estadoOpcion->value)>
                                {{ $estadoOpcion->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Nivel actual</label>
                    <select name="nivel_id" class="form-select">
                        <option value="">Sin nivel asignado</option>
                        @foreach ($niveles->groupBy('categoria_edad') as $grupoEdad => $nivelesGrupo)
                            <optgroup label="{{ $grupoEdad }}">
                                @foreach ($nivelesGrupo as $nivelOpcion)
                                    <option value="{{ $nivelOpcion->id }}" @selected(old('nivel_id', $alumno->nivel_id) == $nivelOpcion->id)>
                                        {{ $nivelOpcion->nombre }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Plan de mensualidad</label>
                    <select name="plan_id" class="form-select">
                        <option value="">Sin plan asignado</option>
                        @foreach ($planes as $planOpcion)
                            <option value="{{ $planOpcion->id }}" @selected(old('plan_id', $alumno->plan_id) == $planOpcion->id)>
                                {{ $planOpcion->nombre }} ({{ $planOpcion->clases_por_semana }} clases/semana)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group full">
                    <label class="form-check">
                        <input type="checkbox" id="tieneTutorEditar" name="tiene_tutor" value="1" onchange="toggleTutorEditar()" @checked(old('tiene_tutor', $alumno->nombreTutor() !== null))>
                        El alumno tiene tutor / responsable
                    </label>
                </div>

                <div id="camposTutorEditar">
                    <div class="form-group">
                        <label>Nombre del tutor / responsable</label>
                        <input type="text" class="form-input" name="tutor_nombre" value="{{ old('tutor_nombre', $alumno->nombreTutor()) }}">
                    </div>

                    <div class="form-group">
                        <label>Correo del tutor / responsable (opcional)</label>
                        <input type="email" class="form-input" name="tutor_email" value="{{ old('tutor_email', $alumno->tutorUser?->email) }}">
                        <small>Si no se captura, el tutor solo queda como dato de contacto (sin acceso al portal).</small>
                    </div>

                    <div class="form-group">
                        <label>Teléfono del tutor</label>
                        <input type="text" class="form-input" name="tutor_telefono" value="{{ old('tutor_telefono', $alumno->telefonoTutor()) }}">
                    </div>
                </div>

                <div class="form-group full">
                    <label>Observaciones</label>
                    <input type="text" class="form-input" name="observaciones" value="{{ old('observaciones', $alumno->observaciones) }}">
                </div>

                <div class="form-group">
                    <label>Certificado médico {{ $alumno->certificado_medico_path ? '(reemplazar)' : '(opcional)' }}</label>
                    <input type="file" class="form-input" name="certificado_medico" accept=".pdf,.jpg,.jpeg,.png">
                    @if ($alumno->certificado_medico_path)
                        <span class="form-hint"><a href="{{ \Illuminate\Support\Facades\Storage::url($alumno->certificado_medico_path) }}" target="_blank">Ver archivo actual</a></span>
                    @endif
                </div>

                <div class="form-group">
                    <label>Identificación / CURP {{ $alumno->identificacion_path ? '(reemplazar)' : '(opcional)' }}</label>
                    <input type="file" class="form-input" name="identificacion" accept=".pdf,.jpg,.jpeg,.png">
                    @if ($alumno->identificacion_path)
                        <span class="form-hint"><a href="{{ \Illuminate\Support\Facades\Storage::url($alumno->identificacion_path) }}" target="_blank">Ver archivo actual</a></span>
                    @endif
                </div>

                <div class="form-group">
                    <label>Foto del alumno {{ $alumno->foto_path ? '(reemplazar)' : '(opcional)' }}</label>
                    <input type="file" class="form-input" name="foto" accept=".jpg,.jpeg,.png">
                    @if ($alumno->foto_path)
                        <span class="form-hint"><a href="{{ \Illuminate\Support\Facades\Storage::url($alumno->foto_path) }}" target="_blank">Ver archivo actual</a></span>
                    @endif
                </div>

                <div class="form-group">
                    <label>Contrato firmado {{ $alumno->contrato_firmado_path ? '(reemplazar)' : '(opcional)' }}</label>
                    <input type="file" class="form-input" name="contrato_firmado" accept=".pdf,.jpg,.jpeg,.png">
                    @if ($alumno->contrato_firmado_path)
                        <span class="form-hint"><a href="{{ \Illuminate\Support\Facades\Storage::url($alumno->contrato_firmado_path) }}" target="_blank">Ver archivo actual</a></span>
                    @endif
                </div>

            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
                <a href="{{ route('alumnos.show', $alumno) }}" class="btn btn-outline">Cancelar</a>
            </div>

        </form>

    </div>

@endsection

@push('scripts')
<script>
    function toggleTutorEditar() {
        const visible = document.getElementById('tieneTutorEditar').checked;
        document.getElementById('camposTutorEditar').style.display = visible ? '' : 'none';
    }

    document.addEventListener('DOMContentLoaded', toggleTutorEditar);
</script>
@endpush
