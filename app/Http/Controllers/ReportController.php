<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\OrderItem;
use App\Support\Currency;
use App\Support\SimplePdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        [$from, $to, $reportData] = $this->reportData($request);

        return view('reports.index', array_merge($reportData, [
            'from' => $from,
            'to' => $to,
        ]));
    }

    public function exportExcel(Request $request)
    {
        [, , $data] = $this->reportData($request);

        return response()
            ->view('reports.export_excel', $data)
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', 'attachment; filename="reporte-ventas.xls"');
    }

    public function exportPdf(Request $request)
    {
        [$from, $to, $data] = $this->reportData($request);

        $lines = [
            'Rango: '.$from.' a '.$to,
            'Total vendido: '.Currency::cop($data['totalSales']),
            'Saldo pendiente: '.Currency::cop($data['pendingBalance']),
            'Facturas activas: '.$data['invoices']->count(),
            '',
            'Productos mas vendidos:',
        ];

        foreach ($data['topProducts'] as $product) {
            $lines[] = $product->product_name.' - '.$product->total_quantity.' unidades';
        }

        $lines[] = '';
        $lines[] = 'Facturas con saldo:';

        foreach ($data['pendingInvoices'] as $invoice) {
            $lines[] = $invoice->invoice_number.' / '.$invoice->client_name.' / saldo '.Currency::cop($invoice->balance_due);
        }

        return SimplePdf::download('Reporte de ventas NATADINATTA', $lines, 'reporte-ventas.pdf');
    }

    protected function reportData(Request $request): array
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->toDateString());
        $fromDate = Carbon::parse($from)->startOfDay();
        $toDate = Carbon::parse($to)->endOfDay();

        $invoiceQuery = Invoice::query()
            ->where('status', 'activa')
            ->whereBetween('issue_date', [$fromDate, $toDate]);

        $invoices = (clone $invoiceQuery)->latest('issue_date')->get();
        $orderIds = $invoices->pluck('order_id');

        $topProducts = OrderItem::query()
            ->selectRaw('product_name, SUM(quantity) as total_quantity, SUM(line_total) as total_amount')
            ->whereIn('order_id', $orderIds->all())
            ->groupBy('product_name')
            ->orderByDesc('total_quantity')
            ->take(10)
            ->get();

        $pendingInvoices = (clone $invoiceQuery)
            ->where('balance_due', '>', 0)
            ->orderByDesc('balance_due')
            ->get();

        return [
            $from,
            $to,
            [
                'invoices' => $invoices,
                'totalSales' => (float) $invoices->sum('total'),
                'pendingBalance' => (float) $pendingInvoices->sum('balance_due'),
                'topProducts' => $topProducts,
                'pendingInvoices' => $pendingInvoices,
            ],
        ];
    }
}
