<table border="1">
    <tr>
        <th colspan="6">Cuentas por cobrar NATADINATTA</th>
    </tr>
    <tr>
        <td>Desde</td>
        <td>{{ $from }}</td>
        <td>Hasta</td>
        <td>{{ $to }}</td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <th>Factura</th>
        <th>Cliente</th>
        <th>Fecha</th>
        <th>Total</th>
        <th>Abonado</th>
        <th>Saldo</th>
    </tr>
    @foreach($receivables as $invoice)
        <tr>
            <td>{{ $invoice->invoice_number }}</td>
            <td>{{ $invoice->client_name }}</td>
            <td>{{ $invoice->issue_date->format('Y-m-d') }}</td>
            <td>{{ $invoice->total }}</td>
            <td>{{ $invoice->total_paid }}</td>
            <td>{{ $invoice->balance_due }}</td>
        </tr>
    @endforeach
</table>
