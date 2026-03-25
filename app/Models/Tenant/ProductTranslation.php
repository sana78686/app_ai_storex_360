<?php
namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class ProductTranslation extends Model
{
    protected $fillable = [
        'product_id',
        'locale',
        'name',
        'slug',
        'description',
    ];
 public $timestamps = false;
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
