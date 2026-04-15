<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Company;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderLifecycleService;
use App\Support\NumberGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $status = $request->string('status')->toString();
        $search = $request->string('search')->toString();
        $from = $request->input('from');
        $to = $request->input('to');

        $orders = Order::query()
            ->with(['client', 'creator', 'invoice'])
            ->when($user?->isEmployee(), fn ($query) => $query->where('created_by', $user->id))
            ->when($search, function ($query) use ($search) {
                $query->where(function ($nested) use ($search) {
                    $nested->where('order_number', 'like', "%{$search}%")
                        ->orWhereHas('client', fn ($client) => $client->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('invoice', fn ($invoice) => $invoice->where('invoice_number', 'like', "%{$search}%"));
                });
            })
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($from, fn ($query) => $query->whereDate('order_date', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('order_date', '<=', $to))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('orders.index', compact('orders', 'status', 'search', 'from', 'to'));
    }

    public function create(): View
    {
        return view('orders.create', [
            'order' => new Order(['order_date' => now()->toDateString()]),
            'clients' => Client::query()->orderBy('name')->get(),
            'products' => Product::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $payload = $this->validatedOrder($request);

        DB::transaction(function () use ($payload) {
            $order = Order::query()->create([
                'order_number' => NumberGenerator::nextOrderNumber(),
                'client_id' => $payload['client_id'],
                'created_by' => auth()->id(),
                'order_date' => $payload['order_date'],
                'status' => 'pendiente',
                'subtotal' => $payload['subtotal'],
                'total' => $payload['total'],
                'notes' => $payload['notes'],
            ]);

            $this->syncItems($order, $payload['items']);
        });

        return redirect()->route('orders.index')->with('status', 'Pedido registrado correctamente.');
    }

    public function show(Order $order): View
    {
        $this->authorizeOrderAccess($order);

        $order->load(['client', 'creator', 'items', 'invoice.payments.recorder']);

        return view('orders.show', compact('order'));
    }

    public function edit(Order $order): View
    {
        $this->authorizeOrderAccess($order);
        abort_if($order->status !== 'pendiente', 403);

        $order->load('items');

        return view('orders.edit', [
            'order' => $order,
            'clients' => Client::query()->orderBy('name')->get(),
            'products' => Product::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $this->authorizeOrderAccess($order);
        abort_if($order->status !== 'pendiente', 403);

        $payload = $this->validatedOrder($request);

        DB::transaction(function () use ($order, $payload) {
            $order->update([
                'client_id' => $payload['client_id'],
                'order_date' => $payload['order_date'],
                'subtotal' => $payload['subtotal'],
                'total' => $payload['total'],
                'notes' => $payload['notes'],
            ]);

            $order->items()->delete();
            $this->syncItems($order, $payload['items']);
        });

        return redirect()->route('orders.show', $order)->with('status', 'Pedido actualizado correctamente.');
    }

    public function confirm(OrderLifecycleService $lifecycle, Order $order): RedirectResponse
    {
        $this->authorizeOrderAccess($order);
        abort_if($order->status !== 'pendiente', 403);

        DB::transaction(function () use ($lifecycle, $order) {
            $lifecycle->confirmOrder($order, Company::current());
        });

        return redirect()->route('orders.show', $order)->with('status', 'Factura confirmada correctamente.');
    }

    public function cancel(OrderLifecycleService $lifecycle, Order $order): RedirectResponse
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        abort_if($order->status === 'cancelado', 403);

        DB::transaction(function () use ($lifecycle, $order) {
            $lifecycle->cancelOrder($order);
        });

        return redirect()->route('orders.show', $order)->with('status', 'Pedido o factura cancelada correctamente.');
    }

    public function destroy(OrderLifecycleService $lifecycle, Order $order): RedirectResponse
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        DB::transaction(function () use ($lifecycle, $order) {
            $lifecycle->deleteOrder($order);
        });

        return redirect()->route('orders.index')->with('status', 'Venta eliminada correctamente.');
    }

    protected function validatedOrder(Request $request): array
    {
        $data = $request->validate([
            'client_id' => ['required', Rule::exists('clients', 'id')],
            'order_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['nullable', Rule::exists('products', 'id')],
            'items.*.product_name' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        $items = collect($data['items'])->map(function (array $item) {
            $quantity = (int) $item['quantity'];
            $unitPrice = (float) $item['unit_price'];

            return [
                'product_id' => ! empty($item['product_id']) ? (int) $item['product_id'] : null,
                'product_name' => $item['product_name'],
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'line_total' => $quantity * $unitPrice,
            ];
        })->all();

        $subtotal = collect($items)->sum('line_total');

        return [
            'client_id' => (int) $data['client_id'],
            'order_date' => $data['order_date'],
            'notes' => $data['notes'] ?? null,
            'items' => $items,
            'subtotal' => $subtotal,
            'total' => $subtotal,
        ];
    }

    protected function syncItems(Order $order, array $items): void
    {
        foreach ($items as $item) {
            $order->items()->create($item);
        }
    }

    protected function authorizeOrderAccess(Order $order): void
    {
        $user = auth()->user();

        abort_if(! $user, 403);
        abort_if($user->isEmployee() && $order->created_by !== $user->id, 403);
    }
}
