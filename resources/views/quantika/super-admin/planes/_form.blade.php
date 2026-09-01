<div class="form-grid">

    <div class="form-group">
        <label for="nombre">Nombre del plan</label>
        <input type="text" id="nombre" name="nombre" class="form-input" value="{{ old('nombre', $plan->nombre ?? '') }}" placeholder="p.ej. Plan 3 clases/semana" required>
        @error('nombre') <span class="form-error">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label for="clases_por_semana">Clases por semana</label>
        <select id="clases_por_semana" name="clases_por_semana" class="form-select" required>
            @foreach (range(1, 7) as $opcion)
                <option value="{{ $opcion }}" {{ (string) old('clases_por_semana', $plan->clases_por_semana ?? '') === (string) $opcion ? 'selected' : '' }}>
                    {{ $opcion }} {{ $opcion === 1 ? 'clase' : 'clases' }} por semana
                </option>
            @endforeach
        </select>
        @error('clases_por_semana') <span class="form-error">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label for="precio">Precio mensual</label>
        <input type="number" id="precio" name="precio" class="form-input" min="0" step="0.01" value="{{ old('precio', $plan->precio ?? '') }}" placeholder="Opcional">
        @error('precio') <span class="form-error">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label>Disponibilidad</label>
        <div class="checkbox-row">
            <input type="hidden" name="activo" value="0">
            <input type="checkbox" id="activo" name="activo" value="1" {{ old('activo', $plan->activo ?? true) ? 'checked' : '' }}>
            <label for="activo" style="font-weight:600; color:var(--text);">Plan activo</label>
        </div>
    </div>

</div>

<div class="form-actions">
    <button type="submit" class="btn btn-primary">{{ $plan ? 'Guardar cambios' : 'Crear plan' }}</button>
    <a href="{{ route('super-admin.planes.index') }}" class="btn btn-outline">Cancelar</a>
</div>
