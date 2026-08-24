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
        <small>QUANTIKA POOL · PORTAL DE TUTORES</small>
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
            <div class="avatar">{{ mb_strtoupper($inicialesUsuario) }}</div>
            <span>{{ $usuario->name }}</span>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout" title="Cerrar sesión">⏻</button>
        </form>

    </div>

</header>

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
