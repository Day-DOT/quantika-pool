<div class="form-grid">

    <div class="form-group">
        <label for="nivel_id">Nivel</label>
        <select id="nivel_id" name="nivel_id" class="form-select" required>
            @foreach ($niveles as $nivel)
                <option value="{{ $nivel->id }}" {{ (string) old('nivel_id', $criterio->nivel_id ?? request('nivel_id', '')) === (string) $nivel->id ? 'selected' : '' }}>
                    {{ str_pad((string) $nivel->orden, 2, '0', STR_PAD_LEFT) }} · {{ $nivel->nombre }}
                </option>
            @endforeach
        </select>
        @error('nivel_id') <span class="form-error">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label for="orden">Orden</label>
        <input type="number" id="orden" name="orden" class="form-input" min="0" value="{{ old('orden', $criterio->orden ?? 0) }}">
        @error('orden') <span class="form-error">{{ $message }}</span> @enderror
    </div>

    <div class="form-group full">
        <label for="nombre">Nombre del criterio</label>
        <input type="text" id="nombre" name="nombre" class="form-input" value="{{ old('nombre', $criterio->nombre ?? '') }}" required>
        @error('nombre') <span class="form-error">{{ $message }}</span> @enderror
    </div>

    <div class="form-group full">
        <label for="descripcion">Descripción</label>
        <textarea id="descripcion" name="descripcion" class="form-textarea">{{ old('descripcion', $criterio->descripcion ?? '') }}</textarea>
        @error('descripcion') <span class="form-error">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label>Disponibilidad</label>
        <div class="checkbox-row">
            <input type="hidden" name="activo" value="0">
            <input type="checkbox" id="activo" name="activo" value="1" {{ old('activo', $criterio->activo ?? true) ? 'checked' : '' }}>
            <label for="activo" style="font-weight:600; color:var(--text);">Criterio activo</label>
        </div>
    </div>

</div>

<div class="form-actions">
    <button type="submit" class="btn btn-primary">{{ $criterio ? 'Guardar cambios' : 'Crear criterio' }}</button>
    <a href="{{ route('super-admin.criterios.index') }}" class="btn btn-outline">Cancelar</a>
</div>
