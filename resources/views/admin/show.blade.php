{{-- DOC: Proyecto Cisternas | Vista personalizada de la aplicacion. --}}
@extends('layouts.app')

@section('content')
<div class="sub-topbar mb-3">
    <strong>Detalle de Usuario</strong>
</div>

<div class="card" style="max-width:700px; margin:auto;">
    <div class="card-body">
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="text" class="form-control" value="{{ $user->email }}" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Rol</label>
            <input type="text" class="form-control" value="{{ $user->role }}" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Contraseña</label>
            <div class="input-group">
                <input type="password" id="generated_password" class="form-control" value="{{ $generatedPassword }}" readonly>
                <button type="button" id="btn-toggle-password" class="btn btn-outline-secondary">Ver</button>
            </div>
            <small class="text-muted">Solo visible. No se puede modificar.</small>
        </div>

        <div class="mb-3">
            <label class="form-label">Permisos del rol</label>
            <ul class="mb-0">
                @foreach($capabilities as $capability)
                    <li>{{ $capability }}</li>
                @endforeach
            </ul>
        </div>

        <div class="d-flex gap-2">
            <a class="btn btn-outline-secondary" href="{{ route('admin.users') }}">Volver</a>
        </div>
    </div>
</div>

<script>
const pwdInput = document.getElementById('generated_password');
const btnTogglePwd = document.getElementById('btn-toggle-password');

btnTogglePwd.addEventListener('click', function () {
    pwdInput.type = pwdInput.type === 'password' ? 'text' : 'password';
    btnTogglePwd.textContent = pwdInput.type === 'password' ? 'Ver' : 'Ocultar';
});
</script>
@endsection


