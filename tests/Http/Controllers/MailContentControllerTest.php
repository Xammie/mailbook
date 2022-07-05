<?php

use function Pest\Laravel\get;
use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Tests\Mails\TestMail;

it('can render content', function () {
    Mailbook::register(TestMail::class, function () {
        return new TestMail();
    });

    get(route('mailbook.content', TestMail::class))
        ->assertSuccessful()
        ->assertSeeText('Test mail');
});

it('cannot render without any registered mailables', function () {
    get(route('mailbook.content', TestMail::class))
        ->assertStatus(500);
});

it('cannot render with unknown mailable', function () {
    Mailbook::register(TestMail::class, function () {
        return new TestMail();
    });

    get(route('mailbook.content', 'test-mail'))
        ->assertStatus(400);
});


it('can render without mailables', function () {
    get(route('mailbook.dashboard'))
        ->assertSuccessful()
        ->assertSeeText('You have not registered any mailables.');
});
