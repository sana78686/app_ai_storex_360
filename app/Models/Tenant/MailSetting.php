<?php
namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class MailSetting extends Model
{
    protected $fillable = [
        'provider',
        'smtp_host',
        'smtp_port',
        'smtp_username',
        'smtp_password',
        'smtp_encryption',

        'mailgun_domain',
        'mailgun_api_key',
        'mailgun_region',

        'microsoft_client_id',
        'microsoft_tenant_id',
        'microsoft_client_secret',

        'sendgrid_api_key',

        'sparkpost_api_key',
    ];
}
