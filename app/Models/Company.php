<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name',
        'address',
        'city',
        'nit',
        'phone',
        'email',
        'invoice_prefix',
    ];

    public static function current(): self
    {
        return static::query()->firstOrCreate(
            ['id' => 1],
            [
                'name' => 'NATADINATTA',
                'address' => 'Dirección principal',
                'city' => 'Bogotá',
                'nit' => '900000000-0',
                'phone' => '3000000000',
                'email' => 'admin@natadinatta.com',
                'invoice_prefix' => 'NAT',
            ]
        );
    }
}
