<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model
{
    // protected $connection = 'tenant';
    protected $fillable = [
        // Company Info
        'company_name',
        'default_email',
        'logo',
        'description',

        // Legal Details
        // 'legal_details',

        // Location
        'country',
        'country_code',
        'state',
        'city',
        'zip',

        // Geo + IP
        'ip_address',
        'latitude',
        'longitude',

        // System Settings
        'currency',
        'calling_code',
        'timezone',
        'date_format',
        'datetime_format',

        // Maintenance
        'maintenance_mode',
        'maintenance_message',
        // theme
        'theme',
    ];

    public function media()
    {
        return $this->morphMany(Media::class, 'owner');
    }
}
