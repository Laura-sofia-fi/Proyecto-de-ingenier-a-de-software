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

class ReportsExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_and_export_reports(): void
    {
        Company::current();
        $admin = User::factory()->admin()->create();
        $client = Client::query()->create(['name' => 'Cliente Reporte']);
        $reportDate = '2026-04-11';
        $order = Order::query()->create([
            'order_number' => 'PED-00001',
            'client_id' => $client->id,
            'created_by' => $admin->id,
            'order_date' => $reportDate,
            'status' => 'confirmado',
            'subtotal' => 30000,
            'total' => 30000,
        ]);

        OrderItem::query()->create([
            'order_id' => $order->id,
            'product_name' => 'Producto Reportado',
            'quantity' => 3,
            'unit_price' => 10000,
            'line_total' => 30000,
        ]);

        Invoice::query()->create([
            'order_id' => $order->id,
            'invoice_number' => 'NAT-00001',
            'issue_date' => $reportDate,
            'status' => 'activa',
            'payment_status' => 'abono',
            'company_name' => 'NATADINATTA',
            'company_address' => 'Calle 1',
            'company_city' => 'Bogotá',
            'company_nit' => '900',
            'client_name' => $client->name,
            'client_document' => null,
            'client_phone' => null,
            'subtotal' => 30000,
            'total' => 30000,
            'total_paid' => 10000,
            'balance_due' => 20000,
        ]);

        $this->actingAs($admin)->get('/reports?from='.$reportDate.'&to='.$reportDate)
            ->assertOk()
            ->assertSee('Producto Reportado')
            ->assertSee('NAT-00001');

        $this->actingAs($admin)->get('/reports/export/excel?from='.$reportDate.'&to='.$reportDate)
            ->assertOk()
            ->assertHeader('Content-Disposition', 'attachment; filename="reporte-ventas.xls"');

        $this->actingAs($admin)->get('/reports/export/pdf?from='.$reportDate.'&to='.$reportDate)
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_reports_filter_excludes_invoices_outside_selected_range(): void
    {
        Company::current();
        $admin = User::factory()->admin()->create();
        $client = Client::query()->create(['name' => 'Cliente Reporte']);

        $insideOrder = Order::query()->create([
            'order_number' => 'PED-RANGE-001',
            'client_id' => $client->id,
            'created_by' => $admin->id,
            'order_date' => '2026-04-11',
            'status' => 'confirmado',
            'subtotal' => 40000,
            'total' => 40000,
        ]);

        $outsideOrder = Order::query()->create([
            'order_number' => 'PED-RANGE-002',
            'client_id' => $client->id,
            'created_by' => $admin->id,
            'order_date' => '2026-04-01',
            'status' => 'confirmado',
            'subtotal' => 90000,
            'total' => 90000,
        ]);

        OrderItem::query()->create([
            'order_id' => $insideOrder->id,
            'product_name' => 'Producto Dentro',
            'quantity' => 2,
            'unit_price' => 20000,
            'line_total' => 40000,
        ]);

        OrderItem::query()->create([
            'order_id' => $outsideOrder->id,
            'product_name' => 'Producto Fuera',
            'quantity' => 3,
            'unit_price' => 30000,
            'line_total' => 90000,
        ]);

        Invoice::query()->create([
            'order_id' => $insideOrder->id,
            'invoice_number' => 'NAT-RANGE-001',
            'issue_date' => '2026-04-11',
            'status' => 'activa',
            'payment_status' => 'pendiente',
            'company_name' => 'NATADINATTA',
            'company_address' => 'Calle 1',
            'company_city' => 'Bogota',
            'company_nit' => '900',
            'client_name' => $client->name,
            'client_document' => null,
            'client_phone' => null,
            'subtotal' => 40000,
            'total' => 40000,
            'total_paid' => 0,
            'balance_due' => 40000,
        ]);

        Invoice::query()->create([
            'order_id' => $outsideOrder->id,
            'invoice_number' => 'NAT-RANGE-002',
            'issue_date' => '2026-04-01',
            'status' => 'activa',
            'payment_status' => 'pendiente',
            'company_name' => 'NATADINATTA',
            'company_address' => 'Calle 1',
            'company_city' => 'Bogota',
            'company_nit' => '900',
            'client_name' => $client->name,
            'client_document' => null,
            'client_phone' => null,
            'subtotal' => 90000,
            'total' => 90000,
            'total_paid' => 0,
            'balance_due' => 90000,
        ]);

        $this->actingAs($admin)->get('/reports?from=2026-04-10&to=2026-04-12')
            ->assertOk()
            ->assertSee('Producto Dentro')
            ->assertSee('NAT-RANGE-001')
            ->assertDontSee('Producto Fuera')
            ->assertDontSee('NAT-RANGE-002');
    }
}
