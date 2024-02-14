<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Xammie\Mailbook\Http\Controllers\DashboardController;
use Xammie\Mailbook\Http\Controllers\MailContentController;
use Xammie\Mailbook\Http\Controllers\MailSendController;

if (config('mailbook.enabled')) {
    Route::prefix(config('mailbook.route_prefix'))
        ->middleware(config('mailbook.middlewares'))
        ->group(function (): void {
            Route::get('/', DashboardController::class)->name('mailbook.dashboard');
            Route::get('/content', MailContentController::class)->name('mailbook.content');
            Route::get('/send', MailSendController::class)->name('mailbook.send');
        });
}
