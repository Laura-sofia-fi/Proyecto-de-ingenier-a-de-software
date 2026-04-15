@extends('layouts.app')

@section('title', 'Ventas y facturas')
@section('page-title', 'Ventas y facturas')
@section('page-description', 'Control de pedidos, confirmación de facturas y seguimiento de pagos')

@section('content')
<div class="card content-card">
    <div class="card-body">
        <div class="d-flex flex-column gap-3 mb-4">
            <form class="row g-2 align-items-end filter-grid" method="GET">
                <div class="col-12 col-xl-4">
                    <label class="form-label">Buscar</label>
                    <input type="text" class="form-control" name="search" placeholder="Pedido, factura o cliente..." value="{{ $search }}">
                </div>
                <div class="col-6 col-md-3 col-xl-2">
                    <label class="form-label">Estado</label>
                    <select class="form-select" name="status">
                        <option value="">Todos</option>
                        @foreach(['pendiente', 'confirmado', 'cancelado'] as $itemStatus)
                            <option value="{{ $itemStatus }}" @selected($status === $itemStatus)>{{ ucfirst($itemStatus) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3 col-xl-2">
                    <label class="form-label">Desde</label>
                    <input type="date" class="form-control" name="from" value="{{ $from }}">
                </div>
                <div class="col-6 col-md-3 col-xl-2">
                    <label class="form-label">Hasta</label>
                    <input type="date" class="form-control" name="to" value="{{ $to }}">
                </div>
                <div class="col-12 col-md-6 col-xl-2">
                    <div class="d-flex flex-column flex-sm-row gap-2 toolbar-stack">
                        <button class="btn btn-outline-secondary">Filtrar</button>
                        <a href="{{ route('orders.create') }}" class="btn btn-primary">Nueva venta</a>
                    </div>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table align-middle table-mobile-tight">
                <thead>
                <tr>
                    <th>Pedido</th>
                    <th>Cliente</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Total</th>
                    <th>Factura</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>{{ $order->order_number }}</td>
                        <td>{{ $order->client->name }}</td>
                        <td>{{ $order->order_date->format('Y-m-d') }}</td>
                        <td><span class="badge text-bg-secondary text-capitalize">{{ $order->status }}</span></td>
                        <td>{{ \App\Support\Currency::cop($order->total) }}</td>
                        <td>{{ $order->invoice?->invoice_number ?: 'Sin confirmar' }}</td>
                        <td class="text-end">
                            <div class="action-group justify-content-end">
                                <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-primary btn-sm">Ver detalle</a>
                                @if(auth()->user()?->isAdmin())
                                    <form method="POST" action="{{ route('orders.destroy', $order) }}" onsubmit="return confirm('¿Eliminar esta venta y su factura relacionada? Esta acción también la borrará de la base de datos.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm">Eliminar</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No hay ventas registradas con esos filtros.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{ $orders->links() }}
    </div>
</div>
@endsection
