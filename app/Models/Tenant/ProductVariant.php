<?php

namespace App\Models\Tenant;


use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'owner');
    }


     public function optionValues()
    {
        return $this->belongsToMany(
            ProductOptionValue::class,
            'product_variant_values'
        );
    }
}
