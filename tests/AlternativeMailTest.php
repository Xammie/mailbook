<?php

use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Tests\Mails\AlternativeMail;
use Xammie\Mailbook\Tests\Mails\ClassicMail;

it('can get mail information', function ($mailable) {
    $item = Mailbook::add($mailable);

    expect($item->subject())->toBe('Invoice Paid')
        ->and($item->content())->toBe('Test mail content');
})
    ->with([
        new ClassicMail(),
        new AlternativeMail(),
    ])
    ->skip(! class_exists('Illuminate\Mail\Mailables\Envelope'), 'Alternative mailables are not available in this version of Laravel');
