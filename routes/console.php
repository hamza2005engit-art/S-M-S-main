<?php

use App\Jobs\GenerateReportsJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Schedule::command('salary:generate')
   ->everyMinute();// ->monthlyOn(11, '15:49');

//     Schedule::command('attendance:generate')->everyMinute();
//         // ->weekdays("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday")
//         // ->at('06:00');
//  Schedule::job(new GenerateReportsJob())->dailyAt('00:00');
