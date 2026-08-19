@extends('quantika.layouts.app')

@section('title', 'Nuevo alumno | QUANTIKA POOL')

@section('page-title', 'Nuevo alumno')

@section('content')

<div class="content">

    <div class="page-intro">

        <h2>
            Registrar alumno
        </h2>

        <p>
            Registra un nuevo alumno en QUANTIKA POOL.
        </p>

    </div>


    <div class="table-card"
         style="padding:35px;">

        <h3 style="margin-bottom:25px;">
            Información del alumno
        </h3>


        <form>

            <div style="
                display:grid;
                grid-template-columns:repeat(2,1fr);
                gap:18px;
            ">


                <div>

                    <label>
                        Nombre
                    </label>

                    <input
                        type="text"
                        placeholder="Nombre del alumno"
                        style="
                            width:100%;
                            margin-top:8px;
                            height:50px;
                            padding:0 15px;
                            border-radius:12px;
                            border:1px solid rgba(83,214,238,.14);
                            background:#042337;
                            color:white;
                        "
                    >

                </div>


                <div>

                    <label>
                        Apellidos
                    </label>

                    <input
                        type="text"
                        placeholder="Apellidos"
                        style="
                            width:100%;
                            margin-top:8px;
                            height:50px;
                            padding:0 15px;
                            border-radius:12px;
                            border:1px solid rgba(83,214,238,.14);
                            background:#042337;
                            color:white;
                        "
                    >

                </div>


                <div>

                    <label>
                        Fecha de nacimiento
                    </label>

                    <input
                        type="date"
                        style="
                            width:100%;
                            margin-top:8px;
                            height:50px;
                            padding:0 15px;
                            border-radius:12px;
                            border:1px solid rgba(83,214,238,.14);
                            background:#042337;
                            color:white;
                        "
                    >

                </div>


                <div>

                    <label>
                        Teléfono
                    </label>

                    <input
                        type="text"
                        placeholder="Teléfono"
                        style="
                            width:100%;
                            margin-top:8px;
                            height:50px;
                            padding:0 15px;
                            border-radius:12px;
                            border:1px solid rgba(83,214,238,.14);
                            background:#042337;
                            color:white;
                        "
                    >

                </div>


                <div>

                    <label>
                        Sucursal
                    </label>

                    <select
                        style="
                            width:100%;
                            margin-top:8px;
                            height:50px;
                            padding:0 15px;
                            border-radius:12px;
                            border:1px solid rgba(83,214,238,.14);
                            background:#042337;
                            color:white;
                        "
                    >

                        <option>
                            Quantika
                        </option>

                        <option>
                            Aqualix
                        </option>

                    </select>

                </div>


                <div>

                    <label>
                        Nivel
                    </label>

                    <select
                        style="
                            width:100%;
                            margin-top:8px;
                            height:50px;
                            padding:0 15px;
                            border-radius:12px;
                            border:1px solid rgba(83,214,238,.14);
                            background:#042337;
                            color:white;
                        "
                    >

                        <option>
                            Tortuga
                        </option>

                        <option>
                            Pez
                        </option>

                        <option>
                            Delfín
                        </option>

                        <option>
                            Tiburón
                        </option>

                    </select>

                </div>

            </div>


            <div style="
                margin-top:30px;
                display:flex;
                gap:12px;
            ">

                <button
                    type="submit"
                    class="btn btn-primary"
                >

                    Guardar alumno

                </button>


                <a
                    href="{{ url('/alumnos') }}"
                    class="btn btn-outline"
                >

                    Cancelar

                </a>

            </div>

        </form>

    </div>

</div>

@endsection