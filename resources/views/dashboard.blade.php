@extends('layouts.app')

@section('title', 'Panel principal')
@section('page-title', 'Panel principal')
@section('page-description', 'Resumen operativo de ventas, recaudo y actividad reciente')

@section('content')
<div class="row g-3 g-lg-4 mb-4">
    <div class="col-12 col-md-4">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="text-muted">Ventas del día</div>
                <div class="display-6 fw-bold">{{ \App\Support\Currency::cop($salesToday) }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="text-muted">Saldo pendiente</div>
                <div class="display-6 fw-bold">{{ \App\Support\Currency::cop($pendingBalance) }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="text-muted">Pedidos de hoy</div>
                <div class="display-6 fw-bold">{{ $ordersToday }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card content-card mb-4">
    <div class="card-header bg-white d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
            <h5 class="mb-1">Resumen por períodos</h5>
            <div class="text-muted small">Comparativo rápido sin gráficas</div>
        </div>
        <a href="{{ route('orders.create') }}" class="btn btn-primary">Nueva venta</a>
    </div>
    <div class="card-body">
        <div class="row g-3">
            @foreach($periodSummaries as $summary)
                <div class="col-12 col-xl-4">
                    <div class="border rounded-4 p-4 h-100 bg-light-subtle">
                        <div class="text-muted text-uppercase small mb-2">{{ $summary['label'] }}</div>
                        <div class="fw-semibold mb-3">Ventas: {{ \App\Support\Currency::cop($summary['sales_total']) }}</div>
                        <div class="small mb-2">Abonos registrados: {{ \App\Support\Currency::cop($summary['payments_total']) }}</div>
                        <div class="small mb-2">Pedidos creados: {{ $summary['orders_count'] }}</div>
                        <div class="small">Ticket promedio: {{ \App\Support\Currency::cop($summary['avg_ticket']) }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-12 col-xl-7">
        <div class="card content-card h-100">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0">Transacciones recientes</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle table-mobile-tight">
                        <thead>
                        <tr>
                            <th>Pedido</th>
                            <th>Cliente</th>
                            <th>Estado</th>
                            <th>Total</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($recentOrders as $order)
                            <tr>
                                <td>{{ $order->order_number }}</td>
                                <td>{{ $order->client->name }}</td>
                                <td><span class="badge text-bg-secondary text-capitalize">{{ $order->status }}</span></td>
                                <td>{{ \App\Support\Currency::cop($order->total) }}</td>
                                <td class="text-end"><a href="{{ route('orders.show', $order) }}" class="btn btn-outline-primary btn-sm">Ver</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">No hay pedidos recientes.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-5">
        <div class="card content-card h-100">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0">Facturas y abonos</h5>
            </div>
            <div class="card-body">
                @forelse($recentPayments as $invoice)
                    <div class="border rounded-4 p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <div>
                                <div class="fw-semibold">{{ $invoice->invoice_number }}</div>
                                <div class="text-muted small">{{ $invoice->client_name }}</div>
                            </div>
                            <span class="badge text-bg-info text-capitalize">{{ $invoice->payment_status }}</span>
                        </div>
                        <div class="small mt-2">Total: {{ \App\Support\Currency::cop($invoice->total) }}</div>
                        <div class="small">Abonado: {{ \App\Support\Currency::cop($invoice->total_paid) }}</div>
                        <div class="small">Saldo: {{ \App\Support\Currency::cop($invoice->balance_due) }}</div>
                    </div>
                @empty
                    <p class="text-muted mb-0">Aún no hay facturas confirmadas.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
