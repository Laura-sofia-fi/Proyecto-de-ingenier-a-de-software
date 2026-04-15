@extends('layouts.app')

@section('title', 'Catálogo de productos')
@section('page-title', 'Catálogo de productos')
@section('page-description', 'Productos y precios base para ventas al por mayor')

@section('content')
<div class="card content-card">
    <div class="card-body">
        <div class="d-flex flex-column flex-lg-row gap-3 justify-content-between mb-4">
            <form class="d-flex flex-column flex-sm-row gap-2 toolbar-stack" method="GET">
                <input type="text" class="form-control" name="search" placeholder="Buscar producto o SKU..." value="{{ $search }}">
                <button class="btn btn-outline-secondary">Buscar</button>
            </form>
            <a href="{{ route('products.create') }}" class="btn btn-primary">Nuevo producto</a>
        </div>

        <div class="table-responsive">
            <table class="table align-middle table-mobile-tight">
                <thead>
                <tr>
                    <th>Producto</th>
                    <th>SKU</th>
                    <th>Precio</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $product->name }}</div>
                            <div class="small text-muted">{{ $product->description ?: 'Sin descripción' }}</div>
                        </td>
                        <td>{{ $product->sku }}</td>
                        <td>{{ \App\Support\Currency::cop($product->price) }}</td>
                        <td>
                            <span class="badge {{ $product->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                {{ $product->is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="action-group justify-content-end">
                                <a href="{{ route('products.edit', $product) }}" class="btn btn-outline-primary btn-sm">Editar</a>
                                <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('¿Eliminar este producto del catálogo?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No hay productos registrados.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{ $products->links() }}
    </div>
</div>
@endsection
