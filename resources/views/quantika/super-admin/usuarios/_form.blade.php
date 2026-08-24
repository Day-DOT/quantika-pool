{{-- Formulario compartido por create/edit. $usuario es null al crear. --}}

<div class="form-grid">

    <div class="form-group">
        <label for="name">Nombre completo</label>
        <input type="text" id="name" name="name" class="form-input" value="{{ old('name', $usuario->name ?? '') }}" required>
        @error('name') <span class="form-error">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label for="email">Correo electrónico</label>
        <input type="email" id="email" name="email" class="form-input" value="{{ old('email', $usuario->email ?? '') }}" required>
        @error('email') <span class="form-error">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label for="password">{{ $usuario ? 'Nueva contraseña (opcional)' : 'Contraseña' }}</label>
        <input type="password" id="password" name="password" class="form-input" {{ $usuario ? '' : 'required' }}>
        <span class="form-hint">Mínimo 8 caracteres.</span>
        @error('password') <span class="form-error">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label for="telefono">Teléfono</label>
        <input type="text" id="telefono" name="telefono" class="form-input" value="{{ old('telefono', $usuario->telefono ?? '') }}">
        @error('telefono') <span class="form-error">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label for="role">Rol</label>
        <select id="role" name="role" class="form-select" onchange="toggleCamposRol()" required>
            @foreach ($roles as $rol)
                <option value="{{ $rol->value }}" {{ old('role', $usuario->role->value ?? '') === $rol->value ? 'selected' : '' }}>
                    {{ $rol->label() }}
                </option>
            @endforeach
        </select>
        @error('role') <span class="form-error">{{ $message }}</span> @enderror
    </div>

    <div class="form-group" id="grupo-sucursal">
        <label for="sucursal_id">Sucursal base</label>
        <select id="sucursal_id" name="sucursal_id" class="form-select">
            <option value="">— Selecciona —</option>
            @foreach ($sucursales as $s)
                <option value="{{ $s->id }}" {{ (string) old('sucursal_id', $usuario->sucursal_id ?? $usuario->instructor?->sucursal_id ?? '') === (string) $s->id ? 'selected' : '' }}>
                    {{ $s->nombre }}
                </option>
            @endforeach
        </select>
        <span class="form-hint">Aplica a Administrador e Instructor.</span>
        @error('sucursal_id') <span class="form-error">{{ $message }}</span> @enderror
    </div>

    <div class="form-group" id="grupo-especialidad">
        <label for="especialidad">Especialidad (instructor)</label>
        <input type="text" id="especialidad" name="especialidad" class="form-input" value="{{ old('especialidad', $usuario->instructor->especialidad ?? '') }}">
        @error('especialidad') <span class="form-error">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label>Acceso</label>
        <div class="checkbox-row">
            <input type="hidden" name="activo" value="0">
            <input type="checkbox" id="activo" name="activo" value="1" {{ old('activo', $usuario->activo ?? true) ? 'checked' : '' }}>
            <label for="activo" style="font-weight:600; color:var(--text);">Usuario activo</label>
        </div>
    </div>

</div>

<div class="form-actions">
    <button type="submit" class="btn btn-primary">{{ $usuario ? 'Guardar cambios' : 'Crear usuario' }}</button>
    <a href="{{ route('super-admin.usuarios.index') }}" class="btn btn-outline">Cancelar</a>
</div>

<script>
    function toggleCamposRol() {
        const role = document.getElementById('role').value;
        const grupoSucursal = document.getElementById('grupo-sucursal');
        const grupoEspecialidad = document.getElementById('grupo-especialidad');

        grupoSucursal.style.display = (role === 'admin' || role === 'instructor') ? '' : 'none';
        grupoEspecialidad.style.display = (role === 'instructor') ? '' : 'none';
    }

    document.addEventListener('DOMContentLoaded', toggleCamposRol);
</script>
