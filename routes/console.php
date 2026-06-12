<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;
use App\Services\GajiService;

Schedule::call(function () {
    app(GajiService::class)->autoSaveUnclaimedSalaries();
})->weeklyOn(5, '23:59'); // Setiap hari Jumat pukul 23:59

