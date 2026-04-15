<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_history_displays_orders_invoices_and_payments(): void
    {
        Company::current();
        $admin = User::factory()->admin()->create(['name' => 'Admin Uno']);
        $client = Client::query()->create([
            'name' => 'Cliente Historial',
            'document_number' => '998877',
            'address' => 'Calle 45',
        ]);

        $order = Order::query()->create([
            'order_number' => 'PED-HIS-001',
            'client_id' => $client->id,
            'created_by' => $admin->id,
            'order_date' => now()->toDateString(),
            'status' => 'confirmado',
            'stock_adjusted' => false,
            'subtotal' => 70000,
            'total' => 70000,
        ]);

        OrderItem::query()->create([
            'order_id' => $order->id,
            'product_name' => 'Pull buoy',
            'quantity' => 1,
            'unit_price' => 70000,
            'line_total' => 70000,
        ]);

        $invoice = Invoice::query()->create([
            'order_id' => $order->id,
            'invoice_number' => 'NAT-HIS-001',
            'issue_date' => now()->toDateString(),
            'status' => 'activa',
            'payment_status' => 'abono',
            'company_name' => 'NATADINATTA',
            'company_address' => 'Calle 10',
            'company_city' => 'Bogota',
            'company_nit' => '900',
            'client_name' => $client->name,
            'client_document' => $client->document_number,
            'client_phone' => null,
            'subtotal' => 70000,
            'total' => 70000,
            'total_paid' => 30000,
            'balance_due' => 40000,
        ]);

        Payment::query()->create([
            'invoice_id' => $invoice->id,
            'recorded_by' => $admin->id,
            'payment_date' => now()->toDateString(),
            'amount' => 30000,
            'notes' => 'Abono inicial',
        ]);

        $date = now()->toDateString();

        $this->actingAs($admin)
            ->get("/clients/{$client->id}?from={$date}&to={$date}")
            ->assertOk()
            ->assertSee('Cliente Historial')
            ->assertSee('PED-HIS-001')
            ->assertSee('NAT-HIS-001')
            ->assertSee('Abono inicial')
            ->assertSee('Saldo pendiente');
    }
}
