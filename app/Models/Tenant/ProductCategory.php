<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductCategory extends Model
{
    use HasFactory;

    protected $table = 'product_category';

    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'category_id',
    ];
}