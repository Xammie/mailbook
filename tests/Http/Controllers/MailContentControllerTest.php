<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Support\FakeSeedGenerator;
use Xammie\Mailbook\Tests\Fixtures\Mails\OtherMail;
use Xammie\Mailbook\Tests\Fixtures\Mails\TestMail;
use Xammie\Mailbook\Tests\Fixtures\Mails\TranslatedMail;
use Xammie\Mailbook\Tests\Fixtures\Mails\TranslatedNotification;
use Xammie\Mailbook\Tests\Support\FakeSeedGeneratorExpectation;
use Xammie\Mailbook\Tests\TestCase;

class MailContentControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('mailbook.locales', [
            'en' => 'English',
            'nl' => 'Dutch',
            'de' => 'German',
        ]);
        app('translator')->addJsonPath(__DIR__.'/../../lang');
    }

    public function test_can_render(): void
    {
        Mailbook::add(TestMail::class);
        Mailbook::add(OtherMail::class);

        $this->get(route('mailbook.content', ['class' => TestMail::class, 's' => '123']))
            ->assertSuccessful()
            ->assertSeeText('Test mail');
    }

    public function test_can_render_with_seed(): void
    {
        Mailbook::add(TestMail::class);

        $fakeSeedGenerator = FakeSeedGeneratorExpectation::factory();
        $fakeSeedGenerator->expectsRestoreSeed('123');

        $this->app->instance(FakeSeedGenerator::class, $fakeSeedGenerator->mock);

        $this->get(route('mailbook.content', ['class' => TestMail::class, 's' => '123']))
            ->assertSuccessful()
            ->assertSeeText('Test mail');
    }

    public function test_renders_custom_script(): void
    {
        Mailbook::add(TestMail::class);

        $this->get(route('mailbook.content', ['class' => TestMail::class, 's' => '123']))
            ->assertSee('<script defer>', escape: false);
    }

    public function test_cannot_render_without_class(): void
    {
        Mailbook::add(OtherMail::class);

        $this->get(route('mailbook.content'))
            ->assertStatus(404);
    }

    public function test_can_render_different_locale_mailable(): void
    {
        Mailbook::add(TranslatedMail::class);

        $this->get(route('mailbook.content', ['class' => TranslatedMail::class, 'locale' => 'nl']))
            ->assertSuccessful()
            ->assertSeeText('Dit is een test mail');
    }

    public function test_can_render_different_locale_notification(): void
    {
        Mailbook::add(TranslatedNotification::class);

        $this->get(route('mailbook.content', ['class' => TranslatedNotification::class, 'locale' => 'nl']))
            ->assertSuccessful()
            ->assertSeeText('Dit is een test mail');
    }

    public function test_can_render_without_locales(): void
    {
        config()->set('mailbook.locales', []);

        Mailbook::add(TranslatedMail::class);

        $this->get(route('mailbook.content', ['class' => TranslatedMail::class]))->assertSuccessful();
    }

    public function test_can_render_default_variant(): void
    {
        Mailbook::add(TestMail::class)
            ->variant('First variant', fn (): TestMail => new TestMail)
            ->variant('Second variant', fn (): OtherMail => new OtherMail);

        $this->get(route('mailbook.content', ['class' => TestMail::class]))
            ->assertSuccessful()
            ->assertSeeText('Test mail');
    }

    public function test_can_render_selected_variant(): void
    {
        Mailbook::add(TestMail::class)
            ->variant('Second variant', fn (): OtherMail => new OtherMail)
            ->variant('First variant', fn (): TestMail => new TestMail);

        $this->get(route('mailbook.content', ['class' => TestMail::class, 'variant' => 'first-variant']))
            ->assertSuccessful()
            ->assertSeeText('Test mail');
    }

    public function test_cannot_render_unknown_variant(): void
    {
        Mailbook::add(TestMail::class)
            ->variant('First variant', fn (): TestMail => new TestMail)
            ->variant('Second variant', fn (): OtherMail => new OtherMail);

        $this->get(route('mailbook.content', ['class' => TestMail::class, 'variant' => 'unknown']))
            ->assertSuccessful()
            ->assertSeeText('Test mail');
    }

    public function test_can_render_closure(): void
    {
        Mailbook::add(fn () => new TestMail);

        $this->get(route('mailbook.content', ['class' => TestMail::class]))
            ->assertSuccessful()
            ->assertSeeText('Test mail');
    }

    public function test_cannot_render_without_mailables(): void
    {
        $this->get(route('mailbook.content', ['class' => TestMail::class]))
            ->assertStatus(500);
    }

    public function test_cannot_render_with_unknown_mailable(): void
    {
        Mailbook::add(TestMail::class);

        $this->get(route('mailbook.content', ['class' => 'test-mail']))
            ->assertStatus(404);
    }

    public function test_rolls_back_database_changes(): void
    {
        config()->set('mailbook.database_rollback', true);

        self::assertSame(0, DB::transactionLevel());

        Mailbook::add(function () {
            self::assertSame(1, DB::transactionLevel());

            return new TestMail;
        });

        $this->get(route('mailbook.content', ['class' => TestMail::class]))
            ->assertSuccessful()
            ->assertSeeText('Test mail');

        self::assertSame(0, DB::transactionLevel());
    }

    public function test_does_not_rollback_database_changes_when_disabled(): void
    {
        config()->set('mailbook.database_rollback', false);

        self::assertSame(0, DB::transactionLevel());

        Mailbook::add(function () {
            self::assertSame(0, DB::transactionLevel());

            return new TestMail;
        });

        $this->get(route('mailbook.content', ['class' => TestMail::class]))
            ->assertSuccessful()
            ->assertSeeText('Test mail');

        self::assertSame(0, DB::transactionLevel());
    }
}
