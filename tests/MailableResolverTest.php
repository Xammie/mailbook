<?php

use Xammie\Mailbook\MailableResolver;
use Xammie\Mailbook\Tests\Mails\NotificationMail;
use Xammie\Mailbook\Tests\Mails\TestMail;

it('can get class', function ($mailable) {
    $resolver = new MailableResolver($mailable);

    expect($resolver->className())->toEqual(TestMail::class);
})
    ->with('mailables');

it('can get class from class', function () {
    $resolver = new MailableResolver(TestMail::class);

    expect($resolver->className())->toEqual(TestMail::class);
});

it('can get class from closure', function () {
    $resolver = new MailableResolver(fn () => new TestMail());

    expect($resolver->className())->toEqual(TestMail::class);
});

it('cannot get class from closure with invalid type', function () {
    $resolver = new MailableResolver(fn () => 'invalid');

    $resolver->className();
})
    ->throws(UnexpectedValueException::class, 'Unexpected value returned from mailbook closure expected instance of Illuminate\Contracts\Mail\Mailable but got string');

it('can get class from closure with return type', function () {
    $resolver = new MailableResolver(function (): TestMail {
        throw new Exception('this will not be executed');
    });

    expect($resolver->className())->toEqual(TestMail::class);
});

it('can get class from mailable', function () {
    $resolver = new MailableResolver(new TestMail());

    expect($resolver->className())->toEqual(TestMail::class);
});

it('can get instance', function ($mailable) {
    $resolver = new MailableResolver($mailable);

    expect($resolver->instance())->toBeInstanceOf(TestMail::class);
})
    ->with('mailables');

it('can get instance from class', function () {
    $resolver = new MailableResolver(TestMail::class);

    expect($resolver->instance())->toBeInstanceOf(TestMail::class);
});

it('can get instance from closure', function () {
    $resolver = new MailableResolver(fn () => new TestMail());

    expect($resolver->instance())->toBeInstanceOf(TestMail::class);
});

it('cannot get instance from closure with invalid type', function () {
    $resolver = new MailableResolver(fn () => 'invalid');

    $resolver->instance();
})
    ->throws(UnexpectedValueException::class, 'Unexpected value returned from mailbook closure expected instance of Illuminate\Contracts\Mail\Mailable but got string');

it('can get instance from closure with return type', function () {
    $resolver = new MailableResolver(fn (): TestMail => new TestMail());

    expect($resolver->instance())->toBeInstanceOf(TestMail::class);
});

it('can get instance from mailable', function () {
    $resolver = new MailableResolver(new TestMail());

    expect($resolver->instance())->toBeInstanceOf(TestMail::class);
});

it('can resolve dependencies from closure', function () {
    (new MailableResolver(function (TestMail $testMail) {
        expect($testMail)->toBeInstanceOf(TestMail::class);

        return $testMail;
    }))
        ->instance();
});

it('will resolve instance once', function () {
    $resolver = new MailableResolver(TestMail::class);

    expect($resolver->instance())->toBe($resolver->instance());
});

it('will execute closure once when resolving class from closure', function () {
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

it('resolved class instance can be built', function () {
    $resolver = new MailableResolver(function () {
        return new TestMail();
    });

    expect($resolver->resolve()->subject())->toBe('Test email subject');
});

it('can resolve mail from notification', function () {
    $resolver = new MailableResolver(NotificationMail::class);

    expect($resolver->resolve()->content())->toContain(
        'The introduction to the notification.',
        'Thank you for using our application!',
    );
});
