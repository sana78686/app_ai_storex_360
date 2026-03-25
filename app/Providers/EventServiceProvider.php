<?php

namespace App\Providers;


use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\TenantCreated;
use App\Listeners\SetupTenantGeneralSettings;
class EventServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }


    // protected $listen = [
    //     TenantCreated::class => [
    //         SetupTenantGeneralSettings::class,
    //     ],
    // ];
}
