{{--
    Topbar del portal. Variables esperadas:
    $titulo (string), $subtitulo (string, opcional)
    $alumnos (Collection<Alumno>), $alumno (?Alumno) el alumno activo
--}}
@php
    $usuario = auth()->user();
    $inicialesUsuario = collect(preg_split('/\s+/', trim($usuario->name)))
        ->map(fn ($parte) => mb_substr($parte, 0, 1))
        ->take(2)
        ->implode('');

    $otrosParametros = collect(request()->query())->except('alumno')->all();
@endphp

<header class="topbar">

    <div class="page-title">
        <small>QUANTIKA POOL · PORTAL DE ALUMNOS Y TUTORES</small>
        <h1>{{ $titulo }}</h1>
    </div>

    <div class="top-actions">

        @if (($alumnos ?? collect())->count() > 1)
            <form method="GET" action="{{ request()->url() }}" class="alumno-select">
                @foreach ($otrosParametros as $clave => $valor)
                    <input type="hidden" name="{{ $clave }}" value="{{ $valor }}">
                @endforeach
                <select name="alumno" onchange="this.form.submit()" aria-label="Elegir alumno">
                    @foreach ($alumnos as $unAlumno)
                        <option value="{{ $unAlumno->id }}" @selected($alumno && $alumno->id === $unAlumno->id)>
                            {{ $unAlumno->nombreCompleto() }}
                        </option>
                    @endforeach
                </select>
            </form>
        @elseif ($alumno)
            <div class="alumno-select" style="cursor:default;">
                <span class="branch-dot" style="width:9px;height:9px;border-radius:50%;background:var(--cyan);box-shadow:0 0 12px var(--cyan);"></span>
                <span>{{ $alumno->nombreCompleto() }}</span>
            </div>
        @endif

        <div class="top-user">
            @if ($alumno?->foto_path)
                <img class="avatar avatar-photo" src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($alumno->foto_path) }}" alt="Foto de {{ $alumno->nombreCompleto() }}">
                <span>{{ $alumno->nombreCompleto() }}</span>
            @else
                <div class="avatar">{{ mb_strtoupper($inicialesUsuario) }}</div>
                <span>{{ $usuario->name }}</span>
            @endif
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout" title="Cerrar sesión">⏻</button>
        </form>

    </div>

</header>

@php
    $anualidadVencida = $alumno ? \App\Support\Anualidades::vencida($alumno) : null;
@endphp

@if ($anualidadVencida)
    <div role="alert" style="margin: 24px 34px 0; padding: 16px 20px; border-radius: 16px; background: rgba(255,107,107,.12); border: 1px solid rgba(255,107,107,.45); color: #ffb3b3; font-size: 14px; line-height: 1.5;">
        <strong style="display:block; color:#fff; font-size:15px; margin-bottom:4px;">⚠ Se venció la anualidad de {{ $alumno->nombreCompleto() }}</strong>
        Venció el {{ $anualidadVencida->fecha_vencimiento->format('d/m/Y') }} (${{ number_format((float) $anualidadVencida->monto, 2) }}).
        Les pedimos pagarla lo antes posible para evitar la suspensión del servicio.
    </div>
@endif

@if (session('status'))
    <div class="flash-status" style="margin: 24px 34px 0;">{{ session('status') }}</div>
@endif

@if ($errors->any())
    <div class="flash-errors" style="margin: 24px 34px 0;">
        <strong>No se pudo completar la acción:</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
