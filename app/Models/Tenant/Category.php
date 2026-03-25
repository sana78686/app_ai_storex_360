<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Category extends Model
{
    // protected $fillable = [
    //     'name', 'slug', 'parent_id', 'shopify_id', 'vertical', 'prefix', 'level', 'is_active'
    // ];

     protected $guarded = [];

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::saving(function ($category) {
    //         // Generate slug if empty or name changed
    //         if (empty($category->slug) || $category->isDirty('name')) {
    //             $slug = Str::slug($category->name);

    //             $count = static::where('slug', 'LIKE', "{$slug}%")
    //                 ->where('id', '!=', $category->id)
    //                 ->count();

    //             $category->slug = $count ? "{$slug}-" . ($count + 1) : $slug;
    //         }
    //     });
    // }

     protected $with = ['translation'];


  public function translations()
    {
        return $this->hasMany(CategoryTranslation::class);
    }

    public function translation()
    {
        return $this->hasOne(CategoryTranslation::class)
            ->where('locale', config('app.fallback_locale', 'en'));
    }
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'category_product');
    }
     public function getNameAttribute()
    {
        return $this->translation?->name;
    }

    public function getSlugAttribute()
    {
        return $this->translation?->slug;
    }

    /*
    |--------------------------------------------------------------------------
    | Route Binding by Slug
    |--------------------------------------------------------------------------
    */

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->whereHas('translations', function ($q) use ($value) {
            $q->where('locale', app()->getLocale())
              ->where('slug', $value);
        })->firstOrFail();
    }

}
