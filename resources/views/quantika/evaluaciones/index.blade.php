@extends('quantika.super-admin.layout')

@section('title', 'Evaluaciones')
@section('page-title', 'Evaluaciones')

@push('styles')
<style>

    .title-area p {
        color: #79aabd;
        font-size: 16px;
        margin-bottom: 10px;
    }

    .top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 35px;
        gap: 20px;
        flex-wrap: wrap;
    }

    /* Renombrado a .monitoreo-badge para no chocar con .badge del layout */
    .monitoreo-badge {
        padding: 10px 18px;
        border-radius: 30px;
        border: 1px solid rgba(55,210,238,.35);
        background: rgba(13,66,88,.55);
        color: #42d4ee;
        font-size: 13px;
        font-weight: bold;
    }

    .section-title {
        margin-bottom: 20px;
        font-size: 21px;
    }

    .instructors {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 22px;
    }

    .instructor-card {
        background:
            linear-gradient(
                145deg,
                rgba(10,65,86,.95),
                rgba(4,38,54,.96)
            );

        border: 1px solid rgba(55,190,220,.18);
        border-radius: 22px;
        padding: 24px;
        text-decoration: none;
        color: white;
        transition: .25s;
        position: relative;
        overflow: hidden;
        display: block;
    }

    .instructor-card::after {
        content: "";
        position: absolute;
        width: 130px;
        height: 130px;
        right: -55px;
        bottom: -65px;
        border-radius: 50%;
        background: rgba(40,210,235,.08);
    }

    .instructor-card:hover {
        transform: translateY(-5px);
        border-color: rgba(57,214,239,.65);
        box-shadow: 0 15px 40px rgba(0,0,0,.25);
    }

    .instructor-header {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 25px;
    }

    /* Renombrado a .instructor-avatar para no chocar con .avatar del sidebar/topbar del layout */
    .instructor-avatar {
        width: 60px;
        height: 60px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #40d5ed, #21aeca);
        color: #043043;
        font-size: 20px;
        font-weight: 900;
    }

    .instructor-name {
        font-size: 19px;
        font-weight: 800;
    }

    .instructor-info {
        color: #79aabd;
        margin-top: 5px;
        font-size: 14px;
    }

    .instructor-card .stats {
        display: flex;
        gap: 12px;
    }

    .instructor-card .stat {
        flex: 1;
        background: rgba(0,25,38,.35);
        border: 1px solid rgba(255,255,255,.06);
        border-radius: 14px;
        padding: 13px;
    }

    .instructor-card .stat-number {
        font-size: 22px;
        font-weight: 800;
    }

    .instructor-card .stat-label {
        font-size: 11px;
        color: #6e9daf;
        margin-top: 4px;
    }

    .view {
        margin-top: 20px;
        color: #42d4ee;
        font-weight: bold;
        font-size: 13px;
    }

    @media(max-width: 1000px) {
        .instructors {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media(max-width: 650px) {
        .top {
            align-items: flex-start;
            gap: 15px;
            flex-direction: column;
        }

        .instructors {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')

    <div class="top">

        <div class="title-area">
            <p>
                Consulta el progreso de los alumnos evaluados por cada instructor.
            </p>
        </div>

        <div class="monitoreo-badge">
            QUANTIKA POOL · MONITOREO
        </div>

    </div>

    <h2 class="section-title">
        Instructores
    </h2>

    <div class="instructors">

        @forelse ($instructores as $fila)
            @php($instructor = $fila['instructor'])
            <a href="{{ route('evaluaciones.instructor', $instructor) }}"
               class="instructor-card">

                <div class="instructor-header">

                    <div class="instructor-avatar">
                        {{ $fila['iniciales'] }}
                    </div>

                    <div>
                        <div class="instructor-name">
                            {{ $instructor->user?->name ?? 'Sin usuario' }}
                        </div>

                        <div class="instructor-info">
                            Instructor · {{ $instructor->sucursal?->nombre }}
                        </div>
                    </div>

                </div>

                <div class="stats">

                    <div class="stat">
                        <div class="stat-number">{{ $fila['totalAlumnos'] }}</div>
                        <div class="stat-label">
                            ALUMNOS
                        </div>
                    </div>

                    <div class="stat">
                        <div class="stat-number">{{ $fila['totalEvaluados'] }}</div>
                        <div class="stat-label">
                            EVALUADOS
                        </div>
                    </div>

                </div>

                <div class="view">
                    Ver alumnos →
                </div>

            </a>
        @empty
            <p style="color:#79aabd;">No hay instructores registrados todavía.</p>
        @endforelse

    </div>

@endsection
