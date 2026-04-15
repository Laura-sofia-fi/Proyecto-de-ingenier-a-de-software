@extends('layouts.app')

@section('title', 'Editar abono')
@section('page-title', 'Editar abono')
@section('page-description', 'Corrige un abono registrado sin perder el control del saldo')

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card content-card h-100">
            <div class="card-body">
                <div class="text-muted small mb-2">Factura</div>
                <div class="fw-bold mb-1">{{ $payment->invoice->invoice_number }}</div>
                <div class="text-muted mb-1">Cliente: {{ $payment->invoice->client_name }}</div>
                <div class="text-muted mb-1">Total: {{ \App\Support\Currency::cop($payment->invoice->total) }}</div>
                <div class="text-muted mb-1">Abonado: {{ \App\Support\Currency::cop($payment->invoice->total_paid) }}</div>
                <div class="text-muted">Saldo: {{ \App\Support\Currency::cop($payment->invoice->balance_due) }}</div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card content-card">
            <div class="card-body">
                <form method="POST" action="{{ route('payments.update', $payment) }}" class="d-grid gap-3">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Fecha del abono</label>
                            <input type="date" name="payment_date" class="form-control" value="{{ old('payment_date', $payment->payment_date->format('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Monto</label>
                            <input type="number" min="1" step="0.01" name="amount" class="form-control" value="{{ old('amount', $payment->amount) }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Observación</label>
                            <textarea name="notes" class="form-control" rows="3">{{ old('notes', $payment->notes) }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button class="btn btn-primary">Guardar cambios</button>
                        <a href="{{ route('orders.show', $payment->invoice->order) }}" class="btn btn-outline-secondary">Volver</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
