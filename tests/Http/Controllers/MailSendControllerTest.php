<?php

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use function Pest\Laravel\get;
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

    get(route('mailbook.send', ['class' => TestMail::class]))
        ->assertStatus(404);

    Mail::assertNothingSent();
    Notification::assertNothingSent();
});

it('can send mailable', function () {
    Mailbook::add(TestMail::class);

    get(route('mailbook.send', ['class' => TestMail::class]))
        ->assertRedirect();

    Mail::assertSent(TestMail::class);
    Notification::assertNothingSent();
});

it('can send notification', function () {
    Mailbook::add(TestNotification::class);

    get(route('mailbook.send', ['class' => TestNotification::class]))
        ->assertRedirect();

    Notification::assertSentTimes(TestNotification::class, 1);
    Mail::assertNothingSent();
});

it('cannot send with invalid class', function () {
    Mailbook::add(TestMail::class);

    get(route('mailbook.send', ['class' => '::invalid-mailable-item::']))
        ->assertStatus(404);
});

it('cannot send without class', function () {
    Mailbook::add(TestMail::class);

    get(route('mailbook.send'))
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

    get(route('mailbook.send', ['class' => TranslatedMail::class, 'locale' => 'nl']))
        ->assertRedirect();

    Mail::assertSent(TranslatedMail::class, function (TranslatedMail $mail): bool {
        $this->assertEquals('nl', $mail->locale);

        return true;
    });
});

it('can send to one', function () {
    config()->set('mailbook.send_to', 'test@mailbook.dev');

    Mailbook::add(TestMail::class);

    get(route('mailbook.send', ['class' => TestMail::class]))
        ->assertRedirect();

    Mail::assertSent(TestMail::class, function (TestMail $mail): bool {
        $this->assertEquals([
            [
                'name' => null,
                'address' => 'test@mailbook.dev',
            ],
        ], $mail->to);

        return true;
    });
});

it('can send to multiple', function () {
    config()->set('mailbook.send_to', [
        'test@mailbook.dev',
        'example@mailbook.dev',
    ]);

    Mailbook::add(TestMail::class);

    get(route('mailbook.send', ['class' => TestMail::class]))
        ->assertRedirect();

    Mail::assertSent(TestMail::class, function (TestMail $mail): bool {
        $this->assertEquals([
            [
                'name' => null,
                'address' => 'test@mailbook.dev',
            ],
            [
                'name' => null,
                'address' => 'example@mailbook.dev',
            ],
        ], $mail->to);

        return true;
    });
});

it('can send variant', function () {
    Mailbook::add(TestMail::class)
        ->variant('wrong variant', fn () => new OtherMail())
        ->variant('Test variant', fn () => new TestMail());

    get(route('mailbook.send', ['class' => TestMail::class, 'variant' => 'test-variant']))
        ->assertRedirect();

    Mail::assertSent(TestMail::class);
});
