<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Console\Command;

class BlockExpiredTrials extends Command
{
    protected $signature = 'tenants:block-expired-trials';
    protected $description = 'Block tenants whose trial period has expired';

    public function handle(): int
    {
        \Log::info("Laravel scheduler ran at: " . now());
        // Log the current time to see what Carbon::now() resolves to
        $currentTime = Carbon::now();
        $this->info("BlockExpiredTrials command started at: {$currentTime->toDateTimeString()}");
        
        $expiredTenants = Tenant::where('status', 'trial')
            ->where('trial_ends_at', '<', $currentTime)
            ->where('is_approved', false)
            ->get();

        if ($expiredTenants->isEmpty()) {
            $this->info("No expired trial tenants found.");
        } else {
            $this->info("Found {$expiredTenants->count()} expired trial tenants.");
        }

        foreach ($expiredTenants as $tenant) {
            $tenant->block();
            $this->info("Blocked tenant: {$tenant->name} (ID: {$tenant->id})");
        }

        $this->info("Blocked {$expiredTenants->count()} expired trial tenants.");
        
        return self::SUCCESS;
    }
} 