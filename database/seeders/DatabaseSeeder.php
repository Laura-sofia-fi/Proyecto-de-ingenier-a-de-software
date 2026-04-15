<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Company;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Company::query()->updateOrCreate(
            ['id' => 1],
            [
                'name' => 'NATADINATTA',
                'address' => 'Cra 10 #20-30',
                'city' => 'Bogotá',
                'nit' => '901234567-8',
                'phone' => '3001234567',
                'email' => 'admin@natadinatta.com',
                'invoice_prefix' => 'NAT',
            ]
        );

        User::factory()->admin()->create([
            'name' => 'Administrador NATADINATTA',
            'email' => 'admin@natadinatta.com',
        ]);

        User::factory()->create([
            'name' => 'Empleado NATADINATTA',
            'email' => 'empleado@natadinatta.com',
        ]);

        Client::query()->create([
            'name' => 'Cliente Demo',
            'document_number' => '1020304050',
            'phone' => '3005556677',
            'email' => 'cliente@demo.com',
            'address' => 'Calle 123',
            'city' => 'Bogotá',
        ]);

        Product::query()->create([
            'name' => 'Aletas Natación',
            'sku' => 'NAT-AL-001',
            'description' => 'Aletas de entrenamiento para piscina.',
            'price' => 85000,
            'stock' => 20,
            'is_active' => true,
        ]);

        Product::query()->create([
            'name' => 'Gafas Profesionales',
            'sku' => 'NAT-GA-002',
            'description' => 'Gafas con protección UV para entrenamiento.',
            'price' => 65000,
            'stock' => 15,
            'is_active' => true,
        ]);
    }
}
