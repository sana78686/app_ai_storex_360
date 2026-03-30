<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Stancl\Tenancy\Events\TenancyInitialized;
use App\Listeners\TenantMailConfig;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        /**
         * Load tenant mail (SMTP / Mailgun / etc.) after the tenant DB is active.
         * Central mail stays on .env defaults for landlord routes.
         */
        \Event::listen(TenancyInitialized::class, function ($event) {
            (new TenantMailConfig)->handle($event);

            $tenant = $event->tenancy->tenant;
            Log::info('✅ Tenancy Initialized for tenant: ' . $tenant->id);
            Log::info('✅ DB in use: ' . DB::connection()->getDatabaseName());
        });
    }
}
