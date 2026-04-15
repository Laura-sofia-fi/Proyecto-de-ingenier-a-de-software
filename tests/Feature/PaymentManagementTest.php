<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_edit_and_delete_a_payment(): void
    {
        Company::current();
        $admin = User::factory()->admin()->create();
        $client = Client::query()->create(['name' => 'Cliente Abono']);
        $order = Order::query()->create([
            'order_number' => 'PED-PAY-001',
            'client_id' => $client->id,
            'created_by' => $admin->id,
            'order_date' => now()->toDateString(),
            'status' => 'confirmado',
            'stock_adjusted' => false,
            'subtotal' => 100000,
            'total' => 100000,
        ]);

        $invoice = Invoice::query()->create([
            'order_id' => $order->id,
            'invoice_number' => 'NAT-PAY-001',
            'issue_date' => now()->toDateString(),
            'status' => 'activa',
            'payment_status' => 'abono',
            'company_name' => 'NATADINATTA',
            'company_address' => 'Calle 1',
            'company_city' => 'Bogota',
            'company_nit' => '900',
            'client_name' => $client->name,
            'client_document' => null,
            'client_phone' => null,
            'subtotal' => 100000,
            'total' => 100000,
            'total_paid' => 30000,
            'balance_due' => 70000,
        ]);

        $payment = Payment::query()->create([
            'invoice_id' => $invoice->id,
            'recorded_by' => $admin->id,
            'payment_date' => now()->toDateString(),
            'amount' => 30000,
            'notes' => 'Abono inicial',
        ]);

        $this->actingAs($admin)
            ->put("/payments/{$payment->id}", [
                'payment_date' => now()->toDateString(),
                'amount' => 45000,
                'notes' => 'Abono corregido',
            ])
            ->assertRedirect("/orders/{$order->id}");

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'amount' => 45000,
            'notes' => 'Abono corregido',
        ]);

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'total_paid' => 45000,
            'balance_due' => 55000,
        ]);

        $this->actingAs($admin)
            ->delete("/payments/{$payment->id}")
            ->assertRedirect("/orders/{$order->id}");

        $this->assertDatabaseMissing('payments', ['id' => $payment->id]);
        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'total_paid' => 0,
            'balance_due' => 100000,
            'payment_status' => 'pendiente',
        ]);
    }
}
