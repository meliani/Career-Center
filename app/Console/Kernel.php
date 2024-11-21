<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('projects:detect-completed-defenses')
            // ->everySecond();
            ->dailyAt('15:40');
        $schedule->command(\Spatie\Health\Commands\RunHealthChecksCommand::class)->everyMinute();
        $schedule->command(\Spatie\Health\Commands\DispatchQueueCheckJobsCommand::class)->everyMinute();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
