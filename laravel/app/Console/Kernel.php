<?php

namespace App\Console;

use App\VerifyRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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
        // Commands\Inspire::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $periodOption = DB::table('wp_options')->where('option_name', 'transactions_purge_period')->first();
            $days = $periodOption ? $periodOption->option_value : 30;
            $transactions = DB::table('transactions')->where('audit_record', false)->where('created_at', '<=', Carbon::now()->subDays($days))->get();
            foreach ($transactions as $transaction) {
                $verifyRequest = VerifyRequest::where('transactions', 'LIKE', '%' . $transaction->id . '%')->first();
                if (!$verifyRequest) {
                    Transaction::find($transaction->id)->delete();
                }
            }
        })->hourly();
    }
}
