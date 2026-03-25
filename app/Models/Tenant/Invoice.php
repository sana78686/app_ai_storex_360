<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'order_id',
        'invoice_number',
        'status',
        'invoice_date',
        'due_date',
        'delivery_date',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'total',
        'currency',
        'payment_method',
        'billing_address',
        'shipping_address',
        'template_id',
        'pdf_path',
    ];

    protected $casts = [
        'billing_address' => 'array',
        'shipping_address' => 'array',
        'invoice_date' => 'date',
        'due_date' => 'date',
        'delivery_date' => 'date',
    ];

    // Relations
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function template()
    {
        return $this->belongsTo(InvoiceTemplate::class);
    }
    public function media()
{
    return $this->morphMany(Media::class, 'owner');
}

}
