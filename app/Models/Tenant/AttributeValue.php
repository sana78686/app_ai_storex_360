<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AttributeValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'attribute_id',
        'value',
    ];

    /**
     * Relationship: Each value belongs to one attribute.
     */
    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    /**
     * Relationship: This value can be used by many products via pivot.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_attribute')
                    ->withPivot('attribute_id');
    }
}
