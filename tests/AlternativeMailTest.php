<?php

use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Tests\Mails\AlternativeMail;
use Xammie\Mailbook\Tests\Mails\ClassicMail;

it('can get mail subject and content', function ($mailable) {
    $item = Mailbook::add($mailable);

    expect($item->subject())->toBe('Invoice Paid')
        ->and($item->content())->toBe('Test mail content')
        ->and($item->from())->toBe(['"Example" <hello@example.com>'])
        ->and($item->replyTo())->toBe([])
        ->and($item->to())->toBe([])
        ->and($item->cc())->toBe(['"Example Name" <foo@example.com>'])
        ->and($item->bcc())->toBe([]);
})
    ->with([
        new ClassicMail(),
        new AlternativeMail(),
    ])
    ->skip(! class_exists('Illuminate\Mail\Mailables\Envelope'), 'Alternative mailables are not available in this version of Laravel');
