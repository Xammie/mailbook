<?php

use Xammie\Mailbook\MailableResolver;
use Xammie\Mailbook\Tests\Mails\TestMail;

it('can get class from class', function () {
    $resolver = new MailableResolver(TestMail::class);

    expect($resolver->class())->toEqual(TestMail::class);
});

it('can get class from closure', function () {
    $resolver = new MailableResolver(fn () => new TestMail());

    expect($resolver->class())->toEqual(TestMail::class);
});

it('cannot get class from closure with invalid type', function () {
    $resolver = new MailableResolver(fn () => 'invalid');

    $resolver->class();
})
    ->throws(UnexpectedValueException::class, 'Unexpected value returned from mailbook closure expected instance of Illuminate\Contracts\Mail\Mailable but got string');

it('can get class from closure with return type', function () {
    $resolver = new MailableResolver(function (): TestMail {
        throw new Exception('this will not be executed');
    });

    expect($resolver->class())->toEqual(TestMail::class);
});

it('can get class from mailable', function () {
    $resolver = new MailableResolver(new TestMail());

    expect($resolver->class())->toEqual(TestMail::class);
});

it('can get instance from class', function () {
    $resolver = new MailableResolver(TestMail::class);

    expect($resolver->instance())->toEqual(new TestMail());
});

it('can get instance from closure', function () {
    $resolver = new MailableResolver(fn () => new TestMail());

    expect($resolver->instance())->toEqual(new TestMail());
});

it('cannot get instance from closure with invalid type', function () {
    $resolver = new MailableResolver(fn () => 'invalid');

    $resolver->instance();
})
    ->throws(UnexpectedValueException::class, 'Unexpected value returned from mailbook closure expected instance of Illuminate\Contracts\Mail\Mailable but got string');

it('can get instance from closure with return type', function () {
    $resolver = new MailableResolver(fn (): TestMail => new TestMail());

    expect($resolver->instance())->toEqual(new TestMail());
});

it('can get instance from mailable', function () {
    $resolver = new MailableResolver(new TestMail());

    expect($resolver->instance())->toEqual(new TestMail());
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
