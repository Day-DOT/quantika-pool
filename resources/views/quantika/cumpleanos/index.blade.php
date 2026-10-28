@extends('quantika.super-admin.layout')

@section('title', 'Cumpleaños')
@section('page-title', 'Calendario de cumpleaños')

@push('styles')
<style>
    .bday-layout { display: grid; grid-template-columns: minmax(0, 1fr) 300px; gap: 20px; align-items: start; }

    .bday-header { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 16px; }
    .bday-header h3 { font-size: 18px; }
    .bday-nav { display: flex; gap: 8px; }

    .bday-grid { display: grid; grid-template-columns: repeat(7, minmax(0, 1fr)); gap: 6px; }
    .bday-weekday { color: var(--muted); font-size: 11px; font-weight: 800; text-align: center; letter-spacing: 1px; padding: 4px 0; }

    .bday-day {
        min-height: 92px; padding: 8px; border-radius: 12px;
        background: rgba(2, 30, 44, .55); border: 1px solid rgba(66, 213, 238, .10);
        display: flex; flex-direction: column; gap: 4px; min-width: 0;
    }
    .bday-day.fuera { opacity: .35; }
    .bday-day.hoy { border-color: var(--cyan); box-shadow: inset 0 0 0 1px var(--cyan); }
    .bday-day.con-cumple { background: rgba(255, 194, 41, .07); border-color: rgba(255, 194, 41, .30); }
    .bday-num { font-size: 12px; font-weight: 800; color: var(--muted); }
    .bday-day.hoy .bday-num { color: var(--cyan); }

    .bday-chip {
        font-size: 11px; font-weight: 700; color: #fff; line-height: 1.3;
        padding: 4px 6px; border-radius: 8px; background: rgba(255, 194, 41, .14);
        overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    }
    .bday-chip:hover { background: rgba(255, 194, 41, .24); }
    .bday-chip small { color: var(--yellow); font-weight: 800; }

    .bday-list-item { display: flex; justify-content: space-between; gap: 10px; padding: 10px 0; border-bottom: 1px solid rgba(255,255,255,.05); }
    .bday-list-item:last-child { border-bottom: none; }
    .bday-list-item strong { display: block; font-size: 13px; }
    .bday-list-item small { color: var(--muted); font-size: 11.5px; }
    .bday-cuando { color: var(--yellow); font-size: 11.5px; font-weight: 800; white-space: nowrap; text-align: right; }
    .bday-cuando.hoy { color: var(--cyan); }

    @media (max-width: 1100px) { .bday-layout { grid-template-columns: 1fr; } }
    @media (max-width: 700px) {
        .bday-grid { gap: 3px; }
        .bday-day { min-height: 58px; padding: 5px; }
        .bday-chip { font-size: 0; padding: 0; width: 8px; height: 8px; border-radius: 50%; background: var(--yellow); }
        .bday-chip small { display: none; }
    }
</style>
@endpush

@section('content')

    <div class="bday-layout">

        <div class="panel">

            <div class="bday-header">
                <h3>🎂 {{ ucfirst($mes->translatedFormat('F Y')) }}</h3>
                <div class="bday-nav">
                    <a href="{{ route('cumpleanos.index', ['mes' => $mesAnterior]) }}" class="btn btn-outline btn-sm" aria-label="Mes anterior">‹</a>
                    <a href="{{ route('cumpleanos.index') }}" class="btn btn-outline btn-sm">Hoy</a>
                    <a href="{{ route('cumpleanos.index', ['mes' => $mesSiguiente]) }}" class="btn btn-outline btn-sm" aria-label="Mes siguiente">›</a>
                </div>
            </div>

            <div class="bday-grid">
                @foreach (\App\Enums\DiaSemana::cases() as $diaSemana)
                    <div class="bday-weekday">{{ mb_strtoupper(mb_substr($diaSemana->label(), 0, 3)) }}</div>
                @endforeach

                @foreach ($dias as $dia)
                    @php
                        $delMes = $dia->month === $mes->month;
                        $cumples = $delMes ? $cumpleanosDelMes->get($dia->day, collect()) : collect();
                    @endphp
                    <div class="bday-day {{ $delMes ? '' : 'fuera' }} {{ $dia->isToday() ? 'hoy' : '' }} {{ $cumples->isNotEmpty() ? 'con-cumple' : '' }}">
                        <span class="bday-num">{{ $dia->day }}</span>
                        @foreach ($cumples as $cumple)
                            <a href="{{ route('alumnos.show', $cumple['alumno']) }}" class="bday-chip"
                               title="{{ $cumple['alumno']->nombreCompleto() }} · cumple {{ $cumple['cumple'] }}">
                                {{ $cumple['alumno']->nombre }} <small>{{ $cumple['cumple'] }}</small>
                            </a>
                        @endforeach
                    </div>
                @endforeach
            </div>

            @if ($cumpleanosDelMes->isEmpty())
                <div class="empty-state" style="padding:20px 0 0;">Ningún alumno activo cumple años este mes.</div>
            @endif

        </div>

        <div class="panel">

            <div class="bday-header">
                <h3>Próximos {{ \App\Support\Cumpleanos::DIAS_DE_AVISO }} días</h3>
            </div>

            @forelse ($proximos as $cumple)
                @php
                    $faltan = (int) today()->diffInDays($cumple['fecha']);
                @endphp
                <div class="bday-list-item">
                    <div>
                        <a href="{{ route('alumnos.show', $cumple['alumno']) }}"><strong>{{ $cumple['alumno']->nombreCompleto() }}</strong></a>
                        <small>Cumple {{ $cumple['cumple'] }} {{ $cumple['cumple'] === 1 ? 'año' : 'años' }}{{ $cumple['alumno']->nivel ? ' · '.$cumple['alumno']->nivel->nombre : '' }}</small>
                    </div>
                    <div class="bday-cuando {{ $faltan === 0 ? 'hoy' : '' }}">
                        @if ($faltan === 0)
                            ¡Hoy! 🎉
                        @elseif ($faltan === 1)
                            Mañana
                        @else
                            {{ \App\Support\ClasesDelAlumno::fechaCorta($cumple['fecha']) }}
                        @endif
                    </div>
                </div>
            @empty
                <div class="empty-state" style="padding:20px 0;">No hay cumpleaños en los próximos días.</div>
            @endforelse

        </div>

    </div>

@endsection
