<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $invoice->invoice_number }} | NATADINATTA</title>
    <style>
        @page {
            size: A4;
            margin: 14mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #eef2ff;
            color: #0f172a;
            margin: 0;
            padding: 32px;
        }
        .invoice-shell {
            max-width: 900px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 24px 80px rgba(15, 23, 42, .12);
        }
        .invoice-header {
            background: linear-gradient(135deg, #0f172a, #2563eb);
            color: #ffffff;
            padding: 32px;
        }
        .invoice-body {
            padding: 32px;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }
        .card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 20px;
        }
        .label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #64748b;
            margin-bottom: 8px;
        }
        .value {
            font-size: 16px;
            font-weight: 700;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 24px 0;
        }
        th, td {
            padding: 14px 12px;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
        }
        th {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #64748b;
        }
        .summary {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            margin-top: 24px;
        }
        .summary .card {
            text-align: center;
        }
        .muted {
            color: #64748b;
        }
        .actions {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }
        .button {
            display: inline-block;
            text-decoration: none;
            border-radius: 999px;
            padding: 12px 18px;
            font-weight: 700;
        }
        .button-primary {
            background: #2563eb;
            color: #ffffff;
        }
        .button-secondary {
            background: #e2e8f0;
            color: #0f172a;
        }
        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .invoice-shell {
                box-shadow: none;
                border-radius: 0;
            }
            .actions {
                display: none;
            }
        }
    </style>
</head>
<body>
<div class="invoice-shell">
    <div class="invoice-header">
        <div style="display:flex;justify-content:space-between;gap:24px;align-items:flex-start;">
            <div>
                <div style="font-size:13px;letter-spacing:.1em;text-transform:uppercase;opacity:.8;">Factura</div>
                <h1 style="margin:10px 0 6px;font-size:32px;">{{ $invoice->invoice_number }}</h1>
                <div>{{ $invoice->issue_date->format('Y-m-d') }}</div>
            </div>
            <div style="text-align:right;">
                <div style="font-size:28px;font-weight:800;">{{ $invoice->company_name }}</div>
                <div>{{ $invoice->company_address }}</div>
                <div>{{ $invoice->company_city }}</div>
                <div>NIT {{ $invoice->company_nit }}</div>
            </div>
        </div>
    </div>

    <div class="invoice-body">
        <div class="grid">
            <div class="card">
                <div class="label">Cliente</div>
                <div class="value">{{ $invoice->client_name }}</div>
                <div class="muted">Documento: {{ $invoice->client_document ?: 'N/A' }}</div>
                <div class="muted">Telefono: {{ $invoice->client_phone ?: 'N/A' }}</div>
            </div>
            <div class="card">
                <div class="label">Estado</div>
                <div class="value" style="text-transform:capitalize;">{{ $invoice->payment_status }}</div>
                <div class="muted">Factura: {{ $invoice->status }}</div>
                <div class="muted">Pedido: {{ $invoice->order->order_number }}</div>
            </div>
        </div>

        <table>
            <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio</th>
                <th>Total</th>
            </tr>
            </thead>
            <tbody>
            @foreach($invoice->order->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ \App\Support\Currency::cop($item->unit_price) }}</td>
                    <td>{{ \App\Support\Currency::cop($item->line_total) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="summary">
            <div class="card">
                <div class="label">Total factura</div>
                <div class="value">{{ \App\Support\Currency::cop($invoice->total) }}</div>
            </div>
            <div class="card">
                <div class="label">Total abonado</div>
                <div class="value">{{ \App\Support\Currency::cop($invoice->total_paid) }}</div>
            </div>
            <div class="card">
                <div class="label">Saldo pendiente</div>
                <div class="value">{{ \App\Support\Currency::cop($invoice->balance_due) }}</div>
            </div>
        </div>

        <div class="card" style="margin-top:24px;">
            <div class="label">Historial de abonos</div>
            @if($invoice->payments->isEmpty())
                <div class="muted">No hay abonos registrados en esta factura.</div>
            @else
                <table style="margin:0;">
                    <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Monto</th>
                        <th>Nota</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($invoice->payments as $payment)
                        <tr>
                            <td>{{ $payment->payment_date->format('Y-m-d') }}</td>
                            <td>{{ \App\Support\Currency::cop($payment->amount) }}</td>
                            <td>{{ $payment->notes ?: 'Sin nota' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        @unless($downloadMode ?? false)
            <div class="actions">
                <button class="button button-primary" onclick="window.print()">Imprimir factura</button>
                <a href="{{ route('invoices.pdf', $invoice) }}" class="button button-primary">Descargar PDF</a>
                <a href="{{ route('orders.show', $invoice->order) }}" class="button button-secondary">Volver al pedido</a>
            </div>
        @endunless
    </div>
</div>
</body>
</html>
