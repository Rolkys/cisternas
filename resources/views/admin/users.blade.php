{{-- DOC: Proyecto Cisternas | Vista personalizada de la aplicacion. --}}
@extends('layouts.app')

@section('content')
<div class="sub-topbar mb-3">
    <strong>Gestion de Usuarios</strong>
</div>

<div class="mb-3">
    <a class="btn btn-primary" href="{{ route('admin.users.create') }}">Crear usuario</a>
</div>

@if(session('success'))
<div class="alert alert-success">{!! session('success') !!}</div>
@endif

@if(session('error'))
<div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card">
    <div class="card-body p-0">
        <table class="table table-dark-header table-hover mb-0">
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Activo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $u)
                    <tr>
                        <td>{{ $u->email }}</td>
                        <td><span class="badge bg-dark">{{ $u->role }}</span></td>
                        <td>{{ $u->is_active ? 'SI' : 'NO' }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <a class="btn btn-outline-view btn-sm" href="{{ route('admin.users.show', $u) }}">
                                    <i class="bi bi-eye"></i> Ver
                                </a>

                                <a class="btn btn-outline-warning btn-sm" href="{{ route('admin.users.edit', $u) }}">
                                    <i class="bi bi-pencil"></i> Editar
                                </a>

                                <form method="POST" action="{{ route('admin.users.destroy', $u) }}" onsubmit="return confirm('¿Seguro que quieres eliminar este usuario?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        <i class="bi bi-trash"></i> Eliminar
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
