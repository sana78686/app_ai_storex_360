<?php

namespace App\Listeners;

use App\Models\Tenant\MailSetting;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class TenantMailConfig
{
    public function handle($event): void
    {
        try {
            $settings = MailSetting::first();
        } catch (QueryException $e) {
            // mail_settings table may not exist yet (tenant migrations not run)
            return;
        }

        if (! $settings) {
            return;
        }

        if ($settings->provider === 'php') {
            Config::set('mail.default', 'sendmail');

            return;
        }

        if ($settings->provider === 'smtp') {
            $scheme = $this->smtpScheme($settings);

            Config::set('mail.default', 'smtp');
            Config::set('mail.mailers.smtp', [
                'transport' => 'smtp',
                'scheme' => $scheme,
                'url' => null,
                'host' => $settings->smtp_host,
                'port' => (int) $settings->smtp_port,
                'username' => $settings->smtp_username,
                'password' => $settings->smtp_password,
                'timeout' => null,
                'local_domain' => config('mail.mailers.smtp.local_domain'),
            ]);

            $from = $settings->smtp_username ?: config('mail.from.address');
            Config::set('mail.from.address', $from);
            Config::set('mail.from.name', config('mail.from.name'));

            return;
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
            Config::set('mail.from.address', 'noreply@'.$settings->mailgun_domain);
            Config::set('mail.from.name', config('mail.from.name'));

            return;
        }

        if (in_array($settings->provider, ['sendgrid', 'microsoft', 'sparkpost'], true)) {
            Log::warning('TenantMailConfig: mail provider not wired yet; configure SMTP or Mailgun in tenant settings.', [
                'provider' => $settings->provider,
            ]);
        }
    }

    /**
     * Laravel 12 / Symfony Mailer: use scheme smtps for SSL (e.g. port 465), tls for STARTTLS.
     */
    private function smtpScheme(MailSetting $settings): ?string
    {
        $enc = strtolower((string) $settings->smtp_encryption);
        $port = (int) $settings->smtp_port;

        if ($enc === 'ssl' || $port === 465) {
            return 'smtps';
        }

        if ($enc === 'tls' || $port === 587) {
            return 'tls';
        }

        return null;
    }
}
