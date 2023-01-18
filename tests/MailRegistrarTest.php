<?php

use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Tests\Mails\ClassicMail;
use Xammie\Mailbook\Tests\Mails\TestMail;
use Xammie\Mailbook\Tests\Mails\TestNotification;

it('can register mailable with label as first method', function () {
    $item = Mailbook::label('Test label')->add(TestMail::class);

    expect($item->getLabel())->toBe('Test label');
});

it('will clear label in next call', function () {
    Mailbook::label('Test label');
    $item = Mailbook::add(TestMail::class);

    expect($item->getLabel())->toBe('Test Mail');
});

it('can group mails', function () {
    Mailbook::to('test@mailbook.dev')
        ->group(function () {
            Mailbook::add(TestMail::class);
            Mailbook::add(TestNotification::class);
        });

    $first = Mailbook::mailables()->first();
    $last = Mailbook::mailables()->last();

    expect($first->to())->toBe(['test@mailbook.dev']);
    expect($last->to())->toBe(['test@mailbook.dev']);
});

it('will clear registrar after group call', function () {
    Mailbook::to('test@mailbook.dev')
        ->group(function () {
            Mailbook::add(TestMail::class);
            Mailbook::add(TestNotification::class);
        });

    $item = Mailbook::add(ClassicMail::class);

    expect($item->to())->toBe([]);
});
