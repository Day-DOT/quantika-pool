{{-- Tabla de pagos de varios alumnos (resultado de la búsqueda). --}}
<div class="table-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th>Alumno</th>
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
            @foreach ($pagos as $pagoBuscado)
                @php
                    [$estadoTexto, $estadoClase] = match (true) {
                        $pagoBuscado->estado === \App\Enums\EstadoPago::Pagado => ['Pagado', ''],
                        $pagoBuscado->estado === \App\Enums\EstadoPago::EnRevision => ['En revisión', 'badge-blue'],
                        $pagoBuscado->estaVencido() || $pagoBuscado->estado === \App\Enums\EstadoPago::Vencido => ['Vencido', 'badge-red'],
                        default => ['Pendiente', 'badge-yellow'],
                    };
                @endphp
                <tr>
                    <td>
                        <a href="{{ route('pagos.alumno', $pagoBuscado->alumno_id) }}"><strong>{{ $pagoBuscado->alumno?->nombreCompleto() }}</strong></a>
                        <div style="color:var(--muted); font-size:12px;">{{ $pagoBuscado->alumno?->matricula }}</div>
                    </td>
                    <td>{{ $pagoBuscado->concepto->label() }}</td>
                    <td>{{ $pagoBuscado->fecha_vencimiento?->format('d/m/Y') ?? '—' }}</td>
                    <td>{{ $pagoBuscado->fecha_pago?->format('d/m/Y') ?? '—' }}</td>
                    <td>${{ number_format((float) $pagoBuscado->monto, 2) }}</td>
                    <td>{{ $pagoBuscado->metodo_pago?->label() ?? '—' }}</td>
                    <td><span class="badge {{ $estadoClase }}">{{ $estadoTexto }}</span></td>
                    <td>
                        <div style="display:flex; gap:6px; flex-wrap:wrap;">
                            <a href="{{ route('pagos.edit', $pagoBuscado) }}" class="btn btn-outline btn-sm">Editar</a>
                            @if ($pagoBuscado->comprobante_path)
                                <a href="{{ route('pagos.comprobante', $pagoBuscado) }}" target="_blank" rel="noopener" class="btn btn-outline btn-sm">Comprobante</a>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
