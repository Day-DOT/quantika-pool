{{-- Resultados del buscador de pagos. Recibe $pagos, $total y $buscando. --}}
@if (! $buscando)
    <div class="empty-state" style="padding:18px 0;">Escribe un nombre, apellido o matrícula, o elige un filtro, para buscar pagos.</div>
@elseif ($pagos->isEmpty())
    <div class="empty-state" style="padding:18px 0;">No hay pagos que coincidan con la búsqueda.</div>
@else
    <div style="color:var(--muted); font-size:12px; margin-bottom:8px;">
        {{ $total }} {{ $total === 1 ? 'pago encontrado' : 'pagos encontrados' }}{{ $total > $pagos->count() ? ' · se muestran los '.$pagos->count().' más recientes; afina la búsqueda para ver los demás' : '' }}
    </div>
    @include('quantika.pagos.partials.historial-busqueda', ['pagos' => $pagos])
@endif
