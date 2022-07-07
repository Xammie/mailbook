<?php

use function Pest\Laravel\get;
use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Tests\Mails\TestMail;

it('can render', function () {
    Mailbook::add(TestMail::class);

    get(route('mailbook.content', TestMail::class))
        ->assertSuccessful()
        ->assertSeeText('Test mail');
});

it('can render closure', function () {
    Mailbook::add(fn () => new TestMail());

    get(route('mailbook.content', TestMail::class))
        ->assertSuccessful()
        ->assertSeeText('Test mail');
});

it('cannot render without mailables', function () {
    get(route('mailbook.content', TestMail::class))
        ->assertStatus(500);
});

it('cannot render with unknown mailable', function () {
    Mailbook::add(TestMail::class, fn () => new TestMail());

    get(route('mailbook.content', 'test-mail'))
        ->assertStatus(400);
});
