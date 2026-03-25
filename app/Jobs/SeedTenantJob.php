<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use App\Models\{
    Tenant,
    User,
};
use Database\Seeders\Tenant\CategorySeeder;

class SeedTenantJob implements ShouldQueue
{
    use  Queueable;

    protected $tenant;

    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;
    }

    public function handle(): void
    {
        // Run everything inside tenant DB
        $this->tenant->run(function () {

            /*
             * 1. Create tenant admin user
             */
            User::firstOrCreate(
                ['email' => $this->tenant->email],
                [
                    'name'     => $this->tenant->name,
                    'password' => $this->tenant->password,
                ]
            );

            /*
             * 2. Run Category Seeder
             */
            Artisan::call('db:seed', [
                '--class' => CategorySeeder::class,
                '--force' => true,
            ]);

        });
    }
}
