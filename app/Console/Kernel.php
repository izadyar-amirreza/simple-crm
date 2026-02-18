<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Register the commands for the application.
     */
    protected $commands = [
        \App\Console\Commands\CleanupActivityLogs::class,
    ];

        protected function schedule(Schedule $schedule): void
    {
        $schedule
            ->command('activity:cleanup --days=90')
            ->dailyAt('03:00')
            ->withoutOverlapping()
            ->onOneServer();
    }


    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

