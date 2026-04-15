@extends('layouts.app')

@section('title', 'Historial del cliente')
@section('page-title', 'Historial del cliente')
@section('page-description', 'Compras, facturas, abonos y saldo pendiente')

@section('content')
<div class="card content-card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end filter-grid">
            <div class="col-12 col-md-4">
                <label class="form-label">Desde</label>
                <input type="date" name="from" class="form-control" value="{{ $from }}">
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label">Hasta</label>
                <input type="date" name="to" class="form-control" value="{{ $to }}">
            </div>
            <div class="col-12 col-md-4">
                <button class="btn btn-outline-secondary w-100">Filtrar historial</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-12 col-xl-4">
        <div class="card content-card h-100">
            <div class="card-body">
                <div class="text-muted small mb-2">Cliente</div>
                <h3 class="fw-bold mb-1">{{ $client->name }}</h3>
                <div class="text-muted mb-1">Documento: {{ $client->document_number ?: 'N/A' }}</div>
                <div class="text-muted mb-1">Teléfono: {{ $client->phone ?: 'N/A' }}</div>
                <div class="text-muted mb-1">Correo: {{ $client->email ?: 'N/A' }}</div>
                <div class="text-muted mb-1">Dirección: {{ $client->address ?: 'N/A' }}</div>
                <div class="text-muted">Ciudad: {{ $client->city ?: 'N/A' }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-xl-8">
        <div class="row g-3">
            <div class="col-12 col-md-4">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="text-muted">Facturado</div>
                        <div class="h3 fw-bold">{{ \App\Support\Currency::cop($metrics['total_billed']) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="text-muted">Abonado</div>
                        <div class="h3 fw-bold">{{ \App\Support\Currency::cop($metrics['total_paid']) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="text-muted">Saldo pendiente</div>
                        <div class="h3 fw-bold">{{ \App\Support\Currency::cop($metrics['balance_due']) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card content-card h-100">
                    <div class="card-body">
                        <div class="text-muted">Pedidos registrados</div>
                        <div class="display-6 fw-bold">{{ $metrics['orders_count'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card content-card h-100">
                    <div class="card-body">
                        <div class="text-muted">Facturas emitidas</div>
                        <div class="display-6 fw-bold">{{ $metrics['invoices_count'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card content-card mb-4">
    <div class="card-header bg-white border-0">
        <h5 class="mb-0">Pedidos y facturas</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle table-mobile-tight">
                <thead>
                <tr>
                    <th>Pedido</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Factura</th>
                    <th>Total</th>
                    <th>Saldo</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>{{ $order->order_number }}</td>
                        <td>{{ $order->order_date->format('Y-m-d') }}</td>
                        <td><span class="badge text-bg-secondary text-capitalize">{{ $order->status }}</span></td>
                        <td>{{ $order->invoice?->invoice_number ?: 'Sin factura' }}</td>
                        <td>{{ \App\Support\Currency::cop($order->total) }}</td>
                        <td>{{ \App\Support\Currency::cop($order->invoice?->balance_due ?? 0) }}</td>
                        <td class="text-end"><a href="{{ route('orders.show', $order) }}" class="btn btn-outline-primary btn-sm">Ver</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No hay pedidos en el rango seleccionado.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card content-card">
    <div class="card-header bg-white border-0">
        <h5 class="mb-0">Historial de abonos</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle table-mobile-tight">
                <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Factura</th>
                    <th>Monto</th>
                    <th>Registrado por</th>
                    <th>Nota</th>
                </tr>
                </thead>
                <tbody>
                @forelse($payments as $payment)
                    <tr>
                        <td>{{ $payment->payment_date->format('Y-m-d') }}</td>
                        <td>{{ $payment->invoice->invoice_number }}</td>
                        <td>{{ \App\Support\Currency::cop($payment->amount) }}</td>
                        <td>{{ $payment->recorder->name }}</td>
                        <td>{{ $payment->notes ?: 'Sin nota' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No hay abonos registrados en el rango seleccionado.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
