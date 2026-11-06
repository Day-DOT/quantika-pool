{{-- Un pago en el calendario. $completo = false para el renglón compacto del
     día (las acciones de pago abren la ventana del día, $dialogo); true para
     la ventana, con el método de pago y el comprobante. --}}
@php
    $pagado = $pago->estado === \App\Enums\EstadoPago::Pagado;
    $vencido = $pago->estaVencido();
    $diasVencido = $vencido ? (int) $pago->fecha_vencimiento->diffInDays(today()) : 0;
    $color = match (true) {
        $pago->esAnualidad() => 'purple',
        $diasVencido >= 5 => 'red',
        $diasVencido > 0 => 'yellow',
        default => 'green',
    };
    $concepto = $pago->esAnualidad() ? 'Anualidad' : $pago->concepto->label();
@endphp

<div class="calendar-event calendar-event-{{ $color }} {{ $completo ? 'calendar-modal-event' : '' }}">
    <strong>{{ $pago->alumno->nombreCompleto() }}</strong>
    <span class="calendar-event-concepto">{{ $concepto }}</span>
    ${{ number_format((float) $pago->monto, 0) }}

    @if ($vencido && $pago->esAnualidad())
        <span class="calendar-event-tag calendar-event-tag-red">Anualidad vencida</span>
    @elseif ($vencido && $completo)
        <span class="calendar-event-tag calendar-event-tag-red">Vencido hace {{ $diasVencido }} {{ $diasVencido === 1 ? 'día' : 'días' }}</span>
    @endif

    @if ($completo && $pagado)
        <div class="calendar-event-detalle">
            Pagado{{ $pago->fecha_pago ? ' el '.$pago->fecha_pago->format('d/m/Y') : '' }}{{ $pago->metodo_pago ? ' · '.$pago->metodo_pago->label() : '' }}
        </div>
    @endif

    <div class="calendar-event-actions">
        <a href="{{ route('pagos.edit', $pago) }}">Editar</a>
        @if ($pago->comprobante_path)
            <a href="{{ route('pagos.comprobante', $pago) }}" target="_blank" rel="noopener">Comprobante</a>
        @endif
        @unless ($pagado)
            @unless ($completo)
                <button type="button" data-dialog-target="{{ $dialogo }}">Pagar</button>
            @endunless
            <form action="{{ route('pagos.destroy', $pago) }}" method="POST" onsubmit="return confirm('¿Eliminar este pago?');">
                @csrf
                @method('DELETE')
                <button type="submit">Eliminar</button>
            </form>
        @endunless
    </div>

    @if ($completo && ! $pagado)
        <form action="{{ route('pagos.marcar-pagado', $pago) }}" method="POST" class="calendar-event-pagar"
              onsubmit="return confirm(@js($pago->esAnualidad() ? '¿Marcar la anualidad como pagada y agendar la del año siguiente?' : '¿Marcar este pago como pagado y agendar el siguiente mes?'));">
            @csrf
            @method('PATCH')
            <label>
                Método de pago
                <select name="metodo_pago" required>
                    @foreach (\App\Enums\MetodoPago::cases() as $metodo)
                        <option value="{{ $metodo->value }}">{{ $metodo->label() }}</option>
                    @endforeach
                </select>
            </label>
            <button type="submit">Marcar pagado</button>
        </form>
    @endif
</div>
