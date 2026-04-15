<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Support\Currency;
use App\Support\SimplePdf;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ReceivableController extends Controller
{
    public function index(Request $request): View
    {
        [$search, $from, $to, $query] = $this->filteredQuery($request);

        $receivables = (clone $query)
            ->orderByDesc('balance_due')
            ->orderBy('issue_date')
            ->paginate(12)
            ->withQueryString();

        return view('receivables.index', [
            'receivables' => $receivables,
            'search' => $search,
            'from' => $from,
            'to' => $to,
            'metrics' => [
                'total_pending' => (float) (clone $query)->sum('balance_due'),
                'open_invoices' => (int) (clone $query)->count(),
                'partial_invoices' => (int) (clone $query)->where('payment_status', 'abono')->count(),
            ],
        ]);
    }

    public function exportExcel(Request $request)
    {
        [, $from, $to, $query] = $this->filteredQuery($request);
        $receivables = (clone $query)->orderByDesc('balance_due')->orderBy('issue_date')->get();

        return response()
            ->view('receivables.export_excel', [
                'receivables' => $receivables,
                'from' => $from,
                'to' => $to,
            ])
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', 'attachment; filename="cuentas-por-cobrar.xls"');
    }

    public function exportPdf(Request $request): Response
    {
        [, $from, $to, $query] = $this->filteredQuery($request);
        $receivables = (clone $query)->orderByDesc('balance_due')->orderBy('issue_date')->get();

        $lines = [
            'Rango: '.$from.' a '.$to,
            'Total pendiente: '.Currency::cop((float) $receivables->sum('balance_due')),
            'Facturas abiertas: '.$receivables->count(),
            '',
            'Detalle:',
        ];

        foreach ($receivables as $invoice) {
            $lines[] = $invoice->invoice_number.' | '.$invoice->client_name.' | saldo '.Currency::cop($invoice->balance_due).' | '.$invoice->issue_date->format('Y-m-d');
        }

        return SimplePdf::download('Cuentas por cobrar NATADINATTA', $lines, 'cuentas-por-cobrar.pdf');
    }

    protected function filteredQuery(Request $request): array
    {
        $search = $request->string('search')->toString();
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->toDateString());
        $fromDate = Carbon::parse($from)->startOfDay();
        $toDate = Carbon::parse($to)->endOfDay();

        $query = Invoice::query()
            ->with(['order.client', 'payments'])
            ->where('status', 'activa')
            ->where('balance_due', '>', 0)
            ->whereBetween('issue_date', [$fromDate, $toDate])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($nested) use ($search) {
                    $nested->where('invoice_number', 'like', "%{$search}%")
                        ->orWhere('client_name', 'like', "%{$search}%")
                        ->orWhere('client_document', 'like', "%{$search}%");
                });
            });

        return [$search, $from, $to, $query];
    }
}
