<?php

use Illuminate\Support\Facades\Route;
use Xammie\Mailbook\Http\Controllers\DashboardController;
use Xammie\Mailbook\Http\Controllers\MailContentController;

Route::prefix('/mailbook')->group(function () {
    Route::get('/', DashboardController::class)->name('mailbook.dashboard');
    Route::get('/{class}', MailContentController::class)->name('mailbook.content');
});
