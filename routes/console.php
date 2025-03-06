<?php

use App\Console\Commands\UpdateKeepStatus;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

app()->singleton(Schedule::class, function ($app) {
    return tap(new Schedule(), function ($schedule) {
        $schedule->command('update:keep-status')->everyMinute();
    });
});
