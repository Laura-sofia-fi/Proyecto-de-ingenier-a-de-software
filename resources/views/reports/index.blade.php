@extends('layouts.app')

@section('title', 'Reportes')
@section('page-title', 'Reportes de ventas')
@section('page-description', 'Indicadores clave, productos más vendidos y saldos pendientes')

@section('content')
<div class="card content-card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Desde</label>
                <input type="date" name="from" class="form-control" value="{{ $from }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Hasta</label>
                <input type="date" name="to" class="form-control" value="{{ $to }}">
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button class="btn btn-primary">Filtrar</button>
                <a href="{{ route('reports.export.excel', ['from' => $from, 'to' => $to]) }}" class="btn btn-outline-success">Excel</a>
                <a href="{{ route('reports.export.pdf', ['from' => $from, 'to' => $to]) }}" class="btn btn-outline-danger">PDF</a>
            </div>
        </form>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card stat-card">
            <div class="card-body">
                <div class="text-muted">Total vendido</div>
                <div class="display-6 fw-bold">{{ \App\Support\Currency::cop($totalSales) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card stat-card">
            <div class="card-body">
                <div class="text-muted">Saldo pendiente</div>
                <div class="display-6 fw-bold">{{ \App\Support\Currency::cop($pendingBalance) }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card content-card h-100">
            <div class="card-header bg-white border-0"><h5 class="mb-0">Productos más vendidos</h5></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Unidades</th>
                            <th>Valor</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($topProducts as $product)
                            <tr>
                                <td>{{ $product->product_name }}</td>
                                <td>{{ $product->total_quantity }}</td>
                                <td>{{ \App\Support\Currency::cop($product->total_amount) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-4">Sin datos en el rango seleccionado.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card content-card h-100">
            <div class="card-header bg-white border-0"><h5 class="mb-0">Facturas con saldo pendiente</h5></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Factura</th>
                            <th>Cliente</th>
                            <th>Saldo</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($pendingInvoices as $invoice)
                            <tr>
                                <td>{{ $invoice->invoice_number }}</td>
                                <td>{{ $invoice->client_name }}</td>
                                <td>{{ \App\Support\Currency::cop($invoice->balance_due) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-4">No hay saldos pendientes.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
