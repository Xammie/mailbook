<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Tests\Fixtures\Mails\OtherMail;
use Xammie\Mailbook\Tests\Fixtures\Mails\TestMail;
use Xammie\Mailbook\Tests\Fixtures\Mails\TestNotification;
use Xammie\Mailbook\Tests\Fixtures\Mails\TranslatedMail;
use Xammie\Mailbook\Tests\Fixtures\Mails\TranslatedNotification;
use Xammie\Mailbook\Tests\Fixtures\User;

use function Pest\Laravel\get;

beforeEach(function (): void {
    Mail::fake();
    Notification::fake();
    config()->set('mailbook.send', true);
});

it('cannot send mails when disabled', function (): void {
    config()->set('mailbook.send', false);

    Mailbook::add(TestMail::class);

    get(route('mailbook.send', ['class' => TestMail::class]))
        ->assertStatus(404);

    Mail::assertNothingSent();
    Notification::assertNothingSent();
});

it('cannot send mails when sent_to is invalid', function (): void {
    config()->set('mailbook.send_to', null);

    Mailbook::add(TestMail::class);

    get(route('mailbook.send', ['class' => TestMail::class]))
        ->assertStatus(500);

    Mail::assertNothingSent();
    Notification::assertNothingSent();
});

it('can send mailable', function (): void {
    Mailbook::add(TestMail::class);

    get(route('mailbook.send', ['class' => TestMail::class]))
        ->assertRedirect();

    Mail::assertSent(TestMail::class);
    Notification::assertNothingSent();
});

it('can send notification', function (): void {
    Mailbook::add(TestNotification::class);

    get(route('mailbook.send', ['class' => TestNotification::class]))
        ->assertRedirect();

    Notification::assertSentOnDemand(TestNotification::class);
    Mail::assertNothingSent();
});

it('can send notification with notifiable', function (): void {
    Mailbook::to(new User(['email' => 'notifiable@mailbook.dev']))->add(TestNotification::class);

    get(route('mailbook.send', ['class' => TestNotification::class]))
        ->assertRedirect();

    Notification::assertSentOnDemand(TestNotification::class);
    Mail::assertNothingSent();
});

it('cannot send with invalid class', function (): void {
    Mailbook::add(TestMail::class);

    get(route('mailbook.send', ['class' => '::invalid-mailable-item::']))
        ->assertStatus(404);
});

it('cannot send without class', function (): void {
    Mailbook::add(TestMail::class);

    get(route('mailbook.send'))
        ->assertStatus(404);
});

it('can send different locale mailable', function (): void {
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

it('can send different locale notification', function (): void {
    config()->set('mailbook.locales', [
        'en' => 'English',
        'nl' => 'Dutch',
        'de' => 'German',
    ]);

    app('translator')->addJsonPath(__DIR__.'/../../lang');

    Mailbook::add(TranslatedNotification::class);

    get(route('mailbook.send', ['class' => TranslatedNotification::class, 'locale' => 'nl']))
        ->assertRedirect();

    Notification::assertSentOnDemand(TranslatedNotification::class, function (TranslatedNotification $notification): bool {
        $this->assertSame('nl', $notification->locale);

        return true;
    });
    Mail::assertNothingSent();
});

it('can send to one', function (): void {
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

it('cannot send to multiple', function (): void {
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
        ], $mail->to);

        return true;
    });
});

it('can send variant', function (): void {
    Mailbook::add(TestMail::class)
        ->variant('wrong variant', fn () => new OtherMail())
        ->variant('Test variant', fn () => new TestMail());

    get(route('mailbook.send', ['class' => TestMail::class, 'variant' => 'test-variant']))
        ->assertRedirect();

    Mail::assertSent(TestMail::class);
});
