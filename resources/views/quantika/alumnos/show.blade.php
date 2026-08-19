@extends('layouts.quantika')

@section('title', 'Alumno | Quantika Pool')

@section('page-title', 'Perfil del alumno')

@section('content')

<div class="dashboard-wrapper">


    <div class="section-heading">

        <div>

            <span>
                PERFIL DEL ALUMNO
            </span>

            <h2>
                María González
            </h2>

        </div>

        <a
            href="{{ url('/alumnos') }}"
        >
            ← Regresar
        </a>

    </div>


    <div class="dashboard-cards">


        <div class="dashboard-card">

            <div class="dashboard-card-icon">
                👤
            </div>

            <div class="dashboard-card-label">
                ALUMNO
            </div>

            <div class="dashboard-card-number">
                María
            </div>

            <div class="dashboard-card-description">
                González
            </div>

        </div>


        <div class="dashboard-card">

            <div class="dashboard-card-icon">
                🐬
            </div>

            <div class="dashboard-card-label">
                NIVEL ACTUAL
            </div>

            <div class="dashboard-card-number">
                Delfín
            </div>

            <div class="dashboard-card-description">
                Nivel 03
            </div>

        </div>


        <div class="dashboard-card">

            <div class="dashboard-card-icon">
                📈
            </div>

            <div class="dashboard-card-label">
                PROGRESO
            </div>

            <div class="dashboard-card-number">
                78%
            </div>

            <div class="dashboard-card-description">
                Excelente avance
            </div>

        </div>


        <div class="dashboard-card">

            <div class="dashboard-card-icon">
                ✓
            </div>

            <div class="dashboard-card-label">
                ASISTENCIA
            </div>

            <div class="dashboard-card-number">
                95%
            </div>

            <div class="dashboard-card-description">
                19 de 20 clases
            </div>

        </div>


    </div>


    <section class="levels-section">

        <div class="section-heading">

            <div>

                <span>
                    PROGRESIÓN
                </span>

                <h2>
                    Avance del alumno
                </h2>

            </div>

        </div>


        <div class="dashboard-card">

            <div style="
                display:flex;
                justify-content:space-between;
                margin-bottom:12px;
            ">

                <strong>
                    Progreso del nivel
                </strong>

                <span style="
                    color:#52def5;
                    font-weight:800;
                ">
                    78%
                </span>

            </div>


            <div style="
                width:100%;
                height:12px;
                border-radius:20px;
                background:#062337;
                overflow:hidden;
            ">

                <div style="
                    width:78%;
                    height:100%;
                    border-radius:20px;
                    background:linear-gradient(
                        90deg,
                        #20cbe9,
                        #70e9fa
                    );
                "></div>

            </div>

        </div>

    </section>


</div>

@endsection