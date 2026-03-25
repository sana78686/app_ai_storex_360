<?php
namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class ProductOptionTranslation extends Model
{
    protected $fillable = [
        'product_option_id',
        'locale',
        'name',
    ];

    public $timestamps = false;

    public function option()
    {
        return $this->belongsTo(ProductOption::class);
    }
}

