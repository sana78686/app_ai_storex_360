<?php

namespace App\Listeners;

use Stancl\Tenancy\Events\DatabaseMigrated;
use App\Models\Tenant\GeneralSetting;
use App\Helpers\GeoHelper;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SetupTenantGeneralSettings implements ShouldQueue
{
   public function handle(DatabaseMigrated $event): void
{
    $tenant = $event->tenant;
Log::info('Tenant object:', ['tenant' => $tenant]);

   $ip = $tenant->data['ip'] ;

if (! $ip) {
    Log::warning("⚠️ Tenant {$tenant->id} has no IP stored, using default for testing");
    $ip = '176.199.93.89';
}


    $geo = GeoHelper::getIPLocation($ip);

    GeneralSetting::create([
        'country'           => $geo['country_name'] ?? null,
        'currency'          => $geo['currency']['code'] ?? null,
        'timezone'          => $geo['time_zone']['name'] ?? 'UTC',
        'ip_address'        => $ip,
        'maintenance_mode'  => false,
    ]);
}

}
