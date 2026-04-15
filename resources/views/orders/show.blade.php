@extends('layouts.app')

@section('title', 'Detalle de venta')
@section('page-title', 'Detalle de venta')
@section('page-description', 'Consulta pedido, factura y estado de pagos')

@section('content')
<div class="row g-4">
    <div class="col-12 col-xl-8">
        <div class="card content-card mb-4">
            <div class="card-header bg-white border-0 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <div class="fw-bold">{{ $order->order_number }}</div>
                    <div class="text-muted small">{{ $order->client->name }} | {{ $order->order_date->format('Y-m-d') }}</div>
                </div>
                <span class="badge text-bg-secondary text-capitalize">{{ $order->status }}</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle table-mobile-tight">
                        <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio</th>
                            <th>Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td>{{ $item->product_name }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ \App\Support\Currency::cop($item->unit_price) }}</td>
                                <td>{{ \App\Support\Currency::cop($item->line_total) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="3" class="text-end">Total</th>
                            <th>{{ \App\Support\Currency::cop($order->total) }}</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>

                @if($order->notes)
                    <div class="alert alert-light mt-3 mb-0">{{ $order->notes }}</div>
                @endif
            </div>
        </div>

        @if($order->invoice)
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
                                <th>Monto</th>
                                <th>Observación</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($order->invoice->payments as $payment)
                                <tr>
                                    <td>{{ $payment->payment_date->format('Y-m-d') }}</td>
                                    <td>{{ \App\Support\Currency::cop($payment->amount) }}</td>
                                    <td>{{ $payment->notes ?: 'Sin observación' }}</td>
                                    <td class="text-end">
                                        @if(auth()->user()->isAdmin() || $payment->recorded_by === auth()->id())
                                            <div class="action-group justify-content-end">
                                                <a href="{{ route('payments.edit', $payment) }}" class="btn btn-outline-primary btn-sm">Editar</a>
                                                <form method="POST" action="{{ route('payments.destroy', $payment) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-outline-danger btn-sm" onclick="return confirm('¿Eliminar abono?')">Eliminar</button>
                                                </form>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-4">Aún no hay abonos registrados.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="col-12 col-xl-4">
        <div class="card content-card mb-4">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0">Resumen de factura</h5>
            </div>
            <div class="card-body">
                @if($order->invoice)
                    <div class="mb-3">
                        <div class="fw-semibold">{{ $order->invoice->invoice_number }}</div>
                        <div class="text-muted small">Estado: {{ $order->invoice->status }}</div>
                    </div>
                    <div class="mb-2">Total: <strong>{{ \App\Support\Currency::cop($order->invoice->total) }}</strong></div>
                    <div class="mb-2">Abonado: <strong>{{ \App\Support\Currency::cop($order->invoice->total_paid) }}</strong></div>
                    <div class="mb-3">Saldo: <strong>{{ \App\Support\Currency::cop($order->invoice->balance_due) }}</strong></div>
                    <span class="badge text-bg-info text-capitalize mb-3">{{ $order->invoice->payment_status }}</span>

                    @if($order->invoice->status === 'activa' && $order->invoice->balance_due > 0)
                        <form method="POST" action="{{ route('invoices.payments.store', $order->invoice) }}" class="d-grid gap-3 mt-3">
                            @csrf
                            <div>
                                <label class="form-label">Fecha del abono</label>
                                <input type="date" name="payment_date" class="form-control" value="{{ now()->toDateString() }}" required>
                            </div>
                            <div>
                                <label class="form-label">Monto</label>
                                <input type="number" min="1" step="0.01" name="amount" class="form-control" required>
                            </div>
                            <div>
                                <label class="form-label">Observación</label>
                                <textarea name="notes" class="form-control" rows="2"></textarea>
                            </div>
                            <button class="btn btn-primary">Registrar abono</button>
                        </form>
                    @endif
                @else
                    <p class="text-muted">Este pedido aún no ha sido confirmado como factura.</p>
                @endif
            </div>
        </div>

        <div class="card content-card">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0">Acciones</h5>
            </div>
            <div class="card-body d-grid gap-2">
                @if($order->status === 'pendiente')
                    <a href="{{ route('orders.edit', $order) }}" class="btn btn-outline-primary">Editar pedido</a>
                    <form method="POST" action="{{ route('orders.confirm', $order) }}">
                        @csrf
                        <button class="btn btn-success w-100">Confirmar factura</button>
                    </form>
                @endif

                @if($order->invoice)
                    <a href="{{ route('invoices.print', $order->invoice) }}" class="btn btn-outline-dark" target="_blank">Ver factura imprimible</a>
                    <a href="{{ route('invoices.pdf', $order->invoice) }}" class="btn btn-outline-primary">Descargar PDF</a>
                    @if(auth()->user()->isAdmin())
                        <form method="POST" action="{{ route('invoices.destroy', $order->invoice) }}">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-outline-danger w-100" onclick="return confirm('¿Eliminar factura? Esta acción devolverá el pedido a pendiente.')">Eliminar factura</button>
                        </form>
                    @endif
                @endif

                @if($order->status !== 'cancelado')
                    <form method="POST" action="{{ route('orders.cancel', $order) }}">
                        @csrf
                        <button class="btn btn-outline-danger w-100" onclick="return confirm('¿Cancelar este registro?')">Cancelar</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
