<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule daily todo generation to run every day at midnight
Schedule::command('todos:generate-daily')->daily();

// Schedule cleanup of old todos to run weekly
Schedule::command('todos:cleanup-old --days=7')->weekly();
