@extends('layouts.app')

@section('title', 'Clientes')
@section('page-title', 'Gestión de clientes')
@section('page-description', 'Registro, búsqueda y actualización de clientes')

@section('content')
<div class="card content-card">
    <div class="card-body">
        <div class="d-flex flex-column flex-lg-row gap-3 justify-content-between mb-4">
            <form class="d-flex flex-column flex-sm-row gap-2 toolbar-stack" method="GET">
                <input type="text" class="form-control" name="search" placeholder="Buscar cliente..." value="{{ $search }}">
                <button class="btn btn-outline-secondary">Buscar</button>
            </form>
            <a href="{{ route('clients.create') }}" class="btn btn-primary">Nuevo cliente</a>
        </div>

        <div class="table-responsive">
            <table class="table align-middle table-mobile-tight">
                <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Documento</th>
                    <th>Teléfono</th>
                    <th>Dirección</th>
                    <th>Ciudad</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @forelse($clients as $client)
                    <tr>
                        <td>{{ $client->name }}</td>
                        <td>{{ $client->document_number ?: 'N/A' }}</td>
                        <td>{{ $client->phone ?: 'N/A' }}</td>
                        <td>{{ $client->address ?: 'N/A' }}</td>
                        <td>{{ $client->city ?: 'N/A' }}</td>
                        <td class="text-end">
                            <div class="action-group justify-content-end">
                                <a href="{{ route('clients.show', $client) }}" class="btn btn-outline-dark btn-sm">Historial</a>
                                <a href="{{ route('clients.edit', $client) }}" class="btn btn-outline-primary btn-sm">Editar</a>
                                <form method="POST" action="{{ route('clients.destroy', $client) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm" onclick="return confirm('¿Eliminar cliente?')">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No hay clientes registrados.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{ $clients->links() }}
    </div>
</div>
@endsection
