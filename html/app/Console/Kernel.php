<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->everyMinute();

        // $schedule->command('SendEmails user1')->everyMinute()->runInBackground();
        // $schedule->command('SendEmails user2')->everyMinute()->runInBackground();
        // $schedule->command('SendEmails user3')->everyMinute()->runInBackground();

        $uuid = uniqid();

        $schedule->call(function () use ($uuid) {
            Log::info('command 1 ' . $uuid);
            sleep(62);
        })->everyThreeMinutes();

        $schedule->call(function () use ($uuid) {
            Log::info('command 2 ' . $uuid);
        })->at('23:02');

        // $schedule->call(function () use ($uuid) {
        //     Log::info('command 3 ' . $uuid);
        //     sleep(62);
        // })->everyMinute();
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
