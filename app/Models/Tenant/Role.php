<?php

namespace App\Models\Tenant;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    protected $fillable = ['name', 'guard_name'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($role) {
            $role->guard_name = 'sanctum';
        });
    }
}
