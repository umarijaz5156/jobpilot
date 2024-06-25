<?php

namespace App\Console;

use App\Console\Commands\ResetDB;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        ResetDB::class,
        'App\Console\Commands\UpdateJobStatus',
    ];

    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('reset:db')->everyMinute();
        $schedule->command('reset:db')->everyThirtyMinutes();
        $schedule->command('jobs:updatestatus')
            ->dailyAt('13:00')->runInBackground();
        $schedule->command('update:exchange-rates')->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
