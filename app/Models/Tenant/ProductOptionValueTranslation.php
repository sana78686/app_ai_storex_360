<?php
namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class ProductOptionValueTranslation extends Model
{
    protected $fillable = [
        'product_option_value_id',
        'locale',
        'name',
    ];

    public function value()
    {
        return $this->belongsTo(ProductOptionValue::class);
    }
}


