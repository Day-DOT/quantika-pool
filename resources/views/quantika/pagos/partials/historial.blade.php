{{-- Historial completo de pagos de un alumno. Recibe $pagos (Collection<Pago>). --}}
@if ($pagos->isEmpty())
    <div class="empty-state">Este alumno todavía no tiene pagos registrados.</div>
@else
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Concepto</th>
                    <th>Fecha de corte</th>
                    <th>Fecha de pago</th>
                    <th>Monto</th>
                    <th>Método</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pagos as $pagoHistorial)
                    @php
                        [$estadoTexto, $estadoClase] = match (true) {
                            $pagoHistorial->estado === \App\Enums\EstadoPago::Pagado => ['Pagado', ''],
                            $pagoHistorial->estado === \App\Enums\EstadoPago::EnRevision => ['En revisión', 'badge-blue'],
                            $pagoHistorial->estaVencido() || $pagoHistorial->estado === \App\Enums\EstadoPago::Vencido => ['Vencido', 'badge-red'],
                            default => ['Pendiente', 'badge-yellow'],
                        };
                    @endphp
                    <tr>
                        <td>
                            <strong>{{ $pagoHistorial->concepto->label() }}</strong>
                            @if ($pagoHistorial->periodo)
                                <div style="color:var(--muted); font-size:12px;">Periodo {{ $pagoHistorial->periodo }}</div>
                            @endif
                        </td>
                        <td>{{ $pagoHistorial->fecha_vencimiento?->format('d/m/Y') ?? '—' }}</td>
                        <td>{{ $pagoHistorial->fecha_pago?->format('d/m/Y') ?? '—' }}</td>
                        <td>${{ number_format((float) $pagoHistorial->monto, 2) }}</td>
                        <td>{{ $pagoHistorial->metodo_pago?->label() ?? '—' }}</td>
                        <td><span class="badge {{ $estadoClase }}">{{ $estadoTexto }}</span></td>
                        <td>
                            <div style="display:flex; gap:6px; flex-wrap:wrap;">
                                <a href="{{ route('pagos.edit', $pagoHistorial) }}" class="btn btn-outline btn-sm">Editar</a>
                                @if ($pagoHistorial->comprobante_path)
                                    <a href="{{ route('pagos.comprobante', $pagoHistorial) }}" target="_blank" rel="noopener" class="btn btn-outline btn-sm">Comprobante</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
