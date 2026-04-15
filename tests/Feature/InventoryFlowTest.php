<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Company;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_confirming_order_does_not_limit_sale_by_stock_quantity(): void
    {
        Company::current();
        $admin = User::factory()->admin()->create();
        $client = Client::query()->create(['name' => 'Cliente Inventario']);
        $product = Product::query()->create([
            'name' => 'Tabla de Patada',
            'sku' => 'INV-001',
            'price' => 40000,
            'stock' => 0,
            'is_active' => true,
        ]);

        $this->actingAs($admin)->post('/orders', [
            'client_id' => $client->id,
            'order_date' => now()->toDateString(),
            'items' => [
                [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => 25,
                    'unit_price' => 40000,
                ],
            ],
        ])->assertRedirect('/orders');

        $order = \App\Models\Order::query()->firstOrFail();

        $this->actingAs($admin)->post("/orders/{$order->id}/confirm")
            ->assertRedirect("/orders/{$order->id}");

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'confirmado',
            'stock_adjusted' => false,
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 0,
        ]);
    }

    public function test_admin_can_create_product_without_stock_field(): void
    {
        Company::current();
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/products', [
            'name' => 'Producto mayorista',
            'sku' => 'INV-WHS-001',
            'price' => 18000,
            'description' => 'Producto para ventas al por mayor',
            'is_active' => 1,
        ])->assertRedirect('/products');

        $this->assertDatabaseHas('products', [
            'name' => 'Producto mayorista',
            'sku' => 'INV-WHS-001',
            'stock' => 0,
        ]);
    }

    public function test_admin_can_delete_unused_product_from_inventory(): void
    {
        Company::current();
        $admin = User::factory()->admin()->create();
        $product = Product::query()->create([
            'name' => 'Producto libre',
            'sku' => 'INV-DEL-001',
            'price' => 25000,
            'stock' => 4,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->delete("/products/{$product->id}")
            ->assertRedirect('/products');

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_admin_cannot_delete_product_used_in_sales_history(): void
    {
        Company::current();
        $admin = User::factory()->admin()->create();
        $client = Client::query()->create(['name' => 'Cliente Historial']);
        $product = Product::query()->create([
            'name' => 'Producto con ventas',
            'sku' => 'INV-LOCK-001',
            'price' => 50000,
            'stock' => 6,
            'is_active' => true,
        ]);

        $order = Order::query()->create([
            'order_number' => 'PED-HIST-001',
            'client_id' => $client->id,
            'created_by' => $admin->id,
            'order_date' => now()->toDateString(),
            'status' => 'pendiente',
            'stock_adjusted' => false,
            'subtotal' => 50000,
            'total' => 50000,
        ]);

        OrderItem::query()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 1,
            'unit_price' => 50000,
            'line_total' => 50000,
        ]);

        $this->actingAs($admin)
            ->delete("/products/{$product->id}")
            ->assertRedirect('/products');

        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }
}
