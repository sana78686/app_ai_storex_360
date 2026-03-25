<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class InvoiceSetting extends Model
{
    protected $fillable = [
        'prefix',
        'next_number',
        'default_currency',
        // 'logo',
        'footer_text',
        'intro_text',
        'auto_generate_on_paid',
    ];

    protected $casts = [
        'auto_generate_on_paid' => 'boolean',
    ];
}
