<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Scheduling\Schedule;
use App\Console\Commands\SendLoanReminders;


Schedule::command('loans:send-reminders')->dailyAt('09:00');
