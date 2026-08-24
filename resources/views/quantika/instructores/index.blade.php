@extends('quantika.super-admin.layout')

@section('title', 'Instructores')
@section('page-title', 'Instructores')

@push('styles')
<style>

    /* =========================
       ENCABEZADO
    ========================= */

    .header-left p {
        color: #8fb2c4;
        font-size: 16px;
        margin-bottom: 30px;
    }

    /* =========================
       BOTONES
    ========================= */

    .actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 30px;
    }

    .btn-secondary {
        border: 1px solid rgba(66,213,238,.40);
        color: white;
        background: rgba(2,29,43,.25);
        min-height: 48px;
        padding: 0 21px;
        border-radius: 13px;
        font-size: 14px;
        font-weight: 900;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 15px;
        transition: .2s ease;
    }

    .btn-secondary:hover { background: rgba(66,213,238,.10); }

    /* =========================
       ESTADÍSTICAS
    ========================= */

    .stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: linear-gradient(
            145deg,
            #07394e,
            #052d40
        );

        border: 1px solid rgba(69, 207, 234, .18);
        border-radius: 18px;
        padding: 22px;
        position: relative;
        overflow: hidden;
    }

    .stat-card::after {
        content: "";
        position: absolute;
        width: 100px;
        height: 100px;
        right: -45px;
        bottom: -55px;
        background: rgba(39, 205, 235, .08);
        border-radius: 50%;
    }

    .stat-title {
        color: #8db1c3;
        font-size: 14px;
        margin-bottom: 12px;
    }

    .stat-number {
        font-size: 30px;
        font-weight: 800;
    }

    .stat-description {
        color: #40d9c0;
        font-size: 13px;
        margin-top: 5px;
    }

    /* =========================
       PANEL
    ========================= */

    .panel {
        background: rgba(5, 45, 62, .9);
        border: 1px solid rgba(65, 208, 235, .18);
        border-radius: 20px;
        overflow: hidden;
        margin-bottom: 30px;
    }

    .panel-header {
        padding: 22px 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid rgba(255,255,255,.08);
    }

    .panel-header h2 {
        font-size: 21px;
    }

    .panel-header span {
        color: #71a5ba;
        font-size: 14px;
    }

    /* =========================
       TABLA
    ========================= */

    .table-container {
        overflow-x: auto;
    }

    .table-container table {
        width: 100%;
        border-collapse: collapse;
    }

    .table-container th {
        text-align: left;
        padding: 16px 22px;
        font-size: 12px;
        color: #72a5b8;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .table-container td {
        padding: 18px 22px;
        border-top: 1px solid rgba(255,255,255,.06);
        color: #d8e8ee;
    }

    .table-container tr:hover {
        background: rgba(47, 207, 235, .04);
    }

    /* =========================
       INSTRUCTOR
    ========================= */

    .instructor {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    /* Renombrado a .row-avatar para no chocar con .avatar del sidebar/topbar del layout */
    .row-avatar {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: #42d4eb;
        color: #063044;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
    }

    .instructor-info strong {
        display: block;
        margin-bottom: 4px;
    }

    .instructor-info small {
        color: #719caf;
    }

    /* =========================
       SUCURSAL
    ========================= */

    .branch {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        background: rgba(65,208,235,.08);
        border: 1px solid rgba(65,208,235,.15);
        padding: 7px 11px;
        border-radius: 20px;
        font-size: 13px;
    }

    .dot {
        width: 7px;
        height: 7px;
        background: #42d4eb;
        border-radius: 50%;
    }

    /* =========================
       DISPONIBILIDAD
    ========================= */

    .available {
        color: #39e1bd;
        font-weight: 700;
    }

    .busy {
        color: #ffbf38;
        font-weight: 700;
    }

    /* =========================
       ACCIONES TABLA
    ========================= */

    .table-actions {
        display: flex;
        gap: 8px;
    }

    .icon-btn {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        border: 1px solid rgba(255,255,255,.08);
        background: #08384d;
        color: white;
        cursor: pointer;
    }

    .icon-btn:hover {
        background: #0c526a;
    }

    /* =========================
       RESPONSIVE
    ========================= */

    @media(max-width: 1100px) {

        .stats {
            grid-template-columns: repeat(2, 1fr);
        }

    }

    @media(max-width: 700px) {

        .panel-header {
            align-items: flex-start;
            flex-direction: column;
            gap: 8px;
        }

        .table-container th,
        .table-container td {
            white-space: nowrap;
        }

    }

</style>
@endpush

@section('content')

    <div class="header-left">
        <p>
            Administración de instructores y disponibilidad.
        </p>
    </div>


    <!-- BOTONES -->

    <div class="actions">

        <button type="button" class="btn btn-primary" onclick="abrirModalInstructor()">
            + Registrar instructor
        </button>

    </div>


    <!-- ESTADÍSTICAS -->

    <div class="stats">

        <div class="stat-card">

            <div class="stat-title">
                Instructores registrados
            </div>

            <div class="stat-number">
                {{ $totalRegistrados }}
            </div>

            <div class="stat-description">
                {{ $esVistaGlobal ? 'Ambas sucursales' : $sucursalesVisibles->first()->nombre }}
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-title">
                Disponibles ahora
            </div>

            <div class="stat-number">
                {{ $totalDisponibles }}
            </div>

            <div class="stat-description">
                Disponibilidad activa
            </div>

        </div>


        @if ($esVistaGlobal)
            @foreach ($sucursales->take(2) as $sucursalCard)
                <div class="stat-card">

                    <div class="stat-title">
                        {{ $sucursalCard->nombre }}
                    </div>

                    <div class="stat-number">
                        {{ $porSucursal[$sucursalCard->id] ?? 0 }}
                    </div>

                    <div class="stat-description">
                        Instructores asignados
                    </div>

                </div>
            @endforeach
        @else
            <div class="stat-card">

                <div class="stat-title">
                    Activos
                </div>

                <div class="stat-number">
                    {{ $instructores->filter(fn ($fila) => $fila['instructor']->estado === 'activo')->count() }}
                </div>

                <div class="stat-description">
                    Habilitados para dar clase
                </div>

            </div>

            <div class="stat-card">

                <div class="stat-title">
                    Inactivos
                </div>

                <div class="stat-number">
                    {{ $instructores->filter(fn ($fila) => $fila['instructor']->estado !== 'activo')->count() }}
                </div>

                <div class="stat-description">
                    Desactivados temporalmente
                </div>

            </div>
        @endif

    </div>


    <!-- LISTA -->

    <div class="panel">

        <div class="panel-header">

            <h2>Equipo de instructores</h2>

            <span>
                Administración del personal
            </span>

        </div>


        <div class="table-container">

            <table>

                <thead>

                    <tr>

                        <th>
                            Instructor
                        </th>

                        <th>
                            Sucursal
                        </th>

                        <th>
                            Especialidad
                        </th>

                        <th>
                            Disponibilidad
                        </th>

                        <th>
                            Estado
                        </th>

                        <th>
                            Acciones
                        </th>

                    </tr>

                </thead>


                <tbody>


                    @forelse ($instructores as $fila)
                        @php($instructor = $fila['instructor'])
                        <tr
                            data-id="{{ $instructor->id }}"
                            data-name="{{ $instructor->user?->name }}"
                            data-email="{{ $instructor->user?->email }}"
                            data-telefono="{{ $instructor->user?->telefono }}"
                            data-especialidad="{{ $instructor->especialidad }}">

                            <td>

                                <div class="instructor">

                                    <div class="row-avatar">
                                        {{ $fila['iniciales'] }}
                                    </div>

                                    <div class="instructor-info">

                                        <strong>
                                            {{ $instructor->user?->name ?? 'Sin usuario' }}
                                        </strong>

                                        <small>
                                            {{ $instructor->user?->email }}
                                        </small>

                                    </div>

                                </div>

                            </td>


                            <td>

                                <span class="branch">

                                    <span class="dot"></span>

                                    {{ $instructor->sucursal?->nombre }}

                                </span>

                            </td>


                            <td>
                                {{ $instructor->especialidad ?? 'Sin especialidad' }}
                            </td>


                            <td class="{{ $fila['enClase'] ? 'busy' : 'available' }}">
                                {{ $fila['enClase'] ? 'En clase' : 'Disponible' }}
                            </td>


                            <td class="{{ $instructor->estado === 'activo' ? 'available' : 'busy' }}">
                                ● {{ $instructor->estado === 'activo' ? 'Activo' : 'Inactivo' }}
                            </td>


                            <td>

                                <div class="table-actions">

                                    <a href="{{ route('evaluaciones.instructor', $instructor) }}" class="icon-btn" title="Ver evaluaciones" style="display:inline-flex;align-items:center;justify-content:center;text-decoration:none;">
                                        👁
                                    </a>

                                    <button type="button" class="icon-btn" title="Editar" onclick="abrirModalInstructor(this.closest('tr'))">
                                        ✎
                                    </button>

                                    <form action="{{ route('instructores.toggle-estado', $instructor) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Cambiar el estado de {{ $instructor->user?->name }}?');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="icon-btn" title="Activar/Desactivar">
                                            ⋮
                                        </button>
                                    </form>

                                </div>

                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center;color:#71a5ba;">
                                No hay instructores registrados todavía.
                            </td>
                        </tr>
                    @endforelse


                </tbody>

            </table>

        </div>

    </div>


    <!-- =========================================================
         MODAL INSTRUCTOR (crear / editar)
    ========================================================== -->

    <div id="modalInstructor" style="display:none;position:fixed;inset:0;z-index:200;background:rgba(1,15,23,.7);align-items:center;justify-content:center;">

        <div style="width:100%;max-width:480px;max-height:90vh;overflow-y:auto;background:#052d40;border:1px solid rgba(65,208,235,.25);border-radius:20px;padding:28px;">

            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;">
                <h2 id="modalInstructorTitulo" style="font-size:20px;">Registrar instructor</h2>
                <button type="button" onclick="cerrarModalInstructor()" style="background:none;border:none;color:white;font-size:22px;cursor:pointer;">×</button>
            </div>

            @if ($errors->any())
                <div style="margin-bottom:14px;padding:12px 16px;border-radius:12px;background:rgba(255,95,109,.10);border:1px solid rgba(255,95,109,.35);color:#ff9aa1;font-size:12px;">
                    <ul style="margin:0 0 0 16px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="formInstructorCrear" method="POST" action="{{ route('instructores.store') }}">
                @csrf

                <?php
                    $campoEstilo = 'width:100%;margin-top:6px;margin-bottom:14px;height:48px;padding:0 14px;border-radius:12px;border:1px solid rgba(83,214,238,.20);background:#042337;color:white;';
                ?>

                <label>Nombre completo</label>
                <input type="text" name="name" required style="{{ $campoEstilo }}" placeholder="Nombre del instructor">

                <label>Correo electrónico</label>
                <input type="email" name="email" required style="{{ $campoEstilo }}" placeholder="correo@quantika.com">

                <label>Teléfono</label>
                <input type="text" name="telefono" style="{{ $campoEstilo }}" placeholder="10 dígitos">

                <label>Especialidad</label>
                <input type="text" name="especialidad" style="{{ $campoEstilo }}" placeholder="Nivel avanzado, natación adaptada, etc.">

                @if ($esVistaGlobal)
                    <label>Sucursal</label>
                    <select name="sucursal_id" required style="{{ $campoEstilo }}">
                        <option value="">Seleccionar sucursal</option>
                        @foreach ($sucursales as $sucursalOpcion)
                            <option value="{{ $sucursalOpcion->id }}">{{ $sucursalOpcion->nombre }}</option>
                        @endforeach
                    </select>
                @endif

                <div style="display:flex;gap:12px;margin-top:10px;">
                    <button type="submit" class="btn btn-primary">Registrar instructor</button>
                    <button type="button" class="btn-secondary" onclick="cerrarModalInstructor()">Cancelar</button>
                </div>

            </form>

            <form id="formInstructorEditar" method="POST" style="display:none;">
                @csrf
                @method('PUT')

                <label>Nombre completo</label>
                <input type="text" name="name" required style="{{ $campoEstilo }}">

                <label>Correo electrónico</label>
                <input type="email" name="email" required style="{{ $campoEstilo }}">

                <label>Teléfono</label>
                <input type="text" name="telefono" style="{{ $campoEstilo }}">

                <label>Especialidad</label>
                <input type="text" name="especialidad" style="{{ $campoEstilo }}">

                <div style="display:flex;gap:12px;margin-top:10px;">
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    <button type="button" class="btn-secondary" onclick="cerrarModalInstructor()">Cancelar</button>
                </div>

            </form>

        </div>

    </div>

@endsection

@push('scripts')
<script>

function abrirModalInstructor(fila) {

    const modal = document.getElementById('modalInstructor');
    const formCrear = document.getElementById('formInstructorCrear');
    const formEditar = document.getElementById('formInstructorEditar');
    const titulo = document.getElementById('modalInstructorTitulo');

    if (fila) {
        titulo.textContent = 'Editar instructor';
        formCrear.style.display = 'none';
        formEditar.style.display = 'block';
        formEditar.action = '/instructores/' + fila.dataset.id;
        formEditar.querySelector('[name="name"]').value = fila.dataset.name || '';
        formEditar.querySelector('[name="email"]').value = fila.dataset.email || '';
        formEditar.querySelector('[name="telefono"]').value = fila.dataset.telefono || '';
        formEditar.querySelector('[name="especialidad"]').value = fila.dataset.especialidad || '';
    } else {
        titulo.textContent = 'Registrar instructor';
        formCrear.style.display = 'block';
        formEditar.style.display = 'none';
    }

    modal.style.display = 'flex';
}

function cerrarModalInstructor() {
    document.getElementById('modalInstructor').style.display = 'none';
}

@if ($errors->any())
    document.addEventListener('DOMContentLoaded', function () { abrirModalInstructor(); });
@endif

</script>
@endpush
