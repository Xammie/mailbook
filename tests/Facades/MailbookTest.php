<?php

declare(strict_types=1);

use Xammie\Mailbook\Facades\Mailbook;

it('can set locale', function ($locale): void {
    config()->set('mailbook.locales', [
        'en' => 'English',
        'nl' => 'Dutch',
        'de' => 'German',
    ]);

    expect(Mailbook::getLocale())->toBeNull();

    Mailbook::setLocale($locale);

    expect(Mailbook::getLocale())->toBe($locale);
})
    ->with(['en', 'nl', 'de']);

it('cannot set invalid locale', function (): void {
    config()->set('mailbook.locales', [
        'en' => 'English',
        'nl' => 'Dutch',
        'de' => 'German',
    ]);

    Mailbook::setLocale(123);

    expect(Mailbook::getLocale())->toBeNull();
});

it('cannot set unknown locale', function (): void {
    config()->set('mailbook.locales', [
        'en' => 'English',
        'nl' => 'Dutch',
        'de' => 'German',
    ]);

    Mailbook::setLocale('be');

    expect(Mailbook::getLocale())->toBeNull();
});

it('cannot set locales when none are set', function (): void {
    config()->set('mailbook.locales', null);

    Mailbook::setLocale('be');

    expect(Mailbook::getLocale())->toBeNull();
});
