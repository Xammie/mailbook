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

it('can get class from closure with return type', function () {
    $resolver = new MailableResolver(fn (): TestMail => new TestMail());

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
