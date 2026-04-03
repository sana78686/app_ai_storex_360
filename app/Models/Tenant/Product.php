<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasMedia;

class Product extends Model
{
    use  HasMedia;
    // use SoftDeletes, HasMedia;

    protected $guarded = [];

    /**
     * The attributes that should be hidden from arrays/JSON
     */
    protected $hidden = ['slug'];

    /**
     * Prevent slug from being set or checked
     */
    protected static function boot()
    {
        parent::boot();

        // Remove slug from attributes if it exists before creating/updating
        static::creating(function ($product) {
            unset($product->slug);
            if (isset($product->attributes['slug'])) {
                unset($product->attributes['slug']);
            }
        });

        static::updating(function ($product) {
            unset($product->slug);
            if (isset($product->attributes['slug'])) {
                unset($product->attributes['slug']);
            }
        });

        static::saving(function ($product) {
            unset($product->slug);
            if (isset($product->attributes['slug'])) {
                unset($product->attributes['slug']);
            }
        });
    }


    /*
     | Relationships
    */
 public function translations()
    {
        return $this->hasMany(ProductTranslation::class);
    }

    public function translation()
    {
        return $this->hasOne(ProductTranslation::class)
            ->where('locale', app()->getLocale());
    }
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    // 🔥 Polymorphic Media (Product Images)
    public function media()
    {
        return $this->morphMany(Media::class, 'owner');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_product');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function variantOptions()
    {
        return $this->hasMany(ProductOption::class);
    }


     public function getNameAttribute()
    {
        return $this->translation?->name;
    }

    public function getSlugAttribute()
    {
        return $this->translation?->slug;
    }

    public function getDescriptionAttribute()
    {
        return $this->translation?->description;
    }

public function affiliates()
{
    return $this->hasMany(ProductAffiliate::class);
}


    // public function technicalInfos()
    // {
    //     return $this->hasMany(ProductTechnicalInfo::class);
    // }

    // public function infoFields()
    // {
    //     return $this->hasMany(ProductInfoField::class);
    // }

    /*
     | Helpers
 */

    public function isOutOfStock(): bool
    {
        if ($this->type === 'simple') {
            return $this->track_quantity && $this->stock <= 0;
        }

        return $this->variants()
            ->where('quantity', '>', 0)
            ->count() === 0;
    }

    /**
     * Adjust stock when order is placed
     */
    public function adjustStock(int $quantity, ProductVariant $variant = null): void
    {
        if ($variant) {
            $variant->decrement('quantity', $quantity);

            $this->update([
                'stock' => $this->variants()->sum('quantity'),
            ]);

            return;
        }

        if ($this->track_quantity) {
            $this->decrement('stock', $quantity);
        }
    }

    //
    // | Route Model Binding by Slug
    //
    //

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->whereHas('translations', function ($q) use ($value) {
            $q->where('locale', app()->getLocale())
              ->where('slug', $value);
        })->firstOrFail();
    }
}
