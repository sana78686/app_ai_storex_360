<?php
namespace App\Listeners;

use App\Models\Tenant\MailSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\QueryException;

class TenantMailConfig
{
    public function handle($event)
    {
        try {
            $settings = MailSetting::first();
        } catch (QueryException $e) {
            // mail_settings table may not exist yet (tenant migrations not run)
            return;
        }

        if (! $settings) return;

        if ($settings->provider === 'smtp') {
            Config::set('mail.default', 'smtp');

            Config::set('mail.mailers.smtp', [
                'transport' => 'smtp',
                'host' => $settings->smtp_host,
                'port' => $settings->smtp_port,
                'username' => $settings->smtp_username,
                'password' => $settings->smtp_password,
                'encryption' => $settings->smtp_encryption,
            ]);

            Config::set('mail.from.address', $settings->smtp_username);
        }

        if ($settings->provider === 'mailgun') {
            Config::set('mail.default', 'mailgun');

            Config::set('services.mailgun', [
                'domain' => $settings->mailgun_domain,
                'secret' => $settings->mailgun_api_key,
                'endpoint' => $settings->mailgun_region === 'eu'
                    ? 'api.eu.mailgun.net'
                    : 'api.mailgun.net',
            ]);

            Config::set('mail.from.address', 'noreply@' . $settings->mailgun_domain);
        }

        // Add other providers here...
    }
}
