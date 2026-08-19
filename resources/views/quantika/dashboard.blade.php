@extends('layouts.app')

@section('title', 'Dashboard | QUANTIKA POOL')

@section('page-title', 'Dashboard')

@section('content')

<section class="hero">

    <div class="hero-content">

        <div class="status-pill">
            <span></span>
            SISTEMA ACTIVO
        </div>


        <h2>
            El progreso<br>
            comienza<br>
            <span>en el agua.</span>
        </h2>


        <p>
            Administra alumnos, clases, evaluaciones y niveles
            desde un solo lugar.
        </p>


        <div class="hero-actions">

            <a
                href="{{ route('alumnos.create') }}"
                class="btn btn-primary"
            >
                + Registrar alumno →
            </a>


            <a
                href="{{ route('niveles.index') }}"
                class="btn btn-outline"
            >
                Explorar niveles →
            </a>

        </div>



    </div>


    <div class="hero-logo">

        <img
            src="{{ asset('images/quantika-logo.png') }}"
            alt="Quantika Pool"
        >

    </div>

</section>


<div class="stats-grid">

    <x-stat-card
        icon="♙"
        label="Alumnos activos"
        value="248"
        change="+12 este mes"
    />


    <x-stat-card
        icon="♟"
        label="Instructores"
        value="18"
        change="16 disponibles"
    />


    <x-stat-card
        icon="🏊"
        label="Clases de hoy"
        value="24"
        change="6 en curso"
    />


    <x-stat-card
        icon="$"
        label="Pagos pendientes"
        value="$18,450"
        change="14 pagos"
    />

</div>


<div class="content-grid">

    <section class="panel">

        <div class="panel-header">

            <h3>
                Distribución de alumnos
            </h3>

            <span>
                Por nivel
            </span>

        </div>


        <div class="panel-body">

            <div style="display:grid;gap:20px">

                <div>

                    <div style="
                        display:flex;
                        justify-content:space-between;
                        margin-bottom:8px;
                    ">

                        <span>Principiante</span>

                        <strong>92</strong>

                    </div>

                    <div class="progress">

                        <div
                            class="progress-bar"
                            style="width:72%"
                        ></div>

                    </div>

                </div>


                <div>

                    <div style="
                        display:flex;
                        justify-content:space-between;
                        margin-bottom:8px;
                    ">

                        <span>Intermedio</span>

                        <strong>96</strong>

                    </div>

                    <div class="progress">

                        <div
                            class="progress-bar"
                            style="width:77%"
                        ></div>

                    </div>

                </div>


                <div>

                    <div style="
                        display:flex;
                        justify-content:space-between;
                        margin-bottom:8px;
                    ">

                        <span>Avanzado</span>

                        <strong>60</strong>

                    </div>

                    <div class="progress">

                        <div
                            class="progress-bar"
                            style="width:49%"
                        ></div>

                    </div>

                </div>

            </div>

        </div>

    </section>


    <section class="panel">

        <div class="panel-header">

            <h3>
                Próximas clases
            </h3>

            <span>
                Hoy
            </span>

        </div>


        <div class="panel-body">

            <div style="display:grid;gap:15px">

                <div style="
                    display:flex;
                    justify-content:space-between;
                    padding-bottom:14px;
                    border-bottom:1px solid var(--border);
                ">

                    <div>

                        <strong style="color:white">
                            Estrella
                        </strong>

                        <small style="
                            display:block;
                            color:var(--muted);
                            margin-top:4px;
                        ">
                            Mariana López
                        </small>

                    </div>

                    <span class="badge badge-success">
                        10:00 AM
                    </span>

                </div>


                <div style="
                    display:flex;
                    justify-content:space-between;
                    padding-bottom:14px;
                    border-bottom:1px solid var(--border);
                ">

                    <div>

                        <strong style="color:white">
                            Delfín
                        </strong>

                        <small style="
                            display:block;
                            color:var(--muted);
                            margin-top:4px;
                        ">
                            Carlos Ramírez
                        </small>

                    </div>

                    <span class="badge badge-info">
                        12:00 PM
                    </span>

                </div>


                <div style="
                    display:flex;
                    justify-content:space-between;
                ">

                    <div>

                        <strong style="color:white">
                            Orca
                        </strong>

                        <small style="
                            display:block;
                            color:var(--muted);
                            margin-top:4px;
                        ">
                            Ana Torres
                        </small>

                    </div>

                    <span class="badge badge-warning">
                        16:00 PM
                    </span>

                </div>

            </div>

        </div>

    </section>

</div>


<section style="margin-top:30px">

    <div class="page-heading">

        <div>

            <h2>
                Niveles de natación
            </h2>

            <p>
                Sigue el progreso de cada alumno a través de los niveles.
            </p>

        </div>


        <a
            href="{{ route('niveles.index') }}"
            class="btn btn-outline"
        >
            Ver todos →
        </a>

    </div>


    <div class="level-grid">

        <x-level-card
            number="1"
            animal="⭐"
            name="Estrella"
            category="Principiante"
            description="Adaptación al agua, respiración y ejercicios básicos."
        />


        <x-level-card
            number="2"
            animal="🐚"
            name="Caballito de mar"
            category="Principiante"
            description="Flotación y desplazamientos con apoyo de material."
        />


        <x-level-card
            number="3"
            animal="🪼"
            name="Medusa"
            category="Principiante"
            description="Desplazamientos seguros y flotación sin material."
        />


        <x-level-card
            number="4"
            animal="🐙"
            name="Pulpo"
            category="Intermedio"
            description="Movimientos de crol y primeros clavados."
        />

    </div>

</section>

@endsection