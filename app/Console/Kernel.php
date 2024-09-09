<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\MonthlyDeleteUserLog::class,
        Commands\SuspendBusinessNotPayTransactionFee::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
//        //MonthlyDeleteUserLog
//        $schedule->command('monthlyDeleteUserLog')
//            ->timezone(env('APP_TIMEZONE'))
//            ->monthly();
//
//        //SuspendBusinessNotPayTransactionFee
//        $schedule->command('suspendBusinessNotPayTransactionFee')
//            ->timezone(env('APP_TIMEZONE'))
//            ->daily();
    }
}
