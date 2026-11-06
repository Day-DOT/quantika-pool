{{-- Renglón de un alumno en la agenda semanal: nombre, edad y nivel a simple vista. --}}
@php
    $edadAgenda = $alumno?->edad();
@endphp
<div class="agenda-alumno">
    <span class="agenda-alumno-nombre">{{ $alumno?->nombreCompleto() ?? 'Alumno no disponible' }}</span>
    <span class="agenda-alumno-datos">
        {{ $edadAgenda === null ? 'Edad sin registrar' : $edadAgenda.' '.($edadAgenda === 1 ? 'año' : 'años') }}
        · {{ $alumno?->nombreNivelConSubNivel() ?? 'Sin nivel' }}
        @isset($motivo)
            · {{ $motivo }}
        @endisset
    </span>
</div>
