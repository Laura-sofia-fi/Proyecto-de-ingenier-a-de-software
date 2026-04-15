<table border="1">
    <tr>
        <th colspan="3">Reporte de ventas NATADINATTA</th>
    </tr>
    <tr>
        <td>Total vendido</td>
        <td colspan="2">{{ $totalSales }}</td>
    </tr>
    <tr>
        <td>Saldo pendiente</td>
        <td colspan="2">{{ $pendingBalance }}</td>
    </tr>
    <tr><td colspan="3"></td></tr>
    <tr>
        <th>Producto</th>
        <th>Unidades</th>
        <th>Valor</th>
    </tr>
    @foreach($topProducts as $product)
        <tr>
            <td>{{ $product->product_name }}</td>
            <td>{{ $product->total_quantity }}</td>
            <td>{{ $product->total_amount }}</td>
        </tr>
    @endforeach
    <tr><td colspan="3"></td></tr>
    <tr>
        <th>Factura</th>
        <th>Cliente</th>
        <th>Saldo</th>
    </tr>
    @foreach($pendingInvoices as $invoice)
        <tr>
            <td>{{ $invoice->invoice_number }}</td>
            <td>{{ $invoice->client_name }}</td>
            <td>{{ $invoice->balance_due }}</td>
        </tr>
    @endforeach
</table>
