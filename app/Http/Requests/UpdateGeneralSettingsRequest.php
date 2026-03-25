<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGeneralSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
{
    return [
        'company_name'        => 'nullable|string|max:255',
        'default_email'       => 'nullable|email|max:255',

        'country'             => 'nullable|string|max:255',
        'country_code'        => 'nullable|string|max:10',
        'state'               => 'nullable|string|max:255',
        'city'                => 'nullable|string|max:255',
        'zip'                 => 'nullable|string|max:50',

        'ip_address'          => 'nullable|ip',
        'latitude'            => 'nullable|numeric',
        'longitude'           => 'nullable|numeric',

        'currency'            => 'nullable|string|max:10',
        'calling_code'        => 'nullable|string|max:10',

        'timezone'            => 'nullable|string|max:255',

        'date_format'         => 'nullable|string|max:50',
        'datetime_format'     => 'nullable|string|max:50',

        'maintenance_mode'    => 'boolean',
        'maintenance_message' => 'nullable|string',

        'logo'                => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',

        'legal_details'       => 'nullable|string',
        'description'         => 'nullable|string',
    ];
}

}
