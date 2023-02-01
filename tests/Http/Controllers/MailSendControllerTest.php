<?php

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use function Pest\Laravel\post;
use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Tests\Mails\OtherMail;
use Xammie\Mailbook\Tests\Mails\TestMail;
use Xammie\Mailbook\Tests\Mails\TestNotification;
use Xammie\Mailbook\Tests\Mails\TranslatedMail;

beforeEach(function () {
    Mail::fake();
    Notification::fake();
    config()->set('mailbook.send', true);
});

it('cannot send mails when disabled', function () {
    config()->set('mailbook.send', false);

    Mailbook::add(TestMail::class);

    post(route('mailbook.send', ['class' => TestMail::class, 'email' => 'test@example.com']))
        ->assertStatus(404);

    Mail::assertNothingSent();
    Notification::assertNothingSent();
});

it('can send mailable', function () {
    Mailbook::add(TestMail::class);

    post(route('mailbook.send', ['email' => 'test@example.com', 'class' => TestMail::class]))
        ->assertStatus(200)
        ->assertSessionHas('success');

    Mail::assertSent(TestMail::class);
    Notification::assertNothingSent();
});

it('can send notification', function () {
    Mailbook::add(TestNotification::class);

    post(route('mailbook.send', ['email' => 'test@example.com', 'class' => TestNotification::class]))
        ->assertStatus(200)
        ->assertSessionHas('success');

    Notification::assertSentTimes(TestNotification::class, 1);
    Mail::assertNothingSent();
});

it('cannot send with invalid email', function () {
    Mailbook::add(TestMail::class);
    $mailable = Mailbook::mailables()->first()->class();

    post(route('mailbook.send', ['email' => 'example.com', 'class' => $mailable]))
        ->assertStatus(400);
});

it('cannot send without email', function () {
    Mailbook::add(TestMail::class);

    post(route('mailbook.send', ['class' => TestMail::class]))
        ->assertStatus(404);
});

it('cannot send with invalid class', function () {
    Mailbook::add(TestMail::class);

    post(route('mailbook.send', ['email' => 'test@example.com', 'class' => '::invalid-mailable-item::']))
        ->assertStatus(404);
});

it('cannot send without class', function () {
    Mailbook::add(TestMail::class);

    post(route('mailbook.send', ['email' => 'test@example.com']))
        ->assertStatus(404);
});

it('can send different locale mailable', function () {
    config()->set('mailbook.locales', [
        'en' => 'English',
        'nl' => 'Dutch',
        'de' => 'German',
    ]);

    app('translator')->addJsonPath(__DIR__.'/../../lang');

    Mailbook::add(TranslatedMail::class);

    post(route('mailbook.send', ['email' => 'test@example.com', 'class' => TranslatedMail::class, 'locale' => 'nl']))
        ->assertSuccessful();

    Mail::assertSent(TranslatedMail::class, function (TranslatedMail $mail): bool {
        $this->assertEquals('nl', $mail->locale);

        return true;
    });
});

it('can send variant', function () {
    Mailbook::add(TestMail::class)
        ->variant('wrong variant', fn () => new OtherMail())
        ->variant('Test variant', fn () => new TestMail());

    post(route('mailbook.send', ['email' => 'test@example.com', 'class' => TestMail::class, 'variant' => 'test-variant']))
        ->assertSuccessful();

    Mail::assertSent(TestMail::class);
});
