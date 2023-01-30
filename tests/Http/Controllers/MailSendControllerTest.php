<?php

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use function Pest\Laravel\post;
use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Tests\Mails\TestMail;
use Xammie\Mailbook\Tests\Mails\TestNotification;

beforeEach(function () {
    Mail::fake();
    Notification::fake();
    config()->set('mailbook.send', true);
});

it('cannot send mails when disabled', function () {
    config()->set('mailbook.send', false);

    Mailbook::add(TestMail::class);

    post(route('mailbook.send', ['class' => TestMail::class]))
        ->assertStatus(404);

    Mail::assertNothingSent();
    Notification::assertNothingSent();
});

it('can send mailable', function () {
    Mailbook::add(TestMail::class);

    post(route('mailbook.send', ['email' => 'test@mail.com', 'class' => TestMail::class]))
        ->assertStatus(200)
        ->assertSessionHas('success');

    Mail::assertSent(TestMail::class);
    Notification::assertNothingSent();
});

it('can send notification', function () {
    Mailbook::add(TestNotification::class);

    post(route('mailbook.send', ['email' => 'test@mail.com', 'class' => TestNotification::class]))
        ->assertStatus(200)
        ->assertSessionHas('success');

    Notification::assertSentTimes(TestNotification::class, 1);
    Mail::assertNothingSent();
});

it('cannot send with invalid email', function () {
    Mailbook::add(TestMail::class);
    $mailable = Mailbook::mailables()->first()->class();
    $email = '::invalid-email::';

    post(route('mailbook.send', ['email' => $email, 'class' => $mailable]))
        ->assertStatus(400);
});

it('cannot send without email', function () {
    Mailbook::add(TestMail::class);

    post(route('mailbook.send', ['class' => TestMail::class]))
        ->assertStatus(404);
});

it('cannot send with invalid class', function () {
    Mailbook::add(TestMail::class);

    post(route('mailbook.send', ['email' => 'example@mail.com', 'class' => '::invalid-mailable-item::']))
        ->assertStatus(400);
});

it('cannot send without class', function () {
    Mailbook::add(TestMail::class);

    post(route('mailbook.send', ['email' => 'example@mail.com']))
        ->assertStatus(404);
});
