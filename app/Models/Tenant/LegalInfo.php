<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class LegalInfo extends Model
{
    protected $fillable = [
        'company_name',
        'legal_form',
        'registered_address',
        'authorized_representative',
        'email',
        'phone',
        'registration_court',
        'commercial_register_number',
        'vat_id',
        'oss',
    ];
}
