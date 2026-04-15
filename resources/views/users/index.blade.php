@extends('layouts.app')

@section('title', 'Usuarios')
@section('page-title', 'Gestión de usuarios')
@section('page-description', 'Administra empleados y roles del sistema')

@section('content')
<div class="card content-card">
    <div class="card-body">
        <div class="d-flex justify-content-end mb-4">
            <a href="{{ route('users.create') }}" class="btn btn-primary">Nuevo usuario</a>
        </div>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td><span class="badge text-bg-primary text-capitalize">{{ $user->role }}</span></td>
                        <td>
                            <span class="badge {{ $user->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                {{ $user->is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-outline-primary btn-sm">Editar</a>
                                <form method="POST" action="{{ route('users.destroy', $user) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm" onclick="return confirm('¿Eliminar usuario?')">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No hay usuarios registrados.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{ $users->links() }}
    </div>
</div>
@endsection
