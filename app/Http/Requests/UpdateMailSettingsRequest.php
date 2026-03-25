<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMailSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'provider' => 'required|string|in:php,smtp,mailgun,microsoft,sendgrid,sparkpost',

            // SMTP
            'smtp_host' => 'nullable|string',
            'smtp_port' => 'nullable|string',
            'smtp_username' => 'nullable|string',
            'smtp_password' => 'nullable|string',
            'smtp_encryption' => 'nullable|string|in:ssl,tls',

            // Mailgun
            'mailgun_domain' => 'nullable|string',
            'mailgun_api_key' => 'nullable|string',
            'mailgun_region' => 'nullable|string|in:us,eu',

            // Microsoft
            'microsoft_client_id' => 'nullable|string',
            'microsoft_tenant_id' => 'nullable|string',
            'microsoft_client_secret' => 'nullable|string',

            // SendGrid
            'sendgrid_api_key' => 'nullable|string',

            // SparkPost
            'sparkpost_api_key' => 'nullable|string',
        ];
    }
}
