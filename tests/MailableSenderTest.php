<?php

use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\MailableSender;
use Xammie\Mailbook\ResolvedMail;
use Xammie\Mailbook\Tests\Mails\TestMail;

it('can collect mail', function () {
    Event::fake();

    $mailableSender = new MailableSender(new TestMail());
    $mail = $mailableSender->collect();

    expect($mail)->toBeInstanceOf(ResolvedMail::class);

    Event::assertDispatched(MessageSending::class);
    Event::assertDispatched(MessageSent::class);
});

it('will add new mailer', function () {
    $mailableSender = new MailableSender(new TestMail());
    invade($mailableSender)->inject();

    expect(config('mail.mailers.mailbook'))->toBe([
        'transport' => 'mailbook',
    ]);
});

it('will cleanup driver', function () {
    $mailableSender = new MailableSender(new TestMail());
    $mailableSender->collect();

    expect(config('mail.default'))->not()->toBe('mailbook')
        ->and(config('mail.driver'))->not()->toBe('mailbook');
});

it('will cleanup message', function () {
    $mailableSender = new MailableSender(new TestMail());
    $mailableSender->collect();

    expect(Mailbook::getMessage())->toBeNull();
});

it('will inject driver config', function () {
    $mailableSender = new MailableSender(new TestMail());
    invade($mailableSender)->inject();

    expect(config('mail.default'))->toBe('mailbook');
});

it('will inject old driver config', function () {
    $mailableSender = new MailableSender(new TestMail());
    invade($mailableSender)->inject();

    expect(config('mail.driver'))->toBe('mailbook');
});
