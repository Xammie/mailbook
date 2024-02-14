<?php

declare(strict_types=1);

use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Xammie\Mailbook\Data\ResolvedMail;
use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\MailableSender;
use Xammie\Mailbook\Tests\Fixtures\Mails\TestMail;
use Xammie\Mailbook\Tests\Fixtures\Mails\TestNotification;
use Xammie\Mailbook\Tests\Fixtures\User;

it('can collect mail', function (): void {
    Event::fake();

    $mailableSender = new MailableSender(new TestMail());
    $mail = $mailableSender->collect();

    expect($mail)->toBeInstanceOf(ResolvedMail::class);

    Event::assertDispatched(MessageSending::class);
    Event::assertDispatched(MessageSent::class);
});

it('will add new mailer', function (): void {
    $mailableSender = new MailableSender(new TestMail());
    invade($mailableSender)->inject();

    expect(config('mail.mailers.mailbook'))->toBe([
        'transport' => 'mailbook',
    ]);
});

it('will cleanup driver', function (): void {
    $mailableSender = new MailableSender(new TestMail());
    $mailableSender->collect();

    expect(config('mail.default'))->not()->toBe('mailbook')
        ->and(config('mail.driver'))->not()->toBe('mailbook');
});

it('will cleanup message', function (): void {
    $mailableSender = new MailableSender(new TestMail());
    $mailableSender->collect();

    expect(Mailbook::getMessage())->toBeNull();
});

it('will inject driver config', function (): void {
    $mailableSender = new MailableSender(new TestMail());
    invade($mailableSender)->inject();

    expect(config('mail.default'))->toBe('mailbook');
});

it('will inject old driver config', function (): void {
    $mailableSender = new MailableSender(new TestMail());
    invade($mailableSender)->inject();

    expect(config('mail.driver'))->toBe('mailbook');
});

it('can send mailable with email', function (): void {
    $mailableSender = new MailableSender(new TestMail(), 'test@mailbook.dev');
    $mail = $mailableSender->collect();

    expect($mail->to())->toBe(['test@mailbook.dev']);
});

it('can send mailable with notifiable', function (): void {
    $mailableSender = new MailableSender(new TestNotification(), new User(['email' => 'test@mailbook.dev']));
    $mail = $mailableSender->collect();

    expect($mail->to())->toBe(['test@mailbook.dev']);
});

it('can send notification with email', function (): void {
    $mailableSender = new MailableSender(new TestNotification(), 'test@mailbook.dev');
    $mail = $mailableSender->collect();

    expect($mail->to())->toBe(['test@mailbook.dev']);
});

it('can send notification with notifiable', function (): void {
    $mailableSender = new MailableSender(new TestNotification(), new User(['email' => 'test@mailbook.dev']));
    $mail = $mailableSender->collect();

    expect($mail->to())->toBe(['test@mailbook.dev']);
});
