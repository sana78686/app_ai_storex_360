<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'payment_method',
        'payment_provider',
        'transaction_id',
        'amount',
        'status',
        'paid_at',
        'metadata',
        'provider',
        'provider_payment_id',
        'currency',
        'provider_payload',
    ];

    protected $casts = [
        'provider_payload' => 'array',
        'metadata' => 'array',
        'paid_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
