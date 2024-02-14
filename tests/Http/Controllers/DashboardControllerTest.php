<?php

declare(strict_types=1);

use Xammie\Mailbook\Exceptions\MailbookException;
use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Support\FakeSeedGenerator;
use Xammie\Mailbook\Tests\Fixtures\Mails\OtherMail;
use Xammie\Mailbook\Tests\Fixtures\Mails\TestMail;
use Xammie\Mailbook\Tests\Fixtures\Mails\TestNotification;
use Xammie\Mailbook\Tests\Fixtures\Mails\TranslatedMail;
use Xammie\Mailbook\Tests\Fixtures\User;

use function Pest\Laravel\get;
use function Pest\Laravel\mock;
use function Pest\Laravel\withoutExceptionHandling;

it('can render default', function (): void {
    mock(FakeSeedGenerator::class)
        ->shouldReceive('getCurrentSeed')
        ->andReturn(123456);

    Mailbook::add(TestMail::class);
    Mailbook::add(OtherMail::class);

    get(route('mailbook.dashboard'))
        ->assertSuccessful()
        ->assertSeeText('Mailbook')
        ->assertSeeText('Test email subject')
        ->assertViewHas([
            'subject' => 'Test email subject',
            'size' => '9 B',
            'meta' => [
                'Subject' => 'Test email subject',
                'From' => [
                    '"Example" <hello@example.com>',
                ],
            ],
            'attachments' => [],
            'preview' => 'http://localhost/mailbook/content?class=Xammie%5CMailbook%5CTests%5CFixtures%5CMails%5CTestMail&locale=en&s=123456',
        ]);
});

it('can get meta without seed', function (): void {
    mock(FakeSeedGenerator::class)
        ->shouldReceive('getCurrentSeed')
        ->andReturn(null);

    Mailbook::add(OtherMail::class);

    get(route('mailbook.dashboard'))
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
});

it('can get meta', function (): void {
    mock(FakeSeedGenerator::class)
        ->shouldReceive('getCurrentSeed')
        ->andReturn(123456);

    Mailbook::add(OtherMail::class);

    get(route('mailbook.dashboard'))
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
});

it('can render without locales', function (): void {
    Mailbook::add(TestMail::class);

    config()->set('mailbook.locales', []);

    get(route('mailbook.dashboard'))->assertSuccessful();
});

it('can render selected', function (): void {
    Mailbook::add(OtherMail::class);
    Mailbook::add(TestMail::class);

    get(route('mailbook.dashboard', ['selected' => TestMail::class]))
        ->assertSuccessful()
        ->assertSeeText('Mailbook')
        ->assertSeeText('Test email subject');
});

it('can render default locale', function (): void {
    config()->set('mailbook.locales', [
        'en' => 'English',
        'nl' => 'Dutch',
        'de' => 'German',
    ]);

    Mailbook::add(TranslatedMail::class);

    get(route('mailbook.dashboard', ['selected' => TranslatedMail::class]))
        ->assertSuccessful()
        ->assertViewHas('subject', 'Example email subject')
        ->assertViewHas('currentLocale', 'en');
});

it('can render locale', function (): void {
    config()->set('mailbook.locales', [
        'en' => 'English',
        'nl' => 'Dutch',
        'de' => 'German',
    ]);

    app('translator')->addJsonPath(__DIR__.'/../../lang');

    Mailbook::add(TranslatedMail::class);

    get(route('mailbook.dashboard', ['selected' => TranslatedMail::class, 'locale' => 'nl']))
        ->assertSuccessful()
        ->assertViewHas('subject', 'Voorbeeld e-mail onderwerp')
        ->assertViewHas('currentLocale', 'nl');
});

it('cannot render unknown locale', function (): void {
    config()->set('mailbook.locales', [
        'en' => 'English',
        'nl' => 'Dutch',
        'de' => 'German',
    ]);

    Mailbook::add(TranslatedMail::class);

    get(route('mailbook.dashboard', ['selected' => TranslatedMail::class, 'locale' => 'be']))
        ->assertSuccessful()
        ->assertViewHas('currentLocale', 'en');
});

