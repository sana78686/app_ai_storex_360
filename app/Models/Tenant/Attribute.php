<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * Relationship: Attribute has many values.
     */
    public function values()
    {
        return $this->hasMany(AttributeValue::class);
    }

    /**
     * Relationship: Belongs to many products with values.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_attribute')
                    ->withPivot('attribute_value_id');
    }
}