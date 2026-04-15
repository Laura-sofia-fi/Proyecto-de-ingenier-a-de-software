<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();

        $clients = Client::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($nested) use ($search) {
                    $nested->where('name', 'like', "%{$search}%")
                        ->orWhere('document_number', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('clients.index', compact('clients', 'search'));
    }

    public function create(): View
    {
        return view('clients.create', ['client' => new Client()]);
    }

    public function show(Client $client): View
    {
        $from = request('from', now()->startOfMonth()->toDateString());
        $to = request('to', now()->toDateString());
        $fromDate = Carbon::parse($from)->startOfDay();
        $toDate = Carbon::parse($to)->endOfDay();

        $client->load([
            'orders.invoice.payments.recorder',
            'orders.items',
        ]);

        $orders = $client->orders
            ->filter(fn ($order) => $order->order_date->between($fromDate, $toDate))
            ->sortByDesc('order_date')
            ->values();
        $invoices = $orders->pluck('invoice')->filter();
        $payments = $invoices->flatMap->payments
            ->filter(fn ($payment) => $payment->payment_date->between($fromDate, $toDate))
            ->sortByDesc('payment_date')
            ->values();

        return view('clients.show', [
            'client' => $client,
            'orders' => $orders,
            'invoices' => $invoices,
            'payments' => $payments,
            'from' => $from,
            'to' => $to,
            'metrics' => [
                'orders_count' => $orders->count(),
                'invoices_count' => $invoices->count(),
                'total_billed' => (float) $invoices->sum('total'),
                'total_paid' => (float) $invoices->sum('total_paid'),
                'balance_due' => (float) $invoices->sum('balance_due'),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Client::query()->create($this->validatedData($request));

        return redirect()->route('clients.index')->with('status', 'Cliente creado correctamente.');
    }

    public function edit(Client $client): View
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client): RedirectResponse
    {
        $client->update($this->validatedData($request));

        return redirect()->route('clients.index')->with('status', 'Cliente actualizado correctamente.');
    }

    public function destroy(Client $client): RedirectResponse
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $client->delete();

        return redirect()->route('clients.index')->with('status', 'Cliente eliminado correctamente.');
    }

    protected function validatedData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'document_number' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
