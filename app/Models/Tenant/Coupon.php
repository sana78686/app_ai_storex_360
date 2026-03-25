<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'type',
        'value',
        'recurring',
        'repeating_months',
        'min_order_amount',
        'max_discount',
        'start_date',
        'end_date',
        'active',
        'first_order_only',
        'logged_in_only',
        'apply_once_per_order',
        'apply_only_if_products_match',
        'usage_limit',
        'usage_per_customer',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'active' => 'boolean',
        'first_order_only' => 'boolean',
        'logged_in_only' => 'boolean',
        'apply_once_per_order' => 'boolean',
        'apply_only_if_products_match' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\User::class, 'coupon_user')
            ->withPivot('used_at')
            ->withTimestamps();
    }

    public function isValid(): bool
    {
        $now = Carbon::now();

        if (! $this->active) return false;

        if ($this->start_date && $now->lt($this->start_date)) return false;

        if ($this->end_date && $now->gt($this->end_date)) return false;

        return true;
    }


    public function usageCount(): int
    {
        return $this->users()->count();
    }
    public function categories()
{
    return $this->belongsToMany(Category::class, 'coupon_category');
}

}
