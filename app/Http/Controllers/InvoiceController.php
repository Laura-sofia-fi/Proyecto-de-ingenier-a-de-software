<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Services\OrderLifecycleService;
use App\Support\Currency;
use App\Support\HtmlPdf;
use App\Support\SimplePdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function print(Invoice $invoice): View
    {
        $invoice->load(['order.client', 'order.items', 'payments.recorder']);

        return view('invoices.print', compact('invoice'));
    }

    public function downloadPdf(Invoice $invoice): Response
    {
        $invoice->load(['order.client', 'order.items', 'payments.recorder']);

        $html = view('invoices.print', [
            'invoice' => $invoice,
            'downloadMode' => true,
        ])->render();

        $renderedPdf = HtmlPdf::fromHtml($html, $invoice->invoice_number.'.pdf');

        if ($renderedPdf) {
            return response($renderedPdf['contents'], 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="'.$renderedPdf['filename'].'"',
                'Content-Length' => strlen($renderedPdf['contents']),
            ]);
        }

        $lines = [
            'Factura '.$invoice->invoice_number,
            'Fecha: '.$invoice->issue_date->format('Y-m-d'),
            'Empresa: '.$invoice->company_name,
            'NIT: '.$invoice->company_nit,
            'Direccion: '.$invoice->company_address.', '.$invoice->company_city,
            '',
            'Cliente: '.$invoice->client_name,
            'Documento: '.($invoice->client_document ?: 'N/A'),
            'Telefono: '.($invoice->client_phone ?: 'N/A'),
            '',
            'Detalle de productos:',
        ];

        foreach ($invoice->order->items as $item) {
            $lines[] = sprintf(
                '%s | Cant: %d | Precio: %s | Total: %s',
                $item->product_name,
                $item->quantity,
                Currency::cop($item->unit_price),
                Currency::cop($item->line_total)
            );
        }

        $lines[] = '';
        $lines[] = 'Total factura: '.Currency::cop($invoice->total);
        $lines[] = 'Total abonado: '.Currency::cop($invoice->total_paid);
        $lines[] = 'Saldo pendiente: '.Currency::cop($invoice->balance_due);
        $lines[] = 'Estado de pago: '.ucfirst($invoice->payment_status);
        $lines[] = '';
        $lines[] = 'Abonos registrados:';

        if ($invoice->payments->isEmpty()) {
            $lines[] = 'Sin abonos registrados.';
        } else {
            foreach ($invoice->payments as $payment) {
                $lines[] = sprintf(
                    '%s | %s | %s',
                    $payment->payment_date->format('Y-m-d'),
                    Currency::cop($payment->amount),
                    $payment->notes ?: 'Sin nota'
                );
            }
        }

        return SimplePdf::download(
            'Factura '.$invoice->invoice_number.' - NATADINATTA',
            $lines,
            $invoice->invoice_number.'.pdf'
        );
    }

    public function destroy(OrderLifecycleService $lifecycle, Invoice $invoice): RedirectResponse
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        DB::transaction(function () use ($lifecycle, $invoice) {
            $lifecycle->deleteInvoice($invoice);
        });

        return redirect()->route('orders.show', $invoice->order)->with('status', 'Factura eliminada correctamente.');
    }
}
