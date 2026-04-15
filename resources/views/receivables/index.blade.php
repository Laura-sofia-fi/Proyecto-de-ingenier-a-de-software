@extends('layouts.app')

@section('title', 'Cuentas por cobrar')
@section('page-title', 'Cuentas por cobrar')
@section('page-description', 'Seguimiento global de facturas pendientes y abonos parciales')

@section('content')
<div class="row g-3 g-lg-4 mb-4">
    <div class="col-12 col-md-4">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="text-muted">Saldo total pendiente</div>
                <div class="display-6 fw-bold">{{ \App\Support\Currency::cop($metrics['total_pending']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="text-muted">Facturas abiertas</div>
                <div class="display-6 fw-bold">{{ $metrics['open_invoices'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="text-muted">Facturas con abono</div>
                <div class="display-6 fw-bold">{{ $metrics['partial_invoices'] }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card content-card">
    <div class="card-header bg-white d-flex flex-column gap-3">
        <div>
            <h5 class="mb-1">Listado de cartera</h5>
            <div class="text-muted small">Facturas activas con saldo pendiente</div>
        </div>
        <form method="GET" class="row g-2 filter-grid">
            <div class="col-12 col-xl-4">
                <input type="text" name="search" class="form-control" placeholder="Buscar factura o cliente..." value="{{ $search }}">
            </div>
            <div class="col-6 col-md-3 col-xl-2">
                <input type="date" name="from" class="form-control" value="{{ $from }}">
            </div>
            <div class="col-6 col-md-3 col-xl-2">
                <input type="date" name="to" class="form-control" value="{{ $to }}">
            </div>
            <div class="col-12 col-md-6 col-xl-4">
                <div class="d-flex flex-column flex-sm-row gap-2 toolbar-stack">
                    <button class="btn btn-outline-secondary">Filtrar</button>
                    <a href="{{ route('receivables.export.excel', ['search' => $search, 'from' => $from, 'to' => $to]) }}" class="btn btn-outline-success">Excel</a>
                    <a href="{{ route('receivables.export.pdf', ['search' => $search, 'from' => $from, 'to' => $to]) }}" class="btn btn-outline-danger">PDF</a>
                </div>
            </div>
        </form>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle table-mobile-tight">
                <thead>
                <tr>
                    <th>Factura</th>
                    <th>Cliente</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Abonado</th>
                    <th>Saldo</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @forelse($receivables as $invoice)
                    <tr>
                        <td>{{ $invoice->invoice_number }}</td>
                        <td>
                            <div class="fw-semibold">{{ $invoice->client_name }}</div>
                            <div class="small text-muted">{{ $invoice->client_document ?: 'Sin documento' }}</div>
                        </td>
                        <td>{{ $invoice->issue_date->format('Y-m-d') }}</td>
                        <td>{{ \App\Support\Currency::cop($invoice->total) }}</td>
                        <td>{{ \App\Support\Currency::cop($invoice->total_paid) }}</td>
                        <td>{{ \App\Support\Currency::cop($invoice->balance_due) }}</td>
                        <td><span class="badge text-bg-info text-capitalize">{{ $invoice->payment_status }}</span></td>
                        <td class="text-end">
                            <div class="action-group justify-content-end">
                                <a href="{{ route('orders.show', $invoice->order) }}" class="btn btn-outline-primary btn-sm">Ver venta</a>
                                <a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-outline-dark btn-sm">PDF factura</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">No hay cuentas por cobrar pendientes.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{ $receivables->links() }}
    </div>
</div>
@endsection
