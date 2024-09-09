<?php

namespace App\Console\Commands;

use App\Models\UserLog;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MonthlyDeleteUserLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monthlyDeleteUserLog';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Monthly delete data from table user log";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $startDate = Carbon::now()->startOfWeek()->format('Y-m-d');
        $endDate = Carbon::now()->endOfWeek()->format('Y-m-d');

        UserLog::whereRaw('Date(created_at) < "' . $startDate . '"')->delete();

        info('Delete User Log (Except Current Week) CronJob: ' . Carbon::today()->format('Y-m-d'));
    }
}
