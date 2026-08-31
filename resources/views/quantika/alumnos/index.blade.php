@extends('quantika.super-admin.layout')

@section('title', 'Alumnos')
@section('page-title', 'Alumnos')

@push('styles')
<style>
    /* =====================================================
       HEADER DE CONTENIDO
    ===================================================== */

    .content-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 26px;
        flex-wrap: wrap;
    }

    .content-header h2 {
        font-size: 31px;
        font-weight: 900;
        margin-bottom: 7px;
    }

    .content-header p {
        color: var(--muted);
        font-size: 14px;
    }

    /* =====================================================
       SUMMARY CARDS
    ===================================================== */

    .summary {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 25px;
    }

    .summary-card {
        min-height: 120px;
        padding: 20px;
        border-radius: 21px;
        border: 1px solid var(--border);
        background: linear-gradient(145deg, #07364a, #052b3c);
        position: relative;
        overflow: hidden;
    }

    .summary-card::after {
        content: "";
        width: 85px;
        height: 85px;
        border-radius: 50%;
        position: absolute;
        right: -28px;
        bottom: -38px;
        background: rgba(66, 213, 237, .07);
    }

    .summary-label {
        color: #76a7ba;
        font-size: 13px;
        font-weight: 700;
    }

    .summary-value {
        font-size: 30px;
        font-weight: 900;
        margin-top: 12px;
    }

    .summary-extra {
        color: var(--green);
        font-size: 12px;
        font-weight: 800;
        margin-top: 4px;
    }

    /* =====================================================
       TOOLBAR
    ===================================================== */

    .toolbar {
        padding: 18px;
        border-radius: 20px;
        border: 1px solid var(--border);
        background: rgba(5, 43, 59, .78);
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 18px;
        flex-wrap: wrap;
    }

    .search {
        flex: 1;
        min-width: 250px;
        height: 47px;
        position: relative;
    }

    .search input {
        width: 100%;
        height: 100%;
        padding: 0 17px 0 45px;
        border-radius: 14px;
        border: 1px solid var(--border);
        outline: none;
        background: #062c3d;
        color: white;
        font-size: 14px;
    }

    .search span {
        position: absolute;
        left: 16px;
        top: 12px;
        color: #6f9aab;
    }

    .filter {
        height: 47px;
        padding: 0 15px;
        border-radius: 14px;
        border: 1px solid var(--border);
        background: #062c3d;
        color: white;
        outline: none;
        min-width: 145px;
        cursor: pointer;
    }

    /* =====================================================
       TABLE
    ===================================================== */

    .table-card {
        border-radius: 23px;
        border: 1px solid var(--border);
        background: linear-gradient(145deg, rgba(7, 54, 74, .92), rgba(4, 40, 55, .92));
        overflow: hidden;
    }

    .table-head {
        padding: 20px 23px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid var(--border);
        flex-wrap: wrap;
        gap: 10px;
    }

    .table-head h3 {
        font-size: 17px;
        font-weight: 900;
    }

    .table-head span {
        color: #6d9caf;
        font-size: 12px;
    }

    .table-wrapper {
        overflow-x: auto;
    }

    .table-wrapper table {
        width: 100%;
        border-collapse: collapse;
        min-width: 950px;
    }

    .table-wrapper th {
        text-align: left;
        padding: 15px 20px;
        color: #6d9caf;
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        background: rgba(1, 26, 39, .32);
    }

    .table-wrapper td {
        padding: 16px 20px;
        border-top: 1px solid rgba(71, 208, 235, .08);
        font-size: 13px;
        color: #d8edf5;
    }

    .table-wrapper tbody tr {
        transition: .2s;
    }

    .table-wrapper tbody tr:hover {
        background: rgba(66, 213, 237, .035);
    }

    /* ALUMNO */

    .student {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .student-avatar {
        width: 43px;
        height: 43px;
        border-radius: 13px;
        background: linear-gradient(135deg, #49d8ed, #167f9c);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #033044;
        font-weight: 900;
        flex-shrink: 0;
    }

    .student-name {
        font-weight: 850;
        color: white;
    }

    .student-email {
        color: #7198a9;
        font-size: 11px;
        margin-top: 3px;
    }

    /* LEVEL */

    .level {
        display: flex;
        align-items: center;
        gap: 9px;
    }

    .level-animal {
        width: 39px;
        height: 39px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(66, 213, 237, .35);
        background: #062b3c;
        overflow: hidden;
    }

    .level-animal img {
        width: 27px;
        height: 27px;
        object-fit: contain;
    }

    .level-name {
        font-weight: 800;
        color: white;
    }

    .level-number {
        font-size: 10px;
        color: #7198a9;
        margin-top: 2px;
    }

    /* BRANCH */

    .branch {
        color: #a6c5d2;
        font-weight: 700;
    }

    /* PROGRESS */

    .progress {
        width: 110px;
    }

    .progress-top {
        display: flex;
        justify-content: space-between;
        font-size: 10px;
        margin-bottom: 5px;
    }

    .progress-top strong {
        color: white;
    }

    .progress-bar {
        height: 5px;
        background: #123f50;
        border-radius: 20px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        background: var(--cyan);
        border-radius: 20px;
    }

    /* ESTADO (evita chocar con .status del hero del layout) */

    .alumno-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 10px;
        border-radius: 30px;
        font-size: 10px;
        font-weight: 900;
    }

    .alumno-status::before {
        content: "";
        width: 6px;
        height: 6px;
        border-radius: 50%;
    }

    .alumno-status.is-active {
        color: var(--green);
        border: 1px solid rgba(19, 227, 162, .28);
        background: rgba(19, 227, 162, .08);
    }

    .alumno-status.is-active::before {
        background: var(--green);
    }

    .alumno-status.is-inactive {
        color: #ff7c85;
        border: 1px solid rgba(255, 95, 109, .28);
        background: rgba(255, 95, 109, .08);
    }

    .alumno-status.is-inactive::before {
        background: var(--red);
    }

    /* ACTIONS */

    .actions {
        display: flex;
        gap: 6px;
    }

    .action-btn {
        width: 35px;
        height: 35px;
        border-radius: 11px;
        border: 1px solid var(--border);
        background: #092f41;
        color: #91b6c4;
        cursor: pointer;
        transition: .2s;
    }

    .action-btn:hover {
        background: var(--cyan);
        color: #033044;
    }

    /* EMPTY */

    .empty {
        text-align: center;
        padding: 60px;
        color: #7096a6;
        display: none;
    }

    /* =====================================================
       MODAL
    ===================================================== */

    .modal {
        position: fixed;
        inset: 0;
        background: rgba(0, 12, 19, .72);
        backdrop-filter: blur(8px);
        z-index: 100;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .modal.show {
        display: flex;
    }

    .modal-box {
        width: 100%;
        max-width: 760px;
        max-height: 90vh;
        overflow-y: auto;
        background: linear-gradient(145deg, #07374b, #042737);
        border: 1px solid var(--border);
        border-radius: 25px;
        box-shadow: 0 30px 90px rgba(0,0,0,.45);
    }

    .modal-header {
        padding: 22px 25px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .modal-header h3 {
        font-size: 20px;
    }

    .close {
        width: 38px;
        height: 38px;
        border: 1px solid var(--border);
        background: #082f41;
        color: white;
        border-radius: 12px;
        cursor: pointer;
        font-size: 20px;
    }

    .modal-footer {
        padding: 18px 25px;
        border-top: 1px solid var(--border);
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .btn-secondary {
        padding: 13px 20px;
        border-radius: 13px;
        border: 1px solid var(--border);
        background: transparent;
        color: #9bb9c6;
        font-weight: 800;
        cursor: pointer;
    }

    /* =====================================================
       RESPONSIVE
    ===================================================== */

    @media (max-width: 1100px) {
        .summary { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 850px) {
        .content-header { align-items: flex-start; flex-direction: column; }
    }

    @media (max-width: 600px) {
        .summary { grid-template-columns: 1fr; }
        .toolbar { align-items: stretch; }
        .search { min-width: 100%; }
        .filter { flex: 1; }
    }
</style>
@endpush

@section('content')

    <!-- HEADER -->
    <div class="content-header">
        <div>
            <h2>Gestión de alumnos</h2>
            <p>Registra, consulta y administra los alumnos de Quantika Pool.</p>
        </div>

        <button class="btn btn-primary" onclick="abrirModal()">
            + Registrar alumno
        </button>
    </div>


    <!-- SUMMARY -->
    <div class="summary">

        <div class="summary-card">
            <div class="summary-label">Alumnos registrados</div>
            <div class="summary-value">{{ $totalRegistrados }}</div>
            <div class="summary-extra">En esta vista</div>
        </div>

        <div class="summary-card">
            <div class="summary-label">Alumnos activos</div>
            <div class="summary-value">{{ $totalActivos }}</div>
            <div class="summary-extra">{{ $totalRegistrados > 0 ? round(($totalActivos / $totalRegistrados) * 100, 1) : 0 }}% del total</div>
        </div>

        <div class="summary-card">
            <div class="summary-label">Principiantes</div>
            <div class="summary-value">{{ $totalPrincipiantes }}</div>
            <div class="summary-extra">Nivel inicial</div>
        </div>

        <div class="summary-card">
            <div class="summary-label">Avanzados</div>
            <div class="summary-value">{{ $totalAvanzados }}</div>
            <div class="summary-extra">Dominio avanzado</div>
        </div>

    </div>


    <!-- TOOLBAR (filtrado en tiempo real, sin recargar la página) -->
    <div class="toolbar">

        <div class="search">
            <span>⌕</span>
            <input
                type="text"
                id="buscar"
                oninput="filtrarAlumnos()"
                placeholder="Buscar alumno por nombre, correo o tutor...">
        </div>

        <select class="filter" id="filtroNivel" onchange="filtrarAlumnos()">
            <option value="">Todos los niveles</option>
            @foreach ($niveles as $nivelOpcion)
                <option value="{{ $nivelOpcion->id }}">
                    {{ $nivelOpcion->nombre }}
                </option>
            @endforeach
        </select>

        <select class="filter" id="filtroEstado" onchange="filtrarAlumnos()">
            <option value="">Todos los estados</option>
            @foreach (\App\Enums\EstadoAlumno::cases() as $estadoOpcion)
                <option value="{{ $estadoOpcion->value }}">
                    {{ $estadoOpcion->label() }}
                </option>
            @endforeach
        </select>

        <button type="button" class="btn btn-primary" style="padding:0 22px;" onclick="limpiarFiltrosAlumnos()">Limpiar filtros</button>

    </div>


    <!-- TABLE -->
    <div class="table-card">

        <div class="table-head">
            <h3>Alumnos registrados</h3>
            <span>Mostrando <strong id="contador">{{ $alumnos->count() }}</strong> alumnos</span>
        </div>

        <div class="table-wrapper">
            <table>

                <thead>
                    <tr>
                        <th>Alumno</th>
                        <th>Nivel actual</th>
                        <th>Sucursal</th>
                        <th>Tutor / Responsable</th>
                        <th>Asistencia</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody id="tablaAlumnos">
                    @forelse ($alumnos as $fila)
                        @php($alumno = $fila['alumno'])
                        <tr
                            data-search="{{ mb_strtolower($alumno->nombreCompleto().' '.($alumno->email ?? '').' '.($alumno->tutorUser->name ?? '')) }}"
                            data-nivel="{{ $alumno->nivel_id }}"
                            data-sucursal="{{ $alumno->sucursal_id }}"
                            data-estado="{{ $alumno->estado->value }}">

                            <td>
                                <div class="student">
                                    <div class="student-avatar">{{ $fila['iniciales'] }}</div>
                                    <div>
                                        <div class="student-name">{{ $alumno->nombreCompleto() }}</div>
                                        <div class="student-email">{{ $alumno->email ?? ($alumno->tutorUser->email ?? 'Sin correo') }}</div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                @if ($alumno->nivel)
                                    <div class="level">
                                        <div class="level-animal">
                                            <img src="{{ asset($alumno->nivel->imagen) }}" alt="{{ $alumno->nivel->nombre }}">
                                        </div>
                                        <div>
                                            <div class="level-name">{{ $alumno->nivel->nombre }}</div>
                                            <div class="level-number">Nivel {{ str_pad((string) $alumno->nivel->orden, 2, '0', STR_PAD_LEFT) }}</div>
                                        </div>
                                    </div>
                                @else
                                    <span style="color:var(--muted);font-size:12px;">Sin nivel asignado</span>
                                @endif
                            </td>

                            <td><span class="branch">{{ $alumno->sucursal->nombre }}</span></td>

                            <td>{{ $alumno->tutorUser->name ?? 'Sin tutor' }}</td>

                            <td>
                                @if ($fila['asistencia'] !== null)
                                    <div class="progress">
                                        <div class="progress-top">
                                            <span>Asistencia</span>
                                            <strong>{{ $fila['asistencia'] }}%</strong>
                                        </div>
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width:{{ $fila['asistencia'] }}%"></div>
                                        </div>
                                    </div>
                                @else
                                    <span style="color:var(--muted);font-size:12px;">Sin registros</span>
                                @endif
                            </td>

                            <td>
                                <span class="alumno-status {{ $alumno->estado->value === 'activo' ? 'is-active' : 'is-inactive' }}">
                                    {{ $alumno->estado->label() }}
                                </span>
                            </td>

                            <td>
                                <div class="actions">
                                    <a class="action-btn" title="Ver" href="{{ route('alumnos.show', $alumno) }}">◉</a>
                                    <a class="action-btn" title="Editar" href="{{ route('alumnos.edit', $alumno) }}">✎</a>

                                    @if ($alumno->estado->value === 'activo')
                                        <form
                                            action="{{ route('alumnos.baja', $alumno) }}"
                                            method="POST"
                                            style="display:inline;"
                                            onsubmit="return confirm('¿Deseas dar de baja a {{ $alumno->nombreCompleto() }}?');">
                                            @csrf
                                            @method('PATCH')
                                            <button class="action-btn" type="submit" title="Dar de baja">⋯</button>
                                        </form>
                                    @else
                                        <form action="{{ route('alumnos.reactivar', $alumno) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button class="action-btn" type="submit" title="Reactivar">↺</button>
                                        </form>
                                    @endif
                                </div>
                            </td>

                        </tr>
                    @empty
                    @endforelse
                </tbody>

            </table>

            <div class="empty" id="sinResultados" style="display:{{ $alumnos->isEmpty() ? 'block' : 'none' }};">
                No se encontraron alumnos con esos criterios.
            </div>
        </div>

    </div>


    <!-- MODAL REGISTRAR -->
    <div class="modal" id="modalAlumno">

        <div class="modal-box">

            <div class="modal-header">
                <h3>Registrar nuevo alumno</h3>
                <button class="close" onclick="cerrarModal()">×</button>
            </div>

            @if ($errors->any())
                <div style="margin:0 30px 10px;padding:12px 16px;border-radius:12px;background:rgba(255,95,109,.10);border:1px solid rgba(255,95,109,.35);color:#ff9aa1;font-size:12px;">
                    <strong>Revisa lo siguiente:</strong>
                    <ul style="margin:6px 0 0 18px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="formAlumnoCrear" class="form-grid" style="padding:25px;" method="POST" action="{{ route('alumnos.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label>Nombre</label>
                    <input type="text" class="form-input" name="nombre" value="{{ old('nombre') }}" required placeholder="Nombre del alumno">
                </div>

                <div class="form-group">
                    <label>Apellidos</label>
                    <input type="text" class="form-input" name="apellidos" value="{{ old('apellidos') }}" required placeholder="Apellidos">
                </div>

                <div class="form-group">
                    <label>Fecha de nacimiento</label>
                    <input type="date" class="form-input" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}" required>
                </div>

                <div class="form-group">
                    <label>Teléfono</label>
                    <input type="tel" class="form-input" name="telefono" value="{{ old('telefono') }}" placeholder="10 dígitos">
                </div>

                <div class="form-group">
                    <label>Correo electrónico del alumno</label>
                    <input type="email" class="form-input" name="email" value="{{ old('email') }}" placeholder="correo@ejemplo.com">
                </div>

                <div class="form-group">
                    <label>Nombre del tutor / responsable</label>
                    <input type="text" class="form-input" name="tutor_nombre" value="{{ old('tutor_nombre') }}" required placeholder="Nombre del tutor">
                </div>

                <div class="form-group">
                    <label>Correo del tutor / responsable</label>
                    <input type="email" class="form-input" name="tutor_email" value="{{ old('tutor_email') }}" required placeholder="tutor@ejemplo.com">
                </div>

                <div class="form-group">
                    <label>Teléfono del tutor</label>
                    <input type="tel" class="form-input" name="tutor_telefono" value="{{ old('tutor_telefono') }}" placeholder="10 dígitos">
                </div>

                @if ($esVistaGlobal)
                    <div class="form-group">
                        <label>Sucursal</label>
                        <select class="form-select" name="sucursal_id" required>
                            <option value="">Seleccionar sucursal</option>
                            @foreach ($sucursales as $sucursalOpcion)
                                <option value="{{ $sucursalOpcion->id }}" @selected(old('sucursal_id') == $sucursalOpcion->id)>
                                    {{ $sucursalOpcion->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <input type="hidden" name="sucursal_id" value="{{ $sucursales->first()->id }}">
                @endif

                <div class="form-group">
                    <label>Nivel actual</label>
                    <select class="form-select" name="nivel_id">
                        <option value="">Sin nivel asignado</option>
                        @foreach ($niveles as $nivelOpcion)
                            <option value="{{ $nivelOpcion->id }}" @selected(old('nivel_id') == $nivelOpcion->id)>
                                {{ $nivelOpcion->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Plan de mensualidad</label>
                    <select class="form-select" name="plan_id">
                        <option value="">Sin plan asignado</option>
                        @foreach ($planes as $planOpcion)
                            <option value="{{ $planOpcion->id }}" @selected(old('plan_id') == $planOpcion->id)>
                                {{ $planOpcion->nombre }} ({{ $planOpcion->clases_por_semana }} clases/semana)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group full">
                    <label>Observaciones</label>
                    <input type="text" class="form-input" name="observaciones" value="{{ old('observaciones') }}" placeholder="Información adicional del alumno">
                </div>

                <div class="form-group">
                    <label>Certificado médico (opcional)</label>
                    <input type="file" class="form-input" name="certificado_medico" accept=".pdf,.jpg,.jpeg,.png">
                </div>

                <div class="form-group">
                    <label>Identificación / acta de nacimiento (opcional)</label>
                    <input type="file" class="form-input" name="identificacion" accept=".pdf,.jpg,.jpeg,.png">
                </div>

                <div class="form-group">
                    <label>Foto del alumno (opcional)</label>
                    <input type="file" class="form-input" name="foto" accept=".jpg,.jpeg,.png">
                </div>

            </form>

            <div class="modal-footer">
                <button class="btn-secondary" type="button" onclick="cerrarModal()">Cancelar</button>
                <button class="btn btn-primary" type="submit" form="formAlumnoCrear">Registrar alumno</button>
            </div>

        </div>

    </div>

@endsection

@push('scripts')
<script>

    function filtrarAlumnos() {

        const texto = document.getElementById('buscar').value.toLowerCase();
        const nivel = document.getElementById('filtroNivel').value;
        const estado = document.getElementById('filtroEstado').value;

        let visibles = 0;

        document.querySelectorAll('#tablaAlumnos tr').forEach(function (fila) {
            const coincideTexto = !texto || fila.dataset.search.includes(texto);
            const coincideNivel = !nivel || fila.dataset.nivel === nivel;
            const coincideEstado = !estado || fila.dataset.estado === estado;

            const visible = coincideTexto && coincideNivel && coincideEstado;
            fila.style.display = visible ? '' : 'none';

            if (visible) {
                visibles++;
            }
        });

        document.getElementById('contador').textContent = visibles;
        document.getElementById('sinResultados').style.display = visibles === 0 ? 'block' : 'none';
    }

    function limpiarFiltrosAlumnos() {
        document.getElementById('buscar').value = '';
        document.getElementById('filtroNivel').value = '';
        document.getElementById('filtroEstado').value = '';
        filtrarAlumnos();
    }

    function abrirModal() {
        document.getElementById('modalAlumno').classList.add('show');
    }

    function cerrarModal() {
        document.getElementById('modalAlumno').classList.remove('show');
    }

    window.onclick = function(event) {
        const modal = document.getElementById('modalAlumno');
        if (event.target === modal) {
            cerrarModal();
        }
    };

    @if ($abrirModalCrear || $errors->any())
        abrirModal();
    @endif

</script>
@endpush
