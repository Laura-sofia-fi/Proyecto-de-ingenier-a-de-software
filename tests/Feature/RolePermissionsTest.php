<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_cannot_delete_clients(): void
    {
        $employee = User::factory()->create();
        $client = Client::query()->create(['name' => 'Cliente X']);

        $this->actingAs($employee)
            ->delete("/clients/{$client->id}")
            ->assertForbidden();
    }

    public function test_employee_only_sees_own_orders(): void
    {
        $employeeA = User::factory()->create(['name' => 'Empleado A']);
        $employeeB = User::factory()->create(['name' => 'Empleado B']);
        $client = Client::query()->create(['name' => 'Cliente Z']);

        Order::query()->create([
            'order_number' => 'PED-EMP-001',
            'client_id' => $client->id,
            'created_by' => $employeeA->id,
            'order_date' => now()->toDateString(),
            'status' => 'pendiente',
            'stock_adjusted' => false,
            'subtotal' => 1000,
            'total' => 1000,
        ]);

        Order::query()->create([
            'order_number' => 'PED-EMP-002',
            'client_id' => $client->id,
            'created_by' => $employeeB->id,
            'order_date' => now()->toDateString(),
            'status' => 'pendiente',
            'stock_adjusted' => false,
            'subtotal' => 2000,
            'total' => 2000,
        ]);

        $this->actingAs($employeeA)
            ->get('/orders')
            ->assertOk()
            ->assertSee('PED-EMP-001')
            ->assertDontSee('PED-EMP-002');
    }

    public function test_employee_cannot_cancel_orders(): void
    {
        $employee = User::factory()->create();
        $client = Client::query()->create(['name' => 'Cliente Y']);
        $order = Order::query()->create([
            'order_number' => 'PED-CANCEL-001',
            'client_id' => $client->id,
            'created_by' => $employee->id,
            'order_date' => now()->toDateString(),
            'status' => 'pendiente',
            'stock_adjusted' => false,
            'subtotal' => 1000,
            'total' => 1000,
        ]);

        $this->actingAs($employee)
            ->post("/orders/{$order->id}/cancel")
            ->assertForbidden();
    }
}
