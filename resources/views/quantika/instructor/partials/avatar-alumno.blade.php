{{-- Foto de perfil del alumno; si no tiene, se muestran sus iniciales. --}}
@if ($alumno?->foto_path)
    <img class="avatar avatar-photo"
         src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($alumno->foto_path) }}"
         alt="Foto de {{ $alumno->nombreCompleto() }}"
         loading="lazy">
@else
    <div class="avatar">{{ $alumno ? $alumno->iniciales() : '—' }}</div>
@endif
