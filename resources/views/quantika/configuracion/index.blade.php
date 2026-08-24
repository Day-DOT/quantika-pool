@extends('quantika.super-admin.layout')

@section('title', 'Configuración')
@section('page-title', 'Configuración')

@push('styles')
<style>
    .description {
        margin-bottom: 26px;
        color: var(--muted);
        font-size: 15px;
    }

    .settings-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
        max-width: 1100px;
    }

    .setting-card {
        position: relative;
        overflow: hidden;
        min-height: 190px;
        padding: 27px;
        border-radius: 24px;
        background: linear-gradient(145deg, rgba(8, 60, 80, .96), rgba(4, 38, 55, .96));
        border: 1px solid rgba(64, 207, 235, .18);
        box-shadow: 0 18px 40px rgba(0, 0, 0, .15);
        transition: transform .25s ease, border-color .25s ease, box-shadow .25s ease;
    }

    .setting-card::after {
        content: "";
        position: absolute;
        width: 130px;
        height: 130px;
        right: -45px;
        bottom: -55px;
        border-radius: 50%;
        background: rgba(64, 207, 235, .07);
    }

    .setting-card:hover {
        transform: translateY(-4px);
        border-color: rgba(64, 207, 235, .45);
        box-shadow: 0 22px 50px rgba(0, 0, 0, .25);
    }

    .setting-icon {
        width: 55px;
        height: 55px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 17px;
        background: rgba(64, 207, 235, .12);
        border: 1px solid rgba(64, 207, 235, .18);
        font-size: 25px;
        margin-bottom: 22px;
    }

    .setting-card h2 {
        font-size: 20px;
        margin-bottom: 8px;
    }

    .setting-card p {
        color: #82aabb;
        font-size: 14px;
        line-height: 1.6;
        max-width: 420px;
    }

    .setting-button {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        margin-top: 20px;
        padding: 10px 17px;
        border-radius: 12px;
        background: rgba(64, 207, 235, .10);
        border: 1px solid rgba(64, 207, 235, .25);
        color: #43d5ef;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        transition: .2s;
    }

    .setting-button:hover {
        background: #40d0eb;
        color: #032331;
    }

    .settings-footer {
        margin-top: 40px;
        padding-top: 20px;
        border-top: 1px solid rgba(255,255,255,.06);
        color: #527c8d;
        font-size: 12px;
    }

    @media (max-width: 850px) {
        .settings-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 550px) {
        .setting-card {
            min-height: auto;
            padding: 22px;
        }
    }
</style>
@endpush

@section('content')

    <p class="description">
        Administra las opciones generales del sistema.
    </p>

    <div class="settings-grid">

        <!-- SUCURSALES -->
        <div class="setting-card">

            <div class="setting-icon">
                🏢
            </div>

            <h2>Sucursales</h2>

            <p>
                Administra las sucursales de Quantika Pool,
                sus datos y disponibilidad.
            </p>

            @if (auth()->user()->isSuperAdmin())
                <a href="{{ route('super-admin.sucursales.index') }}" class="setting-button">
                    Administrar
                    <span>→</span>
                </a>
            @else
                <a href="#" class="setting-button" style="opacity:.55;cursor:not-allowed;" title="Solo el Super Administrador puede gestionar sucursales" onclick="return false;">
                    Solo Super Admin
                    <span>→</span>
                </a>
            @endif

        </div>

        <!-- USUARIOS -->
        <div class="setting-card">

            <div class="setting-icon">
                👤
            </div>

            <h2>Usuarios</h2>

            <p>
                Administra usuarios, roles, accesos y
                sucursal asignada.
            </p>

            @if (auth()->user()->isSuperAdmin())
                <a href="{{ route('super-admin.usuarios.index') }}" class="setting-button">
                    Administrar
                    <span>→</span>
                </a>
            @else
                <a href="#" class="setting-button" style="opacity:.55;cursor:not-allowed;" title="Solo el Super Administrador puede gestionar usuarios" onclick="return false;">
                    Solo Super Admin
                    <span>→</span>
                </a>
            @endif

        </div>

        <!-- ALBERCA -->
        <div class="setting-card">

            <div class="setting-icon">
                🏊
            </div>

            <h2>Alberca</h2>

            <p>
                Configura los carriles, capacidad máxima
                y disponibilidad de la alberca.
            </p>

            <a href="{{ route('carriles.index') }}" class="setting-button">
                Administrar
                <span>→</span>
            </a>

        </div>

        <!-- SEGURIDAD -->
        <div class="setting-card">

            <div class="setting-icon">
                🔐
            </div>

            <h2>Seguridad</h2>

            <p>
                Administra la contraseña y las opciones
                relacionadas con el acceso al sistema.
            </p>

            @if (auth()->user()->isSuperAdmin())
                <a href="{{ route('super-admin.seguridad.index') }}" class="setting-button">
                    Administrar
                    <span>→</span>
                </a>
            @else
                <a href="#" class="setting-button" style="opacity:.55;cursor:not-allowed;" title="Próximamente" onclick="return false;">
                    Próximamente
                    <span>→</span>
                </a>
            @endif

        </div>

    </div>

    <div class="settings-footer">
        QUANTIKA POOL © 2026 · Sistema de administración
    </div>

@endsection
