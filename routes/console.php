<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

app()->singleton(Schedule::class, function ($app) {
    return tap(new Schedule(), function ($schedule) {
        $schedule->command('update:keep-status')->everyMinute();
        $schedule->command('app:reset-keep-code')->dailyAt('00:00');
    });
});
