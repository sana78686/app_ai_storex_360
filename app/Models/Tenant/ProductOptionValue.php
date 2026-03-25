<?php

namespace App\Models\Tenant;



use Illuminate\Database\Eloquent\Model;

class ProductOptionValue extends Model
{
    protected $fillable = [
        'product_option_id',
        'code',
    ];


    public function option()
    {
        return $this->belongsTo(ProductOption::class);
    }

    public function translations()
    {
        return $this->hasMany(ProductOptionValueTranslation::class);
    }

    public function translation()
    {
        return $this->hasOne(ProductOptionValueTranslation::class)
            ->where('locale', app()->getLocale());
    }

    public function getNameAttribute()
    {
        return $this->translation?->name;
    }
}

