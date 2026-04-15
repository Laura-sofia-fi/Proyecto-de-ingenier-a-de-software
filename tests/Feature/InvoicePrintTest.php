<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoicePrintTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_open_printable_invoice(): void
    {
        Company::current();
        $user = User::factory()->admin()->create();
        $client = Client::query()->create(['name' => 'Cliente Factura', 'document_number' => '123']);
        $order = Order::query()->create([
            'order_number' => 'PED-FACT-001',
            'client_id' => $client->id,
            'created_by' => $user->id,
            'order_date' => now()->toDateString(),
            'status' => 'confirmado',
            'stock_adjusted' => false,
            'subtotal' => 50000,
            'total' => 50000,
        ]);

        OrderItem::query()->create([
            'order_id' => $order->id,
            'product_name' => 'Cronometro',
            'quantity' => 1,
            'unit_price' => 50000,
            'line_total' => 50000,
        ]);

        $invoice = Invoice::query()->create([
            'order_id' => $order->id,
            'invoice_number' => 'NAT-PRINT-001',
            'issue_date' => now()->toDateString(),
            'status' => 'activa',
            'payment_status' => 'pendiente',
            'company_name' => 'NATADINATTA',
            'company_address' => 'Calle 10',
            'company_city' => 'Bogota',
            'company_nit' => '900',
            'client_name' => $client->name,
            'client_document' => $client->document_number,
            'client_phone' => null,
            'subtotal' => 50000,
            'total' => 50000,
            'total_paid' => 0,
            'balance_due' => 50000,
        ]);

        $this->actingAs($user)
            ->get("/invoices/{$invoice->id}/print")
            ->assertOk()
            ->assertSee('NAT-PRINT-001')
            ->assertSee('Cronometro')
            ->assertSee('Imprimir factura');
    }

    public function test_authenticated_user_can_download_invoice_pdf(): void
    {
        Company::current();
        $user = User::factory()->admin()->create();
        $client = Client::query()->create(['name' => 'Cliente PDF']);
        $order = Order::query()->create([
            'order_number' => 'PED-PDF-001',
            'client_id' => $client->id,
            'created_by' => $user->id,
            'order_date' => now()->toDateString(),
            'status' => 'confirmado',
            'stock_adjusted' => false,
            'subtotal' => 25000,
            'total' => 25000,
        ]);

        OrderItem::query()->create([
            'order_id' => $order->id,
            'product_name' => 'Silbato',
            'quantity' => 1,
            'unit_price' => 25000,
            'line_total' => 25000,
        ]);

        $invoice = Invoice::query()->create([
            'order_id' => $order->id,
            'invoice_number' => 'NAT-PDF-001',
            'issue_date' => now()->toDateString(),
            'status' => 'activa',
            'payment_status' => 'pendiente',
            'company_name' => 'NATADINATTA',
            'company_address' => 'Calle 10',
            'company_city' => 'Bogota',
            'company_nit' => '900',
            'client_name' => $client->name,
            'client_document' => null,
            'client_phone' => null,
            'subtotal' => 25000,
            'total' => 25000,
            'total_paid' => 0,
            'balance_due' => 25000,
        ]);

        $this->actingAs($user)
            ->get("/invoices/{$invoice->id}/pdf")
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf')
            ->assertHeader('Content-Disposition', 'attachment; filename="NAT-PDF-001.pdf"');
    }
}
