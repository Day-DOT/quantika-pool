<div class="form-grid">

    @if($nivel?->imagen)
        <div class="form-group full" style="align-items:center;">
            <img src="{{ asset($nivel->imagen) }}" alt="{{ $nivel->nombre }}" style="width:60px; height:60px; object-fit:contain;">
        </div>
    @endif

    <div class="form-group">
        <label for="orden">Orden</label>
        <input type="number" id="orden" name="orden" class="form-input" min="1" value="{{ old('orden', $nivel->orden ?? '') }}" required>
        @error('orden') <span class="form-error">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label for="nombre">Nombre</label>
        <input type="text" id="nombre" name="nombre" class="form-input" value="{{ old('nombre', $nivel->nombre ?? '') }}" required>
        @error('nombre') <span class="form-error">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label for="categoria_edad">Grupo de edad</label>
        <select id="categoria_edad" name="categoria_edad" class="form-select" required>
            @foreach (\App\Models\Nivel::CATEGORIAS_EDAD as $opcion)
                <option value="{{ $opcion }}" {{ old('categoria_edad', $nivel->categoria_edad ?? 'Niños') === $opcion ? 'selected' : '' }}>
                    {{ $opcion }}
                </option>
            @endforeach
        </select>
        @error('categoria_edad') <span class="form-error">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label for="categoria">Categoría (nivel de habilidad)</label>
        <input type="text" id="categoria" name="categoria" class="form-input" value="{{ old('categoria', $nivel->categoria ?? '') }}" placeholder="Principiante, Intermedio, Avanzado..." required>
        @error('categoria') <span class="form-error">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label for="total_sub_niveles">Sub-niveles</label>
        <input type="number" id="total_sub_niveles" name="total_sub_niveles" class="form-input" min="1" max="26" value="{{ old('total_sub_niveles', $nivel->total_sub_niveles ?? 1) }}">
        <small>P.ej. 3 divide este nivel en A, B y C antes de pasar al siguiente nivel. Deja 1 para no dividirlo.</small>
        @error('total_sub_niveles') <span class="form-error">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label for="color_hex">Color distintivo</label>
        <input type="color" id="color_hex" name="color_hex" class="form-input" style="padding:4px;" value="{{ old('color_hex', $nivel->color_hex ?? '#42d8ef') }}">
        @error('color_hex') <span class="form-error">{{ $message }}</span> @enderror
    </div>

    <div class="form-group full">
        <label for="descripcion">Descripción</label>
        <textarea id="descripcion" name="descripcion" class="form-textarea">{{ old('descripcion', $nivel->descripcion ?? '') }}</textarea>
        @error('descripcion') <span class="form-error">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label for="imagen">Imagen / ícono</label>
        <input type="file" id="imagen" name="imagen" class="form-input" accept="image/*">
        @error('imagen') <span class="form-error">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label>Disponibilidad</label>
        <div class="checkbox-row">
            <input type="hidden" name="activo" value="0">
            <input type="checkbox" id="activo" name="activo" value="1" {{ old('activo', $nivel->activo ?? true) ? 'checked' : '' }}>
            <label for="activo" style="font-weight:600; color:var(--text);">Nivel activo</label>
        </div>
    </div>

</div>

<div class="form-actions">
    <button type="submit" class="btn btn-primary">{{ $nivel ? 'Guardar cambios' : 'Crear nivel' }}</button>
    <a href="{{ route('niveles.index') }}" class="btn btn-outline">Cancelar</a>
</div>
