<?php

use Illuminate\Support\Facades\Route;
use Xammie\Mailbook\Http\Controllers\DashboardController;
use Xammie\Mailbook\Http\Controllers\MailContentController;

Route::prefix(config('mailbook.route_prefix'))
    ->middleware(config('mailbook.middlewares'))
    ->group(function () {
        Route::get('/', DashboardController::class)->name('mailbook.dashboard');
        Route::get('/content/{class}/{variant?}', MailContentController::class)->name('mailbook.content');
    });
