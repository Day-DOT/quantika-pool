@extends('layouts.app')

@section('title', 'Dashboard | AQUALIX')

@section('page-title', 'Dashboard AQUALIX')

@section('content')

<section
    class="hero"
    style="
        background-image:
            linear-gradient(
                90deg,
                rgba(2,24,39,.98),
                rgba(2,34,52,.85),
                rgba(2,34,52,.35)
            ),
            url('{{ asset('images/quantika-pool-bg.jpg') }}');
    "
>

    <div class="hero-content">

        <div class="status-pill">

            <span></span>

            SUCURSAL ACTIVA

        </div>


        <h2>

            El progreso<br>

            comienza<br>

            <span>en el agua.</span>

        </h2>


        <p>

            Administra la operación de AQUALIX,
            alumnos, clases, niveles y evaluaciones.

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
                Ver niveles →
            </a>

        </div>


        <div class="hero-metrics">

            <div class="hero-metric">

                <strong>136</strong>

                <span>ALUMNOS ACTIVOS</span>

            </div>


            <div class="hero-metric">

                <strong>8</strong>

                <span>INSTRUCTORES</span>

            </div>


            <div class="hero-metric">

                <strong>96.2%</strong>

                <span>ASISTENCIA</span>

            </div>

        </div>

    </div>


    <div
        class="hero-logo"
        style="
            background:transparent;
            box-shadow:none;
        "
    >

        <img
            src="{{ asset('images/logo-sucursal-2.png') }}"
            alt="AQUALIX"
        >

    </div>

</section>


<div class="stats-grid">

    <x-stat-card
        icon="♙"
        label="Alumnos activos"
        value="136"
        change="+8 este mes"
    />


    <x-stat-card
        icon="♟"
        label="Instructores"
        value="8"
        change="Todos disponibles"
    />


    <x-stat-card
        icon="🏊"
        label="Clases de hoy"
        value="16"
        change="4 en curso"
    />


    <x-stat-card
        icon="$"
        label="Pagos pendientes"
        value="$9,280"
        change="8 pagos"
    />

</div>

@endsection