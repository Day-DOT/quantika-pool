@extends('layouts.app')

@section('title', 'Alumnos | QUANTIKA POOL')

@section('page-title', 'Alumnos')

@section('content')

<div class="page-heading">

    <div>

        <h2>
            Alumnos
        </h2>

        <p>
            Registro y seguimiento de alumnos por sucursal y nivel.
        </p>

    </div>


    <a
        href="{{ route('alumnos.create') }}"
        class="btn btn-primary"
    >
        + Nuevo alumno
    </a>

</div>


<div class="panel">

    <div class="panel-header">

        <h3>
            Alumnos registrados
        </h3>

        <span>
            248 activos
        </span>

    </div>


    <div class="panel-body">

        <div style="
            display:grid;
            grid-template-columns:2fr 1fr 1fr;
            gap:12px;
            margin-bottom:20px;
        ">

            <input
                class="form-control"
                placeholder="Buscar alumno..."
            >


            <select class="form-control">

                <option>Todos los niveles</option>
                <option>Principiante</option>
                <option>Intermedio</option>
                <option>Avanzado</option>

            </select>


            <select class="form-control">

                <option>Todas las sucursales</option>
                <option>QUANTIKA</option>
                <option>AQUALIX</option>

            </select>

        </div>


        <div class="table-wrapper">

            <table class="data-table">

                <thead>

                    <tr>

                        <th>Alumno</th>

                        <th>Sucursal</th>

                        <th>Nivel</th>

                        <th>Progreso</th>

                        <th>Estado</th>

                        <th></th>

                    </tr>

                </thead>


                <tbody>


                    <tr>

                        <td>

                            <div class="student-cell">

                                <div class="student-avatar">
                                    AL
                                </div>

                                <div>

                                    <strong style="color:white">
                                        Ana López
                                    </strong>

                                    <small style="
                                        display:block;
                                        color:var(--muted);
                                    ">
                                        ana@email.com
                                    </small>

                                </div>

                            </div>

                        </td>


                        <td>
                            QUANTIKA
                        </td>


                        <td>

                            <span class="badge badge-info">
                                🐬 Delfín
                            </span>

                        </td>


                        <td style="min-width:130px">

                            <div class="progress">

                                <div
                                    class="progress-bar"
                                    style="width:78%"
                                ></div>

                            </div>

                            <small style="color:var(--muted)">
                                78%
                            </small>

                        </td>


                        <td>

                            <span class="badge badge-success">
                                Activo
                            </span>

                        </td>


                        <td>

                            <a
                                href="{{ route('alumnos.show', 1) }}"
                                class="btn btn-outline"
                                style="padding:8px 11px"
                            >
                                Ver
                            </a>

                        </td>

                    </tr>


                    <tr>

                        <td>

                            <div class="student-cell">

                                <div class="student-avatar">
                                    MR
                                </div>

                                <div>

                                    <strong style="color:white">
                                        Mateo Ramírez
                                    </strong>

                                    <small style="
                                        display:block;
                                        color:var(--muted);
                                    ">
                                        mateo@email.com
                                    </small>

                                </div>

                            </div>

                        </td>


                        <td>
                            AQUALIX
                        </td>


                        <td>

                            <span class="badge badge-success">
                                🐢 Tortuga
                            </span>

                        </td>


                        <td style="min-width:130px">

                            <div class="progress">

                                <div
                                    class="progress-bar"
                                    style="width:64%"
                                ></div>

                            </div>

                            <small style="color:var(--muted)">
                                64%
                            </small>

                        </td>


                        <td>

                            <span class="badge badge-success">
                                Activo
                            </span>

                        </td>


                        <td>

                            <a
                                href="{{ route('alumnos.show', 2) }}"
                                class="btn btn-outline"
                                style="padding:8px 11px"
                            >
                                Ver
                            </a>

                        </td>

                    </tr>


                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection