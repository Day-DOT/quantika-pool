<div class="form-grid">

    <div class="form-group">
        <label for="sucursal_id">Sucursal</label>
        <select id="sucursal_id" name="sucursal_id" class="form-select" required>
            @foreach ($sucursales as $s)
                <option value="{{ $s->id }}" {{ (string) old('sucursal_id', $carril->sucursal_id ?? '') === (string) $s->id ? 'selected' : '' }}>
                    {{ $s->nombre }}
                </option>
            @endforeach
        </select>
        @error('sucursal_id') <span class="form-error">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label for="nombre">Nombre del carril</label>
        <input type="text" id="nombre" name="nombre" class="form-input" value="{{ old('nombre', $carril->nombre ?? '') }}" placeholder="p.ej. Carril 1" required>
        @error('nombre') <span class="form-error">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label for="capacidad_maxima">Capacidad máxima</label>
        <input type="number" id="capacidad_maxima" name="capacidad_maxima" class="form-input" min="1" value="{{ old('capacidad_maxima', $carril->capacidad_maxima ?? 8) }}" required>
        @error('capacidad_maxima') <span class="form-error">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label>Disponibilidad</label>
        <div class="checkbox-row">
            <input type="hidden" name="activo" value="0">
            <input type="checkbox" id="activo" name="activo" value="1" {{ old('activo', $carril->activo ?? true) ? 'checked' : '' }}>
            <label for="activo" style="font-weight:600; color:var(--text);">Carril activo</label>
        </div>
    </div>

</div>

<div class="form-actions">
    <button type="submit" class="btn btn-primary">{{ $carril ? 'Guardar cambios' : 'Crear carril' }}</button>
    <a href="{{ route('super-admin.carriles.index') }}" class="btn btn-outline">Cancelar</a>
</div>
