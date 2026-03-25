<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\DatabaseConfig;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'email',
        'password',
        'name',
        'domain',
        'confirmation_token',
        'confirmation_token_expires_at',
        'status',
        'trial_ends_at',
        'is_approved',
        'approved_at',
        'phone_no',
        'store_name',
        'data',
        'stripe_customer_id', 'stripe_subscription_id', 'subscription_status',
        'subscription_ends_at',
        'stripe_account_id',
        'stripe_access_token',
        'stripe_refresh_token',
        'stripe_publishable_key',
        'stripe_scope',
        'stripe_connected_at',
    ];

    // protected $hidden = ['password'];

    // protected $casts = [
    //     'data' => 'array',
    //     'trial_ends_at' => 'datetime',
    //     'approved_at' => 'datetime',
    //     'confirmation_token_expires_at' => 'datetime',
    //     'is_approved' => 'boolean'
    // ];

    public static function getCustomColumns(): array
{
    return [
        'id',
        'email',
        'password',
        'name',
        'domain',
        'confirmation_token',
        'confirmation_token_expires_at',
        'status',
        'trial_ends_at',
        'is_approved',
        'approved_at',
        'phone_no',
        'store_name',
        'stripe_customer_id', 'stripe_subscription_id', 'subscription_status',
        'subscription_ends_at',
        'stripe_account_id',
        'stripe_access_token',
        'stripe_refresh_token',
        'stripe_publishable_key',
        'stripe_scope',
        'stripe_connected_at',
    ];
}


    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

 public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function currentSubscription()
    {
        return $this->hasOne(Subscription::class)->latestOfMany();
    }
       public function primaryDomain()
    {
        return $this->domains()->where('is_primary', true)->first();
    }

}

