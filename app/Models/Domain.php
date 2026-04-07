<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    /** Domains live on the central DB, not the per-tenant database. */
    protected $connection = 'central';

    protected $guarded = [];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
        public function scopeActive($q)
    {
        return $q->where('status', 'active');
    }

    public function scopePrimary($q)
    {
        return $q->where('is_primary', true);
    }
}
