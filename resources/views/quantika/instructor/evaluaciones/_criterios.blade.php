{{--
    Partial reutilizado por create.blade.php y edit.blade.php.
    Espera: $criterios (Collection<CriterioEvaluacion>), $estados (array<EstadoEvaluacionDetalle>)
    y $detallesPorCriterio (Collection<EvaluacionDetalle> keyBy criterio_evaluacion_id, puede venir vacía).
--}}

@foreach ($criterios as $criterio)

    @php
        $detalleActual = $detallesPorCriterio->get($criterio->id);
        $estadoActual = $detalleActual?->estado?->value ?? 'no_iniciado';
        $observacionesActual = $detalleActual?->observaciones ?? '';
    @endphp

    <div class="criterio-row">

        <div>
            <div class="criterio-nombre">{{ $criterio->nombre }}</div>
            @if ($criterio->descripcion)
                <div class="criterio-desc">{{ $criterio->descripcion }}</div>
            @endif
        </div>

        <div class="field" style="margin-bottom:0;">
            <input type="hidden" name="detalles[{{ $loop->index }}][criterio_evaluacion_id]" value="{{ $criterio->id }}">

            <select
                name="detalles[{{ $loop->index }}][estado]"
                class="estado-select"
                data-estado="{{ old("detalles.$loop->index.estado", $estadoActual) }}"
                onchange="this.setAttribute('data-estado', this.value)"
            >
                @foreach ($estados as $estado)
                    <option value="{{ $estado->value }}" @selected(old("detalles.$loop->index.estado", $estadoActual) === $estado->value)>
                        {{ $estado->label() }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="field" style="margin-bottom:0;">
            <textarea
                name="detalles[{{ $loop->index }}][observaciones]"
                placeholder="Observaciones de este criterio (opcional)"
                style="min-height:48px;"
            >{{ old("detalles.$loop->index.observaciones", $observacionesActual) }}</textarea>
        </div>

    </div>

@endforeach
