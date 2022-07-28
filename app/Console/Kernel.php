<?php

namespace App\Console;

use App\Mail\Report1Day;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
//        $schedule->call('App\IncomeCall::loadCalls')->name('loadCalls')->withoutOverlapping();
//        $schedule->call('App\IncomeCall::handleNewCalls')->name('handleNewCalls')->withoutOverlapping();
        $schedule->call('App\AsterDialer::dialQueue')->name('dialQueue')->withoutOverlapping();
        $schedule->call('App\AsterCall::handleNewCalls')->name('handleNewCalls')->withoutOverlapping();
        $schedule->call('App\Entity::generateCallQueueForAll')->name('generate-call-queue-for-all-entities')->withoutOverlapping();
        $schedule->call('App\WorkShift::checkFinishingShifts')->name('checkFinishingShifts')->withoutOverlapping();
        $schedule->call('App\WorkShift::checkExceededShifts')->name('checkExceededShifts')->everyMinute()->withoutOverlapping();
        $schedule->call('App\GuardedObject::checkObjectsWithoutGuards')->name('checkObjectsWithoutGuards')->everyMinute()->withoutOverlapping();
        $schedule->call(function () {
            $fromDate = Carbon::yesterday()->hour(20)->minute(0)->toDateTimeString();
            $toDate = Carbon::today()->hour(19)->minute(59)->toDateTimeString();
            $email = DB::table('global_settings')->where('name', 'report_email')->value('value');
            Mail::to($email)->send(new Report1Day($fromDate, $toDate));
        })->name('dailyReport')->withoutOverlapping()->dailyAt('20:05')->onOneServer();

        $schedule->call(function (){
            DB::table('action_logs')->truncate();
        })->cron('0 0 6 * *');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
