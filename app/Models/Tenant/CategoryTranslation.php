<?php
namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class CategoryTranslation extends Model
{
    protected $fillable = [
        'category_id',
        'locale',
        'name',
        'slug',
        'description',
    ];
 public $timestamps = false;
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
