<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

    class InvoiceTemplate extends Model
{
    protected $fillable = [
        'tenant_id',
        'name',
        'is_default',
        'template_html',
        'template_css',
        'settings',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'settings' => 'array',
    ];
}




