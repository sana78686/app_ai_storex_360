<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Builds tenant-facing absolute URLs using the same scheme and non-default port as APP_URL
 * (e.g. http://…:8000 locally, https://… with no port in production).
 */
final class TenantUrl
{
    public static function to(string $domain, string $path = ''): string
    {
        $parsed = parse_url((string) config('app.url')) ?: [];
        $scheme = $parsed['scheme'] ?? 'https';

        $port = '';
        if (! empty($parsed['port'])) {
            $default = $scheme === 'https' ? 443 : 80;
            if ((int) $parsed['port'] !== $default) {
                $port = ':'.$parsed['port'];
            }
        }

        $path = $path !== '' ? '/'.ltrim($path, '/') : '';

        return "{$scheme}://{$domain}{$port}{$path}";
    }
}
