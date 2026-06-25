<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Requires `* * * * * php artisan schedule:run` in the server crontab to actually fire.
Schedule::command('articles:fetch')->everyTwoHours()->withoutOverlapping();
