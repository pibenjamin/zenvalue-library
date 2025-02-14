<?php

use Illuminate\Support\Facades\Artisan;
use App\Console\Commands\SendLoanReminders;
use Illuminate\Support\Facades\Schedule;


Schedule::command('loans:send-reminders')->dailyAt('09:00');
