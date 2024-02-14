<?php

declare(strict_types=1);

use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Tests\Fixtures\Mails\ClassicMail;
use Xammie\Mailbook\Tests\Fixtures\Mails\OtherMail;
use Xammie\Mailbook\Tests\Fixtures\Mails\TestMail;
use Xammie\Mailbook\Tests\Fixtures\Mails\TestNotification;

it('can register mailable with label as first method', function (): void {
    $item = Mailbook::label('Test label')->add(TestMail::class);

    expect($item->getLabel())->toBe('Test label');
});

it('will clear label in next call', function (): void {
    Mailbook::label('Test label');
    $item = Mailbook::add(TestMail::class);

    expect($item->getLabel())->toBe('Test Mail');
});

it('can group mails', function (): void {
    Mailbook::to('test@mailbook.dev')
        ->group(function (): void {
            Mailbook::add(TestMail::class);
            Mailbook::add(TestNotification::class);
        });

    $first = Mailbook::mailables()->first();
    $last = Mailbook::mailables()->last();

    expect($first->to())->toBe(['test@mailbook.dev']);
    expect($last->to())->toBe(['test@mailbook.dev']);
});

it('will clear registrar after group call', function (): void {
    Mailbook::to('test@mailbook.dev')
        ->group(function (): void {
            Mailbook::add(TestMail::class);
            Mailbook::add(TestNotification::class);
        });

    $item = Mailbook::add(ClassicMail::class);

    expect($item->to())->toBe([]);
});

it('can pass notifiable', function (): void {
    $item = Mailbook::to('test@mailbook.dev')->add(TestMail::class);

    expect($item->to())->toBe(['test@mailbook.dev']);
});

it('will reset notifiable', function (): void {
    Mailbook::to('test@mailbook.dev')->add(OtherMail::class);
    $item = Mailbook::add(TestMail::class);

    expect($item->to())->toBe([]);
});

it('can pass notifiable as closure', function (): void {
    $item = Mailbook::to(fn () => 'test@mailbook.dev')->add(TestMail::class);

    expect($item->to())->toBe(['test@mailbook.dev']);
});

it('can pass notifiable as closure to group', function (): void {
    Mailbook::to(fn () => 'test@mailbook.dev')
        ->group(function (): void {
            Mailbook::add(TestMail::class);
            Mailbook::add(TestNotification::class);
        });

    $first = Mailbook::mailables()->first();

    expect($first->to())->toBe(['test@mailbook.dev']);
});
