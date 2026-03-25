<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable
{
    use HasApiTokens;

    protected $fillable = [
        'email',
        'password',
        'first_name',
        'last_name',
        'phone',
        'type',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Full name accessor (for display).
     */
    public function getNameAttribute(): string
    {
        $parts = array_filter([$this->first_name, $this->last_name]);
        return implode(' ', $parts) ?: (string) $this->email;
    }

    // ------------------
    // Relationships
    // ------------------

    public function addresses(): HasMany
    {
        return $this->hasMany(CustomerAddress::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // ------------------
    // Helpers
    // ------------------

    public function isGuest(): bool
    {
        return $this->type === 'guest';
    }

    public function isRegistered(): bool
    {
        return $this->type === 'registered';
    }
}
