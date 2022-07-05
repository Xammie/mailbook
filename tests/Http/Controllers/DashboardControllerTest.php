<?php

use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Tests\Mails\TestMail;
use function Pest\Laravel\get;

it('can render', function () {
    Mailbook::register(TestMail::class, function () {
        return new TestMail();
    });

    get(route('mailbook.dashboard'))
        ->assertSuccessful()
        ->assertSeeText('Mailbook')
        ->assertSeeText('Test email subject');
});

it('can render selected', function () {
    Mailbook::register(TestMail::class, function () {
        return new TestMail();
    });
    Mailbook::register(TestMail::class, function () {
        return new TestMail();
    });

    get(route('mailbook.dashboard', ['selected' => TestMail::class]))
        ->assertSuccessful()
        ->assertSeeText('Mailbook')
        ->assertSeeText('Test email subject');
});


it('can render without mailables', function () {
    get(route('mailbook.dashboard'))
        ->assertSuccessful()
        ->assertSeeText('You have not registered any mailables.');
});
