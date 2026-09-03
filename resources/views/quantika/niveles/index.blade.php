@extends('quantika.super-admin.layout')

@section('title', 'Niveles')
@section('page-title', 'Niveles de aprendizaje')

@push('styles')
<style>

    .page {
        width: 100%;
        max-width: 1500px;
        margin: auto;
    }

    /* ENCABEZADO */

    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        gap: 20px;
    }

    .header-left h1 {
        font-size: 32px;
        font-weight: 800;
        margin-bottom: 8px;
    }

    .header-left p {
        color: #83aabd;
        font-size: 15px;
    }

    /* BARRA SUPERIOR */

    .top-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    .section-title {
        font-size: 20px;
        font-weight: 800;
    }

    .add-btn {
        border: none;
        cursor: pointer;
        padding: 13px 22px;
        border-radius: 12px;
        background: #42d4ed;
        color: #03202e;
        font-weight: 800;
        font-size: 14px;
        transition: .25s;
    }

    .add-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(66, 212, 237, .25);
    }

    /* GRID */

    .levels-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
    }

    /* CARD */

    .level-card {
        position: relative;
        min-height: 390px;
        overflow: hidden;

        background:
            linear-gradient(
                145deg,
                rgba(9, 62, 82, .96),
                rgba(3, 37, 54, .98)
            );

        border: 1px solid rgba(56, 207, 237, .18);
        border-radius: 22px;

        transition:
            transform .25s ease,
            border-color .25s ease,
            box-shadow .25s ease;
    }

    .level-card:hover {
        transform: translateY(-5px);
        border-color: rgba(56, 207, 237, .55);
        box-shadow: 0 20px 45px rgba(0, 0, 0, .25);
    }

    /* IMAGEN */

    .animal-area {
        height: 190px;
        display: flex;
        justify-content: center;
        align-items: center;

        background:
            radial-gradient(
                circle,
                rgba(48, 205, 237, .14),
                transparent 65%
            );

        position: relative;
    }

    .animal-area::after {
        content: "";
        position: absolute;
        width: 150px;
        height: 150px;
        border-radius: 50%;
        border: 1px solid rgba(63, 211, 239, .15);
    }

    .animal-image {
        position: relative;
        z-index: 2;

        width: 145px;
        height: 145px;

        object-fit: contain;

        filter:
            drop-shadow(0 10px 20px rgba(0,0,0,.35));

        transition: transform .3s ease;
    }

    .level-card:hover .animal-image {
        transform: scale(1.08);
    }

    /* CONTENIDO */

    .level-content {
        padding: 20px 22px 22px;
    }

    .level-number {
        display: inline-block;
        color: #3dd5ef;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 2px;
        margin-bottom: 8px;
    }

    .level-name {
        font-size: 23px;
        font-weight: 800;
        margin-bottom: 8px;
    }

    .level-description {
        color: #88aebe;
        font-size: 13px;
        line-height: 1.55;
        min-height: 62px;
    }

    /* FOOTER CARD */

    .level-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;

        margin-top: 18px;
        padding-top: 15px;

        border-top: 1px solid rgba(255,255,255,.07);
    }

    .level-tag {
        color: #43d6ee;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 1px;
    }

    .view-btn {
        text-decoration: none;
        color: #fff;
        font-size: 12px;
        font-weight: 700;

        padding: 8px 13px;

        border-radius: 9px;

        background: rgba(54, 208, 238, .08);
        border: 1px solid rgba(54, 208, 238, .18);

        transition: .25s;
    }

    .view-btn:hover {
        background: #42d4ed;
        color: #03202e;
    }

    /* RESPONSIVE */

    @media (max-width: 1200px) {
        .levels-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 900px) {
        .levels-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 600px) {

        .header {
            flex-direction: column;
            align-items: flex-start;
        }

        .top-actions {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }

        .levels-grid {
            grid-template-columns: 1fr;
        }

        .level-card {
            min-height: auto;
        }
    }
</style>
@endpush

@section('content')

<div class="page">

    <!-- ENCABEZADO -->

    <div class="header">

        <div class="header-left">

            <h1>Niveles de aprendizaje</h1>

            <p>
                Consulta los niveles de QUANTIKA POOL y sus habilidades de aprendizaje.
            </p>

        </div>

    </div>


    <!-- ACCIONES -->

    <div class="top-actions">

        <div class="section-title">
            Niveles de natación
        </div>

        @if (auth()->user()->isSuperAdmin())
            <a href="{{ route('super-admin.niveles.create') }}" class="add-btn" style="text-decoration:none;display:inline-flex;align-items:center;">
                + Agregar nivel
            </a>
        @else
            <button class="add-btn" type="button" disabled title="Los niveles son fijos del sistema y no se pueden agregar." style="opacity:.5;cursor:not-allowed;">
                + Agregar nivel
            </button>
        @endif

    </div>


    <!-- NIVELES -->

    @foreach ($niveles->groupBy(fn ($fila) => $fila['nivel']->categoria_edad) as $categoriaEdad => $filasGrupo)

        <div class="section-title" style="margin:30px 0 18px;">{{ $categoriaEdad }}</div>

        <div class="levels-grid">

            @foreach ($filasGrupo as $fila)
                @php($nivel = $fila['nivel'])
                <div class="level-card">

                <div class="animal-area">

                    <img
                        src="{{ asset($nivel->imagen) }}"
                        alt="{{ $nivel->nombre }}"
                        class="animal-image"
                    >

                </div>

                <div class="level-content">

                    <span class="level-number">
                        NIVEL {{ str_pad((string) $nivel->orden, 2, '0', STR_PAD_LEFT) }}
                    </span>

                    <h2 class="level-name">
                        {{ $nivel->nombre }}
                    </h2>

                    <p class="level-description">
                        {{ $nivel->descripcion }}
                    </p>

                    <div style="margin:14px 0;">

                        <div style="display:flex;justify-content:space-between;font-size:11px;font-weight:800;color:#8fb2c4;margin-bottom:6px;">
                            <span>{{ $fila['alumnos'] }} alumno{{ $fila['alumnos'] === 1 ? '' : 's' }} en este nivel</span>
                            <span style="color:{{ $nivel->color_hex }};">{{ $fila['progreso'] }}%</span>
                        </div>

                        <div style="width:100%;height:7px;border-radius:20px;background:rgba(255,255,255,.08);overflow:hidden;">
                            <div style="width:{{ $fila['progreso'] }}%;height:100%;border-radius:20px;background:{{ $nivel->color_hex }};"></div>
                        </div>

                    </div>

                    <div class="level-footer">

                        <span class="level-tag">
                            {{ mb_strtoupper($nivel->categoria) }}
                        </span>

                        <a href="{{ route('alumnos.index', ['nivel' => $nivel->id]) }}" class="view-btn">
                            Ver alumnos →
                        </a>

                    </div>

                    @if (auth()->user()->isSuperAdmin())
                        <div style="margin-top:12px; display:flex; gap:8px;">
                            <a href="{{ route('super-admin.niveles.edit', $nivel) }}" class="view-btn">
                                ✎ Editar nivel
                            </a>
                            <form
                                action="{{ route('super-admin.niveles.destroy', $nivel) }}"
                                method="POST"
                                onsubmit="return confirm('¿Eliminar el nivel {{ $nivel->nombre }}? Esta acción no se puede deshacer.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="view-btn" style="color:#ff6b6b;">🗑 Eliminar</button>
                            </form>
                        </div>
                    @endif

                </div>

                </div>
            @endforeach

        </div>

    @endforeach

</div>

@endsection
