<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Support\FakeSeedGenerator;
use Xammie\Mailbook\Tests\Fixtures\Mails\OtherMail;
use Xammie\Mailbook\Tests\Fixtures\Mails\TestMail;
use Xammie\Mailbook\Tests\Fixtures\Mails\TranslatedMail;
use Xammie\Mailbook\Tests\Fixtures\Mails\UnsluggableSubjectMail;
use Xammie\Mailbook\Tests\Support\FakeSeedGeneratorExpectation;
use Xammie\Mailbook\Tests\TestCase;

class MailDownloadControllerTest extends TestCase
{
    public function test_can_download(): void
    {
        Mailbook::add(TestMail::class);

        $response = $this->get(route('mailbook.download', ['class' => TestMail::class]));

        $response->assertStatus(200);
        $response->assertDownload('test-email-subject.eml');
        $response->assertHeader('Content-Type', 'message/rfc822');

        $content = $response->getContent();

        self::assertStringContainsString('Subject: Test email subject', $content);
        self::assertStringContainsString('hello@example.com', $content);
        self::assertStringContainsString('MIME-Version: 1.0', $content);
        self::assertStringContainsString('Date:', $content);
        self::assertStringContainsString('Message-ID:', $content);
        self::assertStringContainsString('Test mail', $content);
        self::assertStringNotContainsString('remove@mailbook.dev', $content);
    }

    public function test_can_download_mail_with_recipients(): void
    {
        Mailbook::add(OtherMail::class);

        $response = $this->get(route('mailbook.download', ['class' => OtherMail::class]));

        $response->assertSuccessful();
        $response->assertDownload('hello.eml');

        $content = $response->getContent();

        self::assertStringContainsString('From: Harry Potter <harry@example.com>', $content);
        self::assertStringContainsString('Reply-To: Support <questions@example.com>', $content);
        self::assertStringContainsString('To: Mailbook <example@mailbook.dev>', $content);
        self::assertStringContainsString('Cc: Mailbook <cc@mailbook.dev>', $content);
        self::assertStringNotContainsString('remove@mailbook.dev', $content);
        self::assertStringNotContainsString('bcc@mailbook.dev', $content);
    }

    public function test_can_download_selected_variant(): void
    {
        Mailbook::add(TestMail::class)
            ->variant('Other variant', fn () => new OtherMail)
            ->variant('Test variant', fn () => new TestMail);

        $this->get(route('mailbook.download', ['class' => TestMail::class, 'variant' => 'other-variant']))
            ->assertSuccessful()
            ->assertDownload('hello.eml');
    }

    public function test_can_download_different_locale(): void
    {
        config()->set('mailbook.locales', [
            'en' => 'English',
            'nl' => 'Dutch',
        ]);
        app('translator')->addJsonPath(__DIR__.'/../../lang');

        Mailbook::add(TranslatedMail::class);

        $response = $this->get(route('mailbook.download', ['class' => TranslatedMail::class, 'locale' => 'nl']));

        $response->assertSuccessful();
        $response->assertDownload('voorbeeld-e-mail-onderwerp.eml');

        self::assertStringContainsString('Dit is een test mail', $response->getContent());
    }

    public function test_can_download_with_seed(): void
    {
        Mailbook::add(TestMail::class);

        $fakeSeedGenerator = FakeSeedGeneratorExpectation::factory();
        $fakeSeedGenerator->expectsRestoreSeed('123');

        $this->app->instance(FakeSeedGenerator::class, $fakeSeedGenerator->mock);

        $this->get(route('mailbook.download', ['class' => TestMail::class, 's' => '123']))
            ->assertSuccessful();
    }

    public function test_falls_back_to_mailable_class_for_unsluggable_subject(): void
    {
        Mailbook::add(UnsluggableSubjectMail::class);

        $response = $this->get(route('mailbook.download', ['class' => UnsluggableSubjectMail::class]));

        $response->assertSuccessful();
        $response->assertDownload('unsluggablesubjectmail.eml');

        self::assertStringContainsString('Subject: ***', $response->getContent());
    }

    public function test_cannot_download_without_class(): void
    {
        Mailbook::add(OtherMail::class);

        $this->get(route('mailbook.download'))
            ->assertStatus(404);
    }

    public function test_cannot_download_with_unknown_mailable(): void
    {
        Mailbook::add(TestMail::class);

        $this->get(route('mailbook.download', ['class' => 'test-mail']))
            ->assertStatus(404);
    }

    public function test_cannot_download_without_mailables(): void
    {
        $this->get(route('mailbook.download', ['class' => TestMail::class]))
            ->assertStatus(500);
    }

    public function test_rolls_back_database_changes(): void
    {
        config()->set('mailbook.database_rollback', true);

        self::assertSame(0, DB::transactionLevel());

        Mailbook::add(function () {
            self::assertSame(1, DB::transactionLevel());

            return new TestMail;
        });

        $this->get(route('mailbook.download', ['class' => TestMail::class]))
            ->assertSuccessful()
            ->assertDownload('test-email-subject.eml');

        self::assertSame(0, DB::transactionLevel());
    }
}
