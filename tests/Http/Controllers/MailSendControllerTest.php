<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Tests\Fixtures\Mails\OtherMail;
use Xammie\Mailbook\Tests\Fixtures\Mails\TestMail;
use Xammie\Mailbook\Tests\Fixtures\Mails\TestNotification;
use Xammie\Mailbook\Tests\Fixtures\Mails\TranslatedMail;
use Xammie\Mailbook\Tests\Fixtures\Mails\TranslatedNotification;
use Xammie\Mailbook\Tests\Fixtures\User;
use Xammie\Mailbook\Tests\TestCase;

class MailSendControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        Notification::fake();
        config()->set('mailbook.send', true);
    }

    public function test_cannot_send_mails_when_disabled(): void
    {
        config()->set('mailbook.send', false);
        Mailbook::add(TestMail::class);
        $this->get(route('mailbook.send', ['class' => TestMail::class]))
            ->assertStatus(404);
        Mail::assertNothingSent();
        Notification::assertNothingSent();
    }

    public function test_cannot_send_mails_when_sent_to_is_invalid(): void
    {
        config()->set('mailbook.send_to', null);
        Mailbook::add(TestMail::class);
        $this->get(route('mailbook.send', ['class' => TestMail::class]))
            ->assertStatus(500);
        Mail::assertNothingSent();
        Notification::assertNothingSent();
    }

    public function test_can_send_mailable(): void
    {
        Mailbook::add(TestMail::class);
        $this->get(route('mailbook.send', ['class' => TestMail::class]))
            ->assertRedirect();
        Mail::assertSent(TestMail::class);
        Notification::assertNothingSent();
    }

    public function test_can_send_notification(): void
    {
        Mailbook::add(TestNotification::class);
        $this->get(route('mailbook.send', ['class' => TestNotification::class]))
            ->assertRedirect();
        Notification::assertSentOnDemand(TestNotification::class);
        Mail::assertNothingSent();
    }

    public function test_can_send_notification_with_notifiable(): void
    {
        Mailbook::to(new User(['email' => 'notifiable@mailbook.dev']))->add(TestNotification::class);
        $this->get(route('mailbook.send', ['class' => TestNotification::class]))
            ->assertRedirect();
        Notification::assertSentOnDemand(TestNotification::class);
        Mail::assertNothingSent();
    }

    public function test_cannot_send_with_invalid_class(): void
    {
        Mailbook::add(TestMail::class);
        $this->get(route('mailbook.send', ['class' => '::invalid-mailable-item::']))
            ->assertStatus(404);
    }

    public function test_cannot_send_without_class(): void
    {
        Mailbook::add(TestMail::class);
        $this->get(route('mailbook.send'))
            ->assertStatus(404);
    }

    public function test_can_send_different_locale_mailable(): void
    {
        config()->set('mailbook.locales', [
            'en' => 'English',
            'nl' => 'Dutch',
            'de' => 'German',
        ]);
        app('translator')->addJsonPath(__DIR__.'/../../../lang');
        Mailbook::add(TranslatedMail::class);
        $this->get(route('mailbook.send', ['class' => TranslatedMail::class, 'locale' => 'nl']))
            ->assertRedirect();
        Mail::assertSent(TranslatedMail::class, function (TranslatedMail $mail): bool {
            self::assertEquals('nl', $mail->locale);

            return true;
        });
    }

    public function test_can_send_different_locale_notification(): void
    {
        config()->set('mailbook.locales', [
            'en' => 'English',
            'nl' => 'Dutch',
            'de' => 'German',
        ]);
        app('translator')->addJsonPath(__DIR__.'/../../../lang');
        Mailbook::add(TranslatedNotification::class);
        $this->get(route('mailbook.send', ['class' => TranslatedNotification::class, 'locale' => 'nl']))
            ->assertRedirect();
        Notification::assertSentOnDemand(TranslatedNotification::class, function (TranslatedNotification $notification): bool {
            self::assertSame('nl', $notification->locale);

            return true;
        });
        Mail::assertNothingSent();
    }

    public function test_can_send_to_one(): void
    {
        config()->set('mailbook.send_to', 'test@mailbook.dev');
        Mailbook::add(TestMail::class);
        $this->get(route('mailbook.send', ['class' => TestMail::class]))
            ->assertRedirect();
        Mail::assertSent(TestMail::class, function (TestMail $mail): bool {
            self::assertEquals([
                [
                    'name' => null,
                    'address' => 'test@mailbook.dev',
                ],
            ], $mail->to);

            return true;
        });
    }

    public function test_cannot_send_to_multiple(): void
    {
        config()->set('mailbook.send_to', [
            'test@mailbook.dev',
            'example@mailbook.dev',
        ]);
        Mailbook::add(TestMail::class);
        $this->get(route('mailbook.send', ['class' => TestMail::class]))
            ->assertRedirect();
        Mail::assertSent(TestMail::class, function (TestMail $mail): bool {
            self::assertEquals([
                [
                    'name' => null,
                    'address' => 'test@mailbook.dev',
                ],
            ], $mail->to);

            return true;
        });
    }

    public function test_can_send_variant(): void
    {
        Mailbook::add(TestMail::class)
            ->variant('wrong variant', fn () => new OtherMail)
            ->variant('Test variant', fn () => new TestMail);
        $this->get(route('mailbook.send', ['class' => TestMail::class, 'variant' => 'test-variant']))
            ->assertRedirect();
        Mail::assertSent(TestMail::class);
    }
}
