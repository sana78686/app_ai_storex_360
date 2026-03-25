<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class ProductTechnicalInfo extends Model
{

    protected $fillable = [
        'product_id',
        'name',
        'value',
    ];

    // 🔗 Relation with Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
