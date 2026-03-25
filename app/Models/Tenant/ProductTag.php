<?php
namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductTag extends Pivot
{
    protected $table = 'product_tag';

    protected $fillable = [
        'product_id',
        'tag_id',
    ];


//     public function products()
// {
//     return $this->belongsToMany(Product::class, 'product_tag')
//                 ->using(ProductTag::class);
// }

    public $timestamps = false;
}
