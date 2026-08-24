@extends('quantika.super-admin.layout')

@section('title', 'Seguridad')
@section('page-title', 'Seguridad')

@section('content')

    <a href="{{ route('configuracion.index') }}" class="breadcrumb-back">← Volver a configuración</a>

    <p style="color:var(--muted); font-size:14px; margin-bottom:20px;">
        Actualiza la contraseña de tu cuenta ({{ auth()->user()->email }}).
    </p>

    @if (session('status'))
        <div style="margin-bottom:18px;padding:12px 16px;border-radius:12px;background:rgba(19,227,162,.10);border:1px solid rgba(19,227,162,.30);color:#13e3a2;font-size:13px;">
            {{ session('status') }}
        </div>
    @endif

    <div class="panel form-card" style="max-width:520px;">
        <form method="POST" action="{{ route('super-admin.seguridad.update') }}">
            @csrf
            @method('PUT')

            <div class="form-grid">

                <div class="form-group full">
                    <label for="password_actual">Contraseña actual</label>
                    <input type="password" id="password_actual" name="password_actual" class="form-input" required>
                    @error('password_actual') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group full">
                    <label for="password">Nueva contraseña</label>
                    <input type="password" id="password" name="password" class="form-input" required>
                    <span class="form-hint">Mínimo 8 caracteres.</span>
                    @error('password') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group full">
                    <label for="password_confirmation">Confirmar nueva contraseña</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" required>
                </div>

            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Actualizar contraseña</button>
                <a href="{{ route('configuracion.index') }}" class="btn btn-outline">Cancelar</a>
            </div>

        </form>
    </div>

@endsection
