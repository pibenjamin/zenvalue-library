<?php

use Illuminate\Support\Facades\Artisan;
use App\Console\Commands\SendLoanReminders;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Log;


$filePath = storage_path('logs/schedule.log');

Schedule::command('loans:send-reminders')
    ->dailyAt('09:00')
    ->appendOutputTo($filePath)
    ->emailOutputTo('benjaminpiscart@gmail.com');


Schedule::command('schema:dump')
    ->dailyAt('00:00');

    