{{-- Acceso rápido al calendario de cumpleaños, con aviso de los próximos días.
     Recibe $cumpleanosProximos (de App\Support\Cumpleanos::proximos). --}}
@php
    $totalCumpleanos = $cumpleanosProximos->count();
    $cumpleanosDeHoy = $cumpleanosProximos->filter(fn (array $cumple) => $cumple['fecha']->isToday());
    $diasDeAviso = \App\Support\Cumpleanos::DIAS_DE_AVISO;
@endphp

<a href="{{ route('cumpleanos.index') }}" class="quick-link-card"
   title="{{ $totalCumpleanos > 0 ? $totalCumpleanos.' cumpleaños en los próximos '.$diasDeAviso.' días' : 'Sin cumpleaños próximos' }}">
    @if ($totalCumpleanos > 0)
        <span class="quick-link-badge">{{ $totalCumpleanos }}</span>
    @endif
    <div class="quick-link-icon">🎂</div>
    <div>
        <div class="quick-link-title">Cumpleaños</div>
        <div class="quick-link-desc">
            @if ($cumpleanosDeHoy->isNotEmpty())
                Hoy: {{ $cumpleanosDeHoy->map(fn (array $cumple) => $cumple['alumno']->nombre)->implode(', ') }} 🎉
            @elseif ($totalCumpleanos > 0)
                {{ $totalCumpleanos }} en los próximos {{ $diasDeAviso }} días
            @else
                Calendario de cumpleaños de los alumnos
            @endif
        </div>
    </div>
</a>
