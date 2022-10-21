<?php

use Xammie\Mailbook\Facades\Mailbook;

it('can set locale', function () {
    expect(Mailbook::getLocale())->toBeNull();

    Mailbook::setLocale('nl');

    expect(Mailbook::getLocale())->toBe('nl');
});

it('cannot set invalid locale', function () {
    Mailbook::setLocale(123);

    expect(Mailbook::getLocale())->toBeNull();
});

it('cannot set unknown locale', function () {
    Mailbook::setLocale('be');

    expect(Mailbook::getLocale())->toBeNull();
});
