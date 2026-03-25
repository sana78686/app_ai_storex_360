<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class ProductOption extends Model
{
    protected $fillable = [
        'product_id',
        'position'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function translations()
    {
        return $this->hasMany(ProductOptionTranslation::class);
    }

    public function translation()
    {
        return $this->hasOne(ProductOptionTranslation::class)
            ->where('locale', app()->getLocale());
    }

    public function values()
    {
        return $this->hasMany(ProductOptionValue::class);
    }

    public function getNameAttribute()
    {
        return $this->translation?->name;
    }
}



