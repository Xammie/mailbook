<?php

declare(strict_types=1);

use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Event;
use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\MailableResolver;
use Xammie\Mailbook\Tests\Fixtures\Mails\NotificationMail;
use Xammie\Mailbook\Tests\Fixtures\Mails\OtherMail;
use Xammie\Mailbook\Tests\Fixtures\Mails\TestMail;
use Xammie\Mailbook\Tests\Fixtures\Mails\TestNotification;

it('can get class from mailable', function ($mailable): void {
    $resolver = new MailableResolver($mailable);

    expect($resolver->className())->toEqual(TestMail::class);
})
    ->with('mailables');

it('can get class from notification', function ($mailable): void {
    $resolver = new MailableResolver($mailable);

    expect($resolver->className())->toEqual(TestNotification::class);
})
    ->with('notifications');

it('can get class from class', function (): void {
    $resolver = new MailableResolver(TestMail::class);

    expect($resolver->className())->toEqual(TestMail::class);
});

it('can get class from closure', function (): void {
    $resolver = new MailableResolver(fn () => new TestMail());

    expect($resolver->className())->toEqual(TestMail::class);
});

it('cannot get class from closure with invalid type', function (): void {
    $resolver = new MailableResolver(fn () => 'invalid');

    $resolver->className();
})
    ->throws(UnexpectedValueException::class, 'Unexpected value returned from mailbook closure expected instance of Illuminate\Contracts\Mail\Mailable but got string');

it('can get class from closure with return type', function (): void {
    $resolver = new MailableResolver(function (): TestMail {
        throw new Exception('this will not be executed');
    });

    expect($resolver->className())->toEqual(TestMail::class);
});

it('can get instance from mailables', function ($mailable): void {
    $resolver = new MailableResolver($mailable);

    expect($resolver->instance())->toBeInstanceOf(TestMail::class);
})
    ->with('mailables');

it('can get instance from notifications', function ($mailable): void {
    $resolver = new MailableResolver($mailable);

    expect($resolver->instance())->toBeInstanceOf(TestNotification::class);
})
    ->with('notifications');

it('can get instance from class', function (): void {
    $resolver = new MailableResolver(TestMail::class);

    expect($resolver->instance())->toBeInstanceOf(TestMail::class);
});

it('can get instance from closure', function (): void {
    $resolver = new MailableResolver(fn () => new TestMail());

    expect($resolver->instance())->toBeInstanceOf(TestMail::class);
});

it('cannot get instance from closure with invalid type', function (): void {
    $resolver = new MailableResolver(fn () => 'invalid');

    $resolver->instance();
})
    ->throws(UnexpectedValueException::class, 'Unexpected value returned from mailbook closure expected instance of Illuminate\Contracts\Mail\Mailable but got string');

it('can get instance from closure with return type', function (): void {
    $resolver = new MailableResolver(fn (): TestMail => new TestMail());

    expect($resolver->instance())->toBeInstanceOf(TestMail::class);
});

it('can get instance from mailable', function (): void {
    $resolver = new MailableResolver(new TestMail());

    expect($resolver->instance())->toBeInstanceOf(TestMail::class);
});

it('can resolve dependencies from closure', function (): void {
    (new MailableResolver(function (TestMail $testMail) {
        expect($testMail)->toBeInstanceOf(TestMail::class);

        return $testMail;
    }))
        ->instance();
});

it('will resolve instance once', function (): void {
    $resolver = new MailableResolver(TestMail::class);

    expect($resolver->instance())->toBe($resolver->instance());
});

it('will execute closure once when resolving class from closure', function (): void {
    $executed = 0;

    $resolver = new MailableResolver(function () use (&$executed) {
        $executed++;

        return new TestMail();
    });

    $resolver->className();
    $resolver->className();
    $resolver->className();

    expect($executed)->toEqual(1);
});

it('resolved class instance can be built', function (): void {
    $resolver = new MailableResolver(fn () => new TestMail());

    expect($resolver->resolve()->subject())->toBe('Test email subject');
});

it('can resolve className from notification class', function (): void {
    $resolver = new MailableResolver(NotificationMail::class);

    expect($resolver->className())->toBe(NotificationMail::class);
});

it('can resolve className from notification closure', function (): void {
    $resolver = new MailableResolver(fn () => new NotificationMail());

    expect($resolver->className())->toBe(NotificationMail::class);
});

it('can resolve mail from notification', function (): void {
    Event::fake();

    $resolver = new MailableResolver(NotificationMail::class);

    expect($resolver->resolve()->content())->toContain(
        'The introduction to the notification.',
        'Thank you for using our application!',
    );

    Event::assertDispatched(MessageSending::class);

    expect(Mailbook::getMessage())->toBeNull();
});

it('can resolve mail once', function (): void {
    Event::fake();

    $resolver = new MailableResolver(NotificationMail::class);

    $resolver->resolve()->content();
    $resolver->resolve()->content();

    Event::assertDispatchedTimes(MessageSending::class);
});

it('can resolve mail to', function (): void {
    $resolver = new MailableResolver(OtherMail::class);

    expect($resolver->resolve()->to())->toEqual(['"Mailbook" <example@mailbook.dev>']);
});
