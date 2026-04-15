<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReceivablesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_global_receivables_module(): void
    {
        Company::current();
        $admin = User::factory()->admin()->create();
        $client = Client::query()->create(['name' => 'Cliente Cartera', 'document_number' => '554433']);
        $order = Order::query()->create([
            'order_number' => 'PED-CXC-001',
            'client_id' => $client->id,
            'created_by' => $admin->id,
            'order_date' => now()->toDateString(),
            'status' => 'confirmado',
            'stock_adjusted' => false,
            'subtotal' => 90000,
            'total' => 90000,
        ]);

        Invoice::query()->create([
            'order_id' => $order->id,
            'invoice_number' => 'NAT-CXC-001',
            'issue_date' => now()->toDateString(),
            'status' => 'activa',
            'payment_status' => 'abono',
            'company_name' => 'NATADINATTA',
            'company_address' => 'Calle 1',
            'company_city' => 'Bogota',
            'company_nit' => '900',
            'client_name' => $client->name,
            'client_document' => $client->document_number,
            'client_phone' => null,
            'subtotal' => 90000,
            'total' => 90000,
            'total_paid' => 30000,
            'balance_due' => 60000,
        ]);

        $date = now()->toDateString();

        $this->actingAs($admin)
            ->get('/receivables?from='.$date.'&to='.$date)
            ->assertOk()
            ->assertSee('Cuentas por cobrar')
            ->assertSee('NAT-CXC-001')
            ->assertSee('Cliente Cartera')
            ->assertSee('Saldo total pendiente');

        $this->actingAs($admin)
            ->get('/receivables/export/excel?from='.$date.'&to='.$date)
            ->assertOk()
            ->assertHeader('Content-Disposition', 'attachment; filename="cuentas-por-cobrar.xls"');

        $this->actingAs($admin)
            ->get('/receivables/export/pdf?from='.$date.'&to='.$date)
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_receivables_filter_excludes_invoices_outside_selected_range(): void
    {
        Company::current();
        $admin = User::factory()->admin()->create();
        $client = Client::query()->create(['name' => 'Cliente Cartera', 'document_number' => '554433']);

        $insideOrder = Order::query()->create([
            'order_number' => 'PED-CXC-010',
            'client_id' => $client->id,
            'created_by' => $admin->id,
            'order_date' => '2026-04-11',
            'status' => 'confirmado',
            'stock_adjusted' => false,
            'subtotal' => 50000,
            'total' => 50000,
        ]);

        $outsideOrder = Order::query()->create([
            'order_number' => 'PED-CXC-011',
            'client_id' => $client->id,
            'created_by' => $admin->id,
            'order_date' => '2026-04-01',
            'status' => 'confirmado',
            'stock_adjusted' => false,
            'subtotal' => 70000,
            'total' => 70000,
        ]);

        Invoice::query()->create([
            'order_id' => $insideOrder->id,
            'invoice_number' => 'NAT-CXC-010',
            'issue_date' => '2026-04-11',
            'status' => 'activa',
            'payment_status' => 'pendiente',
            'company_name' => 'NATADINATTA',
            'company_address' => 'Calle 1',
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

        Invoice::query()->create([
            'order_id' => $outsideOrder->id,
            'invoice_number' => 'NAT-CXC-011',
            'issue_date' => '2026-04-01',
            'status' => 'activa',
            'payment_status' => 'pendiente',
            'company_name' => 'NATADINATTA',
            'company_address' => 'Calle 1',
            'company_city' => 'Bogota',
            'company_nit' => '900',
            'client_name' => $client->name,
            'client_document' => $client->document_number,
            'client_phone' => null,
            'subtotal' => 70000,
            'total' => 70000,
            'total_paid' => 0,
            'balance_due' => 70000,
        ]);

        $this->actingAs($admin)
            ->get('/receivables?from=2026-04-10&to=2026-04-12')
            ->assertOk()
            ->assertSee('NAT-CXC-010')
            ->assertDontSee('NAT-CXC-011');
    }
}