it('can render default variant', function (): void {
    Mailbook::add(TestMail::class)
        ->variant('Test variant', fn () => new TestMail())
        ->variant('wrong variant', fn () => new OtherMail());

    get(route('mailbook.dashboard'))
        ->assertSuccessful()
        ->assertSeeText('Mailbook')
        ->assertSeeText('Test email subject')
        ->assertSeeText('Test variant');
});

it('can render variant', function (): void {
    Mailbook::add(TestMail::class)
        ->variant('wrong variant', fn () => new OtherMail())
        ->variant('Test variant', fn () => new TestMail());

    get(route('mailbook.dashboard', ['selected' => TestMail::class, 'variant' => 'test-variant']))
        ->assertSuccessful()
        ->assertSeeText('Mailbook')
        ->assertSeeText('Test email subject')
        ->assertSeeText('Test variant');
});

it('can render closure', function (): void {
    Mailbook::add(fn () => new TestMail());

    get(route('mailbook.dashboard'))
        ->assertSuccessful()
        ->assertSeeText('Mailbook')
        ->assertSeeText('Test email subject');
});

it('cannot render without mailables', function (): void {
    withoutExceptionHandling();

    get(route('mailbook.dashboard'));
})
    ->throws(MailbookException::class, 'No mailbook mailables registered');

it('can render other display', function (): void {
    Mailbook::add(TestMail::class);

    get(route('mailbook.dashboard', ['selected' => TestMail::class, 'display' => 'phone']))
        ->assertSuccessful()
        ->assertViewHas('display', 'phone');
});

it('can disable display preview', function (): void {
    config()->set('mailbook.display_preview', false);

    Mailbook::add(TestMail::class);

    get(route('mailbook.dashboard', ['selected' => TestMail::class, 'display' => 'phone']))
        ->assertSuccessful()
        ->assertViewHas('display', fn ($value) => $value === null);
});

it('executes the close once', function (): void {
    $executed = 0;

    Mailbook::add(function () use (&$executed) {
        $executed++;

        return new TestMail();
    });

    get(route('mailbook.dashboard', ['selected' => TestMail::class, 'display' => 'phone']))
        ->assertSuccessful();

    expect($executed)->toBe(1);
});

it('can render mailable with notifiable', function (): void {
    Mailbook::to(new User(['email' => 'test@mailbook.dev']))->add(TestMail::class);

    get(route('mailbook.dashboard'))
        ->assertSuccessful()
        ->assertSeeText('test@mailbook.dev');
});

it('can render notification with notifiable', function (): void {
    Mailbook::to(new User(['email' => 'test@mailbook.dev']))->add(TestNotification::class);

    get(route('mailbook.dashboard'))
        ->assertSuccessful()
        ->assertSeeText('test@mailbook.dev');
});

it('cannot see mail form by default', function (): void {
    Mailbook::add(TestMail::class);

    get(route('mailbook.dashboard', ['class' => TestMail::class]))
        ->assertSuccessful()
        ->assertDontSee('Send');
});

it('can see send button', function (): void {
    config()->set('mailbook.send', true);
    config()->set('mailbook.send_to', 'max@mailbook.dev');
    Mailbook::add(TestMail::class);

    get(route('mailbook.dashboard', ['class' => TestMail::class]))
        ->assertSuccessful()
        ->assertSee('Send to max@mailbook.dev');
});

it('can fallback to unknown clas', function (): void {
    Mailbook::add(TestMail::class);

    get(route('mailbook.dashboard', ['selected' => 'random']))
        ->assertSuccessful();
});

it('can render lower case mailable', function (): void {
    Mailbook::add(TestMail::class);

    get(route('mailbook.dashboard', ['selected' => mb_strtolower(TestMail::class)]))
        ->assertSuccessful();
});
