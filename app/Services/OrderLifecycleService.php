<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Invoice;
use App\Models\Order;
use App\Support\NumberGenerator;

class OrderLifecycleService
{
    public function confirmOrder(Order $order, Company $company): Invoice
    {
        $order->loadMissing(['client', 'items.product']);

        $invoice = Invoice::query()->create([
            'order_id' => $order->id,
            'invoice_number' => NumberGenerator::nextInvoiceNumber(),
            'issue_date' => now()->toDateString(),
            'status' => 'activa',
            'payment_status' => 'pendiente',
            'company_name' => $company->name,
            'company_address' => $company->address,
            'company_city' => $company->city,
            'company_nit' => $company->nit,
            'client_name' => $order->client->name,
            'client_document' => $order->client->document_number,
            'client_phone' => $order->client->phone,
            'subtotal' => $order->subtotal,
            'total' => $order->total,
            'total_paid' => 0,
            'balance_due' => $order->total,
        ]);

        $invoice->refreshPaymentSummary();

        $order->update([
            'status' => 'confirmado',
            'stock_adjusted' => false,
        ]);

        return $invoice;
    }

    public function cancelOrder(Order $order): void
    {
        $order->loadMissing(['items.product', 'invoice']);

        $this->restoreStock($order);

        $order->update([
            'status' => 'cancelado',
            'stock_adjusted' => false,
        ]);

        $order->invoice?->update(['status' => 'cancelada']);
    }

    public function deleteInvoice(Invoice $invoice): void
    {
        $invoice->loadMissing(['order.items.product', 'payments']);

        $order = $invoice->order;

        $this->restoreStock($order);

        $invoice->payments()->delete();
        $invoice->delete();

        $order->update([
            'status' => 'pendiente',
            'stock_adjusted' => false,
        ]);
    }

    public function deleteOrder(Order $order): void
    {
        $order->loadMissing(['items.product', 'invoice.payments']);

        $this->restoreStock($order);

        if ($order->invoice) {
            $order->invoice->payments()->delete();
            $order->invoice->delete();
        }

        $order->delete();
    }

    protected function restoreStock(Order $order): void
    {
        if (! $order->stock_adjusted) {
            return;
        }

        foreach ($order->items as $item) {
            if ($item->product) {
                $item->product->increment('stock', $item->quantity);
            }
        }
    }
}
