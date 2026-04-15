<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete_invoice_without_payments_and_restore_order(): void
    {
        Company::current();
        $admin = User::factory()->admin()->create();
        $client = Client::query()->create(['name' => 'Cliente Factura']);
        $product = Product::query()->create([
            'name' => 'Producto X',
            'sku' => 'PX-001',
            'price' => 10000,
            'stock' => 5,
            'is_active' => true,
        ]);

        $order = Order::query()->create([
            'order_number' => 'PED-DEL-001',
            'client_id' => $client->id,
            'created_by' => $admin->id,
            'order_date' => now()->toDateString(),
            'status' => 'confirmado',
            'stock_adjusted' => true,
            'subtotal' => 20000,
            'total' => 20000,
        ]);

        OrderItem::query()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 2,
            'unit_price' => 10000,
            'line_total' => 20000,
        ]);

        $invoice = Invoice::query()->create([
            'order_id' => $order->id,
            'invoice_number' => 'NAT-DEL-001',
            'issue_date' => now()->toDateString(),
            'status' => 'activa',
            'payment_status' => 'pendiente',
            'company_name' => 'NATADINATTA',
            'company_address' => 'Calle 1',
            'company_city' => 'Bogota',
            'company_nit' => '900',
            'client_name' => $client->name,
            'client_document' => null,
            'client_phone' => null,
            'subtotal' => 20000,
            'total' => 20000,
            'total_paid' => 0,
            'balance_due' => 20000,
        ]);

        $product->decrement('stock', 2);

        $this->actingAs($admin)
            ->delete("/invoices/{$invoice->id}")
            ->assertRedirect("/orders/{$order->id}");

        $this->assertDatabaseMissing('invoices', ['id' => $invoice->id]);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'pendiente',
            'stock_adjusted' => false,
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 5,
        ]);
    }

    public function test_admin_can_delete_invoice_with_payments_and_cleanup_related_records(): void
    {
        Company::current();
        $admin = User::factory()->admin()->create();
        $client = Client::query()->create(['name' => 'Cliente Factura']);
        $product = Product::query()->create([
            'name' => 'Producto Y',
            'sku' => 'PY-001',
            'price' => 15000,
            'stock' => 8,
            'is_active' => true,
        ]);

        $order = Order::query()->create([
            'order_number' => 'PED-DEL-002',
            'client_id' => $client->id,
            'created_by' => $admin->id,
            'order_date' => now()->toDateString(),
            'status' => 'confirmado',
            'stock_adjusted' => true,
            'subtotal' => 30000,
            'total' => 30000,
        ]);

        OrderItem::query()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 2,
            'unit_price' => 15000,
            'line_total' => 30000,
        ]);

        $invoice = Invoice::query()->create([
            'order_id' => $order->id,
            'invoice_number' => 'NAT-DEL-002',
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
            'subtotal' => 30000,
            'total' => 30000,
            'total_paid' => 10000,
            'balance_due' => 20000,
        ]);

        $payment = Payment::query()->create([
            'invoice_id' => $invoice->id,
            'recorded_by' => $admin->id,
            'payment_date' => now()->toDateString(),
            'amount' => 10000,
            'notes' => 'Abono para eliminar',
        ]);

        $product->decrement('stock', 2);

        $this->actingAs($admin)
            ->delete("/invoices/{$invoice->id}")
            ->assertRedirect("/orders/{$order->id}");

        $this->assertDatabaseMissing('invoices', ['id' => $invoice->id]);
        $this->assertDatabaseMissing('payments', ['id' => $payment->id]);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'pendiente',
            'stock_adjusted' => false,
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 8,
        ]);
    }

    public function test_admin_can_delete_order_from_listing_and_remove_invoice_from_database(): void
    {
        Company::current();
        $admin = User::factory()->admin()->create();
        $client = Client::query()->create(['name' => 'Cliente Borrado']);
        $product = Product::query()->create([
            'name' => 'Producto Z',
            'sku' => 'PZ-001',
            'price' => 25000,
            'stock' => 10,
            'is_active' => true,
        ]);

        $order = Order::query()->create([
            'order_number' => 'PED-DEL-003',
            'client_id' => $client->id,
            'created_by' => $admin->id,
            'order_date' => now()->toDateString(),
            'status' => 'confirmado',
            'stock_adjusted' => true,
            'subtotal' => 50000,
            'total' => 50000,
        ]);

        OrderItem::query()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 2,
            'unit_price' => 25000,
            'line_total' => 50000,
        ]);

        $invoice = Invoice::query()->create([
            'order_id' => $order->id,
            'invoice_number' => 'NAT-DEL-003',
            'issue_date' => now()->toDateString(),
            'status' => 'activa',
            'payment_status' => 'pendiente',
            'company_name' => 'NATADINATTA',
            'company_address' => 'Calle 1',
            'company_city' => 'Bogota',
            'company_nit' => '900',
            'client_name' => $client->name,
            'client_document' => null,
            'client_phone' => null,
            'subtotal' => 50000,
            'total' => 50000,
            'total_paid' => 0,
            'balance_due' => 50000,
        ]);

        $product->decrement('stock', 2);

        $this->actingAs($admin)
            ->delete("/orders/{$order->id}")
            ->assertRedirect('/orders');

        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
        $this->assertDatabaseMissing('invoices', ['id' => $invoice->id]);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 10,
        ]);
    }
}
