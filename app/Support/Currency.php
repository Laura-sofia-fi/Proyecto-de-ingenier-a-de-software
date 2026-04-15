<?php

namespace App\Support;

class Currency
{
    public static function cop(float|int|string|null $value): string
    {
        return '$ '.number_format((float) $value, 0, ',', '.');
    }
}
