<?php

namespace App\Support;

use App\Models\Company;
use App\Models\Invoice;
use App\Models\Order;

class NumberGenerator
{
    public static function nextOrderNumber(): string
    {
        $next = (int) Order::query()->count() + 1;

        return 'PED-'.str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }

    public static function nextInvoiceNumber(): string
    {
        $company = Company::current();
        $next = (int) Invoice::query()->count() + 1;

        return strtoupper($company->invoice_prefix).'-'.str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }
}
