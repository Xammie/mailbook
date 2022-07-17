<?php

use Illuminate\Support\Facades\Route;
use Xammie\Mailbook\Http\Controllers\DashboardController;
use Xammie\Mailbook\Http\Controllers\MailContentController;
use Xammie\Mailbook\Http\Middlewares\RollbackDatabase;

Route::prefix('/mailbook')
    ->middleware(RollbackDatabase::class)
    ->group(function () {
        Route::get('/', DashboardController::class)->name('mailbook.dashboard');
        Route::get('/{class}/{variant?}', MailContentController::class)->name('mailbook.content');
    });
