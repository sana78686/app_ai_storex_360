<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\BlockExpiredTrials;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Temporarily run the command every minute for testing
        $schedule->command(BlockExpiredTrials::class) // Use class reference here
                ->everyMinute()
                ->appendOutputTo(storage_path('logs/debug-schedule.log'));

        // Remember to change this back to ->daily()->at('00:00') for production
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
} 