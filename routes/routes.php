<?php

use Illuminate\Support\Facades\Route;
use Xammie\Mailbook\Http\Controllers\MailbookController;
use Xammie\Mailbook\Http\Controllers\RenderMailableController;

Route::get('/mailbook', MailbookController::class)->name('mailbook.dashboard');
Route::get('/mailbook/{class}', RenderMailableController::class)->name('mailbook.mailable');
