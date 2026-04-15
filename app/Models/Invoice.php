<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'order_id',
        'invoice_number',
        'issue_date',
        'status',
        'payment_status',
        'company_name',
        'company_address',
        'company_city',
        'company_nit',
        'client_name',
        'client_document',
        'client_phone',
        'subtotal',
        'total',
        'total_paid',
        'balance_due',
    ];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'subtotal' => 'decimal:2',
            'total' => 'decimal:2',
            'total_paid' => 'decimal:2',
            'balance_due' => 'decimal:2',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function refreshPaymentSummary(): void
    {
        $totalPaid = (float) $this->payments()->sum('amount');
        $balanceDue = max((float) $this->total - $totalPaid, 0);

        $this->forceFill([
            'total_paid' => $totalPaid,
            'balance_due' => $balanceDue,
            'payment_status' => $balanceDue <= 0 ? 'pagado' : ($totalPaid > 0 ? 'abono' : 'pendiente'),
        ])->save();
    }
}
