<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests\Http\Controllers;

use Xammie\Mailbook\Exceptions\MailbookException;
use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Support\FakeSeedGenerator;
use Xammie\Mailbook\Tests\Fixtures\Mails\OtherMail;
use Xammie\Mailbook\Tests\Fixtures\Mails\TestMail;
use Xammie\Mailbook\Tests\Fixtures\Mails\TestNotification;
use Xammie\Mailbook\Tests\Fixtures\Mails\TranslatedMail;
use Xammie\Mailbook\Tests\Fixtures\User;
use Xammie\Mailbook\Tests\Support\FakeSeedGeneratorExpectation;
use Xammie\Mailbook\Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    private FakeSeedGeneratorExpectation $fakeSeedGenerator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fakeSeedGenerator = FakeSeedGeneratorExpectation::factory();
        $this->app->instance(FakeSeedGenerator::class, $this->fakeSeedGenerator->mock);
    }

    public function test_can_render_default(): void
    {
        $this->fakeSeedGenerator->expectsGetCurrentSeed(123456);

        Mailbook::add(TestMail::class);
        Mailbook::add(OtherMail::class);

        $this->get(route('mailbook.dashboard'))
            ->assertSuccessful()
            ->assertSeeText('Mailbook')
            ->assertSeeText('Test email subject')
            ->assertViewHas([
                'subject' => 'Test email subject',
                'size' => '9 B',
                'meta' => [
                    'Subject' => 'Test email subject',
                    'From' => ['"Example" <hello@example.com>'],
                ],
                'attachments' => [],
                'preview' => 'http://localhost/mailbook/content?class=Xammie%5CMailbook%5CTests%5CFixtures%5CMails%5CTestMail&locale=en&s=123456',
            ]);
    }

    public function test_can_get_meta_without_seed(): void
    {
        $this->fakeSeedGenerator->expectsGetCurrentSeed(null);

        Mailbook::add(OtherMail::class);

        $this->get(route('mailbook.dashboard'))
            ->assertSuccessful()
            ->assertViewHas([
                'subject' => 'Hello!',
                'size' => '20 B',
                'meta' => [
                    'Subject' => 'Hello!',
                    'From' => ['"Harry Potter" <harry@example.com>'],
                    'To' => ['"Mailbook" <example@mailbook.dev>'],
                    'Reply To' => ['"Support" <questions@example.com>'],
                    'Cc' => ['"Mailbook" <cc@mailbook.dev>'],
                    'Bcc' => ['"Mailbook" <bcc@mailbook.dev>'],
                ],
                'attachments' => [],
                'preview' => 'http://localhost/mailbook/content?class=Xammie%5CMailbook%5CTests%5CFixtures%5CMails%5COtherMail&locale=en',
            ]);
    }

    public function test_can_get_meta(): void
    {
        $this->fakeSeedGenerator->expectsGetCurrentSeed(123456);

        Mailbook::add(OtherMail::class);

        $this->get(route('mailbook.dashboard'))
            ->assertSuccessful()
            ->assertViewHas([
                'subject' => 'Hello!',
                'size' => '20 B',
                'meta' => [
                    'Subject' => 'Hello!',
                    'From' => ['"Harry Potter" <harry@example.com>'],
                    'To' => ['"Mailbook" <example@mailbook.dev>'],
                    'Reply To' => ['"Support" <questions@example.com>'],
                    'Cc' => ['"Mailbook" <cc@mailbook.dev>'],
                    'Bcc' => ['"Mailbook" <bcc@mailbook.dev>'],
                ],
                'attachments' => [],
                'preview' => 'http://localhost/mailbook/content?class=Xammie%5CMailbook%5CTests%5CFixtures%5CMails%5COtherMail&locale=en&s=123456',
            ]);
    }

    public function test_can_render_without_locales(): void
    {
        $this->fakeSeedGenerator->expectsGetCurrentSeed(null);

        Mailbook::add(TestMail::class);

        config()->set('mailbook.locales', []);

        $this->get(route('mailbook.dashboard'))->assertSuccessful();
    }

    public function test_can_render_selected(): void
    {
        $this->fakeSeedGenerator->expectsGetCurrentSeed(null);

        Mailbook::add(OtherMail::class);
        Mailbook::add(TestMail::class);

        $this->get(route('mailbook.dashboard', ['selected' => TestMail::class]))
            ->assertSuccessful()
            ->assertSeeText('Mailbook')
            ->assertSeeText('Test email subject');
    }

    public function test_can_render_default_locale(): void
    {
        $this->fakeSeedGenerator->expectsGetCurrentSeed(null);

        config()->set('mailbook.locales', [
            'en' => 'English',
            'nl' => 'Dutch',
            'de' => 'German',
        ]);

        Mailbook::add(TranslatedMail::class);

        $this->get(route('mailbook.dashboard', ['selected' => TranslatedMail::class]))
            ->assertSuccessful()
            ->assertViewHas('subject', 'Example email subject')
            ->assertViewHas('currentLocale', 'en');
    }

    public function test_can_render_locale(): void
    {
        $this->fakeSeedGenerator->expectsGetCurrentSeed(null);

        config()->set('mailbook.locales', [
            'en' => 'English',
            'nl' => 'Dutch',
            'de' => 'German',
        ]);

        app('translator')->addJsonPath(__DIR__.'/../../lang');

        Mailbook::add(TranslatedMail::class);

        $this->get(route('mailbook.dashboard', ['selected' => TranslatedMail::class, 'locale' => 'nl']))
            ->assertSuccessful()
            ->assertViewHas('subject', 'Voorbeeld e-mail onderwerp')
            ->assertViewHas('currentLocale', 'nl');
    }

    public function test_cannot_render_unknown_locale(): void
    {
        $this->fakeSeedGenerator->expectsGetCurrentSeed(null);

        config()->set('mailbook.locales', [
            'en' => 'English',
            'nl' => 'Dutch',
            'de' => 'German',
        ]);

        Mailbook::add(TranslatedMail::class);

        $this->get(route('mailbook.dashboard', ['selected' => TranslatedMail::class, 'locale' => 'be']))
            ->assertSuccessful()
            ->assertViewHas('currentLocale', 'en');
    }

    public function test_can_render_default_variant(): void
    {
        $this->fakeSeedGenerator->expectsGetCurrentSeed(null);

        Mailbook::add(TestMail::class)
            ->variant('Test variant', fn () => new TestMail)
            ->variant('wrong variant', fn () => new OtherMail);

        $this->get(route('mailbook.dashboard'))
            ->assertSuccessful()
            ->assertSeeText('Mailbook')
            ->assertSeeText('Test email subject')
            ->assertSeeText('Test variant');
    }

    public function test_can_render_variant(): void
    {
        $this->fakeSeedGenerator->expectsGetCurrentSeed(null);

        Mailbook::add(TestMail::class)
            ->variant('wrong variant', fn () => new OtherMail)
            ->variant('Test variant', fn () => new TestMail);

        $this->get(route('mailbook.dashboard', ['selected' => TestMail::class, 'variant' => 'test-variant']))
            ->assertSuccessful()
            ->assertSeeText('Mailbook')
            ->assertSeeText('Test email subject')
            ->assertSeeText('Test variant');
    }

    public function test_can_render_closure(): void
    {
        $this->fakeSeedGenerator->expectsGetCurrentSeed(null);

        Mailbook::add(fn () => new TestMail);

        $this->get(route('mailbook.dashboard'))
            ->assertSuccessful()
            ->assertSeeText('Mailbook')
            ->assertSeeText('Test email subject');
    }

    public function test_cannot_render_without_mailables(): void
    {
        $this->expectException(MailbookException::class);
        $this->expectExceptionMessage('No mailbook mailables registered');
        $this->withoutExceptionHandling();
        $this->get(route('mailbook.dashboard'));
    }

    public function test_can_render_other_display(): void
    {
        $this->fakeSeedGenerator->expectsGetCurrentSeed(null);

        Mailbook::add(TestMail::class);

        $this->get(route('mailbook.dashboard', ['selected' => TestMail::class, 'display' => 'phone']))
            ->assertSuccessful()
            ->assertViewHas('display', 'phone');
    }

    public function test_can_disable_display_preview(): void
    {
        $this->fakeSeedGenerator->expectsGetCurrentSeed(null);
        config()->set('mailbook.display_preview', false);

        Mailbook::add(TestMail::class);

        $this->get(route('mailbook.dashboard', ['selected' => TestMail::class, 'display' => 'phone']))
            ->assertSuccessful()
            ->assertViewHas('display', fn ($value) => $value === null);
    }

    public function test_executes_the_closure_once(): void
    {
        $this->fakeSeedGenerator->expectsGetCurrentSeed(null);

        $executed = 0;
        Mailbook::add(function () use (&$executed) {
            $executed++;

            return new TestMail;
        });

        $this->get(route('mailbook.dashboard', ['selected' => TestMail::class, 'display' => 'phone']))->assertSuccessful();

        self::assertSame(1, $executed);
    }

    public function test_can_render_mailable_with_notifiable(): void
    {
        $this->fakeSeedGenerator->expectsGetCurrentSeed(null);

        Mailbook::to(new User(['email' => 'test@mailbook.dev']))->add(TestMail::class);

        $this->get(route('mailbook.dashboard'))
            ->assertSuccessful()
            ->assertSeeText('test@mailbook.dev');
    }

    public function test_can_render_notification_with_notifiable(): void
    {
        $this->fakeSeedGenerator->expectsGetCurrentSeed(null);

        Mailbook::to(new User(['email' => 'test@mailbook.dev']))->add(TestNotification::class);

        $this->get(route('mailbook.dashboard'))
            ->assertSuccessful()
            ->assertSeeText('test@mailbook.dev');
    }

    public function test_cannot_see_mail_form_by_default(): void
    {
        $this->fakeSeedGenerator->expectsGetCurrentSeed(null);

        Mailbook::add(TestMail::class);

        $this->get(route('mailbook.dashboard', ['class' => TestMail::class]))
            ->assertSuccessful()
            ->assertDontSee('Send');
    }

    public function test_can_see_send_button(): void
    {
        $this->fakeSeedGenerator->expectsGetCurrentSeed(null);
        config()->set('mailbook.send', true);
        config()->set('mailbook.send_to', 'max@mailbook.dev');

        Mailbook::add(TestMail::class);

        $this->get(route('mailbook.dashboard', ['class' => TestMail::class]))
            ->assertSuccessful()
            ->assertSee('Send to max@mailbook.dev');
    }

    public function test_can_fallback_to_unknown_class(): void
    {
        $this->fakeSeedGenerator->expectsGetCurrentSeed(null);

        Mailbook::add(TestMail::class);

        $this->get(route('mailbook.dashboard', ['selected' => 'random']))
            ->assertSuccessful();
    }

    public function test_can_render_lower_case_mailable(): void
    {
        $this->fakeSeedGenerator->expectsGetCurrentSeed(null);

        Mailbook::add(TestMail::class);

        $this->get(route('mailbook.dashboard', ['selected' => mb_strtolower(TestMail::class)]))
            ->assertSuccessful();
    }
}
