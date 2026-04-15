<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Nombre</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Correo</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Teléfono</label>
        <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Rol</label>
        <select name="role" class="form-select" required>
            @foreach(['admin' => 'Administrador', 'empleado' => 'Empleado'] as $value => $label)
                <option value="{{ $value }}" @selected(old('role', $user->role ?: 'empleado') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Contraseña</label>
        <input type="password" name="password" class="form-control" {{ $user->exists ? '' : 'required' }}>
    </div>
    <div class="col-md-6">
        <label class="form-label">Confirmar contraseña</label>
        <input type="password" name="password_confirmation" class="form-control" {{ $user->exists ? '' : 'required' }}>
    </div>
    <div class="col-12">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $user->exists ? $user->is_active : true))>
            <label class="form-check-label" for="is_active">Usuario activo</label>
        </div>
    </div>
</div>
