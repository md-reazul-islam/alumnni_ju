<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('library:send-overdue-notices')->daily();
Schedule::command('carpool:expire-bookings')->everyFiveMinutes();
Schedule::command('carpool:reconcile-earnings')->daily();
Schedule::command('matrimony:expire-interests')->daily();
