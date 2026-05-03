<?php

use App\Models\Blog;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\Warranty;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


// Schedule::call(function () {
//     Warranty::autoExpire();
// })->daily();

Schedule::call(function () {
    Blog::autoPublish();
})->hourly();

Schedule::call(function () {
    Warranty::autoExpire();
})->dailyAt('00:00');