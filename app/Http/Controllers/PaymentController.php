<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function store(Request $request, Invoice $invoice): RedirectResponse
    {
        abort_if($invoice->status !== 'activa', 403);

        $data = $request->validate([
            'payment_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:1'],
            'notes' => ['nullable', 'string'],
        ]);

        if ((float) $data['amount'] > (float) $invoice->balance_due) {
            return back()->withErrors(['amount' => 'El abono no puede superar el saldo pendiente.']);
        }

        DB::transaction(function () use ($invoice, $data) {
            $invoice->payments()->create([
                'recorded_by' => auth()->id(),
                'payment_date' => $data['payment_date'],
                'amount' => $data['amount'],
                'notes' => $data['notes'] ?? null,
            ]);

            $invoice->refresh();
            $invoice->refreshPaymentSummary();
        });

        return redirect()->route('orders.show', $invoice->order_id)->with('status', 'Abono registrado correctamente.');
    }

    public function edit(Payment $payment): View
    {
        $this->authorizePaymentAction($payment);

        $payment->load('invoice.order.client');

        return view('payments.edit', compact('payment'));
    }

    public function update(Request $request, Payment $payment): RedirectResponse
    {
        $this->authorizePaymentAction($payment);

        $data = $request->validate([
            'payment_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:1'],
            'notes' => ['nullable', 'string'],
        ]);

        $invoice = $payment->invoice()->firstOrFail();
        $otherPayments = (float) $invoice->payments()->whereKeyNot($payment->id)->sum('amount');

        if ($otherPayments + (float) $data['amount'] > (float) $invoice->total) {
            return back()->withErrors(['amount' => 'El abono actualizado no puede superar el total de la factura.'])->withInput();
        }

        DB::transaction(function () use ($payment, $invoice, $data) {
            $payment->update([
                'payment_date' => $data['payment_date'],
                'amount' => $data['amount'],
                'notes' => $data['notes'] ?? null,
            ]);

            $invoice->refreshPaymentSummary();
        });

        return redirect()->route('orders.show', $invoice->order_id)->with('status', 'Abono actualizado correctamente.');
    }

    public function destroy(Payment $payment): RedirectResponse
    {
        $this->authorizePaymentAction($payment);

        $invoice = $payment->invoice()->firstOrFail();

        DB::transaction(function () use ($payment, $invoice) {
            $payment->delete();
            $invoice->refreshPaymentSummary();
        });

        return redirect()->route('orders.show', $invoice->order_id)->with('status', 'Abono eliminado correctamente.');
    }

    protected function authorizePaymentAction(Payment $payment): void
    {
        $user = auth()->user();

        abort_if(
            ! $user || (! $user->isAdmin() && $payment->recorded_by !== $user->id),
            403
        );
    }
}
