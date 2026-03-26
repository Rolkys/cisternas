@extends('layouts.app')

@section('content')
<div class="sub-topbar mb-3">
    <strong>Editar Usuario</strong>
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
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PATCH')

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input class="form-control" value="{{ $user->email }}" disabled>
            </div>

            <div class="mb-3">
                <label class="form-label">Rol</label>
                <select name="role" class="form-select">
                    @foreach ($rolesDisponibles as $rol)
                        <option value="{{ $rol }}" {{ $user->role === $rol ? 'selected' : '' }}>{{ $rol }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-check mb-3">
                <input type="hidden" name="is_active" value="0">
                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ $user->is_active ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">Cuenta activa</label>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <a class="btn btn-outline-secondary" href="{{ route('admin.users') }}">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
