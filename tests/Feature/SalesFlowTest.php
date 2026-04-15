<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_confirm_and_pay_an_order(): void
    {
        Company::current();
        $admin = User::factory()->admin()->create();
        $client = Client::query()->create(['name' => 'Cliente Uno']);

        $this->actingAs($admin)->post('/orders', [
            'client_id' => $client->id,
            'order_date' => now()->toDateString(),
            'notes' => 'Pedido inicial',
            'items' => [
                ['product_name' => 'Producto A', 'quantity' => 2, 'unit_price' => 10000],
                ['product_name' => 'Producto B', 'quantity' => 1, 'unit_price' => 5000],
            ],
        ])->assertRedirect('/orders');

        $order = \App\Models\Order::query()->firstOrFail();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'total' => 25000,
            'status' => 'pendiente',
        ]);

        $this->actingAs($admin)->post("/orders/{$order->id}/confirm")
            ->assertRedirect("/orders/{$order->id}");

        $invoice = $order->fresh()->invoice;

        $this->assertNotNull($invoice);
        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'payment_status' => 'pendiente',
            'balance_due' => 25000,
        ]);

        $this->actingAs($admin)->post("/invoices/{$invoice->id}/payments", [
            'payment_date' => now()->toDateString(),
            'amount' => 10000,
            'notes' => 'Primer abono',
        ])->assertRedirect("/orders/{$order->id}");

        $this->actingAs($admin)->post("/invoices/{$invoice->id}/payments", [
            'payment_date' => now()->toDateString(),
            'amount' => 15000,
            'notes' => 'Pago final',
        ])->assertRedirect("/orders/{$order->id}");

        $this->assertDatabaseHas('payments', ['invoice_id' => $invoice->id, 'amount' => 10000]);
        $this->assertDatabaseHas('payments', ['invoice_id' => $invoice->id, 'amount' => 15000]);
        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'payment_status' => 'pagado',
            'total_paid' => 25000,
            'balance_due' => 0,
        ]);
    }
}
