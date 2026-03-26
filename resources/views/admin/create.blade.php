@extends('layouts.app')

@section('content')
<div class="sub-topbar mb-3">
    <strong>Crear Usuario</strong>
</div>

@if($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="card" style="max-width:500px; margin:auto;">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Rol</label>
                <select name="role" class="form-select" required>
                    @foreach ($rolesDisponibles as $rol)
                        <option value="{{ $rol }}" {{ old('role', 'Usuario') === $rol ? 'selected' : '' }}>{{ $rol }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Contrasena generada</label>
                <div class="input-group">
                    <input
                        type="password"
                        id="password_generada"
                        name="password_generada"
                        class="form-control"
                        value="{{ old('password_generada') }}"
                        readonly
                        required
                    >
                    <button type="button" id="btn-toggle-pwd" class="btn btn-outline-secondary">Ver</button>
                    <button type="button" id="btn-generar-pwd" class="btn btn-outline-primary">Generar</button>
                </div>
                <small class="text-muted">No editable. Debes pulsar "Generar" antes de crear el usuario.</small>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" id="btn-crear" class="btn btn-primary" disabled>Crear</button>
                <a class="btn btn-outline-secondary" href="{{ route('admin.users') }}">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
function generarPasswordDesdeEmail(email) {
    const local = (email || '').split('@')[0] || '';
    if (!local) return '';

    const upper = local.toUpperCase();
    const firstAscii = upper.charCodeAt(0);
    const lastAscii = upper.charCodeAt(upper.length - 1);

    return `${upper}${firstAscii}${lastAscii}`;
}

const emailInput = document.querySelector('input[name="email"]');
const passwordInput = document.getElementById('password_generada');
const btnGenerar = document.getElementById('btn-generar-pwd');
const btnToggle = document.getElementById('btn-toggle-pwd');
const btnCrear = document.getElementById('btn-crear');

btnGenerar.addEventListener('click', function () {
    const pass = generarPasswordDesdeEmail(emailInput.value.trim());
    if (!pass) {
        alert('Introduce un email valido antes de generar la contrasena.');
        return;
    }

    passwordInput.value = pass;
    passwordInput.type = 'text';
    btnToggle.textContent = 'Ocultar';
    btnCrear.disabled = false;
});

btnToggle.addEventListener('click', function () {
    if (!passwordInput.value) return;
    passwordInput.type = passwordInput.type === 'password' ? 'text' : 'password';
    btnToggle.textContent = passwordInput.type === 'password' ? 'Ver' : 'Ocultar';
});

emailInput.addEventListener('input', function () {
    passwordInput.value = '';
    passwordInput.type = 'password';
    btnToggle.textContent = 'Ver';
    btnCrear.disabled = true;
});

if (passwordInput.value) {
    btnCrear.disabled = false;
}
</script>
@endsection
