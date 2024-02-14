<?php

declare(strict_types=1);

use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Tests\Fixtures\Mails\AlternativeMail;
use Xammie\Mailbook\Tests\Fixtures\Mails\ClassicMail;

it('can get mail subject and content', function ($mailable): void {
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
    ->skip(! class_exists(\Illuminate\Mail\Mailables\Envelope::class), 'Alternative mailables are not available in this version of Laravel');
