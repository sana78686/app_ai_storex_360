<?php

namespace App\Models\Tenant;

use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    protected $fillable = ['name', 'guard_name'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($permission) {
            $permission->guard_name = 'sanctum';
        });
    }

    public function newQuery()
    {
        return parent::newQuery()->where('guard_name', 'sanctum');
    }
}
