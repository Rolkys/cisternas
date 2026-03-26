@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0"><i class="bi bi-people"></i> Gestión de Usuarios</h4>
    <a href="{{ route('cisterna.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

{{-- Mensaje de éxito con contraseña visible --}}
@if(session('success'))
    @php $msg = session('success'); @endphp
    @if(str_contains($msg, 'Contraseña:'))
        @php
            preg_match('/Contraseña: (\S+)/', $msg, $matches);
            $pass  = $matches[1] ?? '';
            $texto = explode('Contraseña:', $msg)[0];
        @endphp
        <div class="alert alert-success">
            {{ $texto }}
            <hr class="my-2">
            <div class="d-flex align-items-center gap-2">
                <strong>Contraseña generada:</strong>
                <code class="fs-5 bg-light px-3 py-1 rounded border">{{ $pass }}</code>
            </div>
            <small class="text-muted">Anota esta contraseña, no se volverá a mostrar.</small>
        </div>
    @else
        <div class="alert alert-success alert-dismissible fade show">
            {{ $msg }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Formulario crear usuario --}}
<div class="card shadow-sm mb-4">
    <div class="card-header fw-bold">Nuevo usuario</div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.users.store') }}" id="form-crear">
            @csrf
            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif
            <div class="row g-3 align-items-end">

                <div class="col-md-4">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control"
                            value="{{ old('email') }}" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Contraseña generada</label>
                    <input type="text" name="password_generada" id="password_generada"
                            class="form-control bg-light" readonly
                            placeholder="Pulsa 🔑 para generar">
                </div>

                <div class="col-auto d-flex align-items-end">
                    {{-- generarPassword() definido en public/js/app-custom.js --}}
                    <button type="button" class="btn btn-outline-secondary"
                            onclick="generarPassword()" title="Generar contraseña">
                        🔑 Generar
                    </button>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Rol</label>
                    <select name="role" class="form-select">
                        <option value="operario">Operario</option>
                        <option value="Usuario">Usuario</option>
                        <option value="Administrador">Administrador</option>
                        @if(auth()->user()->role === 'Root')
                            <option value="Root">Root</option>
                        @endif
                    </select>
                </div>

                <div class="col-auto d-flex align-items-end">
                    <button type="submit" class="btn btn-primary" id="btn-crear" disabled>
                        <i class="bi bi-plus-lg"></i> Crear
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

{{-- Tabla de usuarios --}}
<div class="card shadow-sm">
    <div class="card-header fw-bold">Usuarios registrados</div>
    <div class="card-body p-0">
        <table class="table table-bordered table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Registro</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr class="{{ !$user->is_active ? 'table-secondary text-muted' : '' }}">
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @if(auth()->user()->role === 'Root' && $user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.role', $user) }}">
                                @csrf
                                @method('PATCH')
                                <div class="input-group input-group-sm">
                                    <select name="role" class="form-select form-select-sm">
                                        <option value="operario"     {{ $user->role == 'operario'     ? 'selected' : '' }}>Operario</option>
                                        <option value="Usuario"      {{ $user->role == 'Usuario'      ? 'selected' : '' }}>Usuario</option>
                                        <option value="Administrador"{{ $user->role == 'Administrador'? 'selected' : '' }}>Administrador</option>
                                        <option value="Root"         {{ $user->role == 'Root'         ? 'selected' : '' }}>Root</option>
                                    </select>
                                    <button class="btn btn-outline-secondary btn-sm" type="submit">
                                        <i class="bi bi-check"></i>
                                    </button>
                                </div>
                            </form>
                        @else
                            <span class="badge
                                @if($user->role === 'Root')          bg-danger
                                @elseif($user->role === 'Administrador') bg-warning text-dark
                                @elseif($user->role === 'operario')  bg-info
                                @else                                bg-secondary
                                @endif">
                                {{ $user->role === 'operario' ? 'Operario' : $user->role }}
                            </span>
                        @endif
                    </td>
                    <td>
                        <form method="POST" action="{{ route('admin.users.toggle', $user) }}">
                            @csrf
                            @method('PATCH')
                            <button class="btn btn-sm {{ $user->is_active ? 'btn-success' : 'btn-outline-secondary' }}"
                                    {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                {{ $user->is_active ? '✅ Activo' : '❌ Inactivo' }}
                            </button>
                        </form>
                    </td>
                    <td class="small">
                        {{ $user->fecha_registro ? date('d/m/Y', strtotime($user->fecha_registro)) : '—' }}
                    </td>
                    <td>
                        @if(auth()->user()->role === 'Root' && $user->id !== auth()->id())
                            <form method="POST"
                                    action="{{ route('admin.users.destroy', $user) }}"
                                    onsubmit="return confirm('¿Eliminar este usuario?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
{{-- generarPassword() y toggleTodos() ya están en public/js/app-custom.js --}}