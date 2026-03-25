<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'product_id',
        'position',
        'title',
        'description',
        'quantity',
        'unit',
        'unit_price',
        'subtotal',
        'tax_percent',
        'tax_amount',
        'total',
    ];

    protected $casts = [
        'quantity' => 'float',
        'unit_price' => 'float',
        'subtotal' => 'float',
        'tax_percent' => 'float',
        'tax_amount' => 'float',
        'total' => 'float',
    ];

    // Relations
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
