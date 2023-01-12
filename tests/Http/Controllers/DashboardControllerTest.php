<?php

use function Pest\Laravel\get;
use function Pest\Laravel\withoutExceptionHandling;
use Xammie\Mailbook\Exceptions\MailbookException;
use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Tests\Mails\OtherMail;
use Xammie\Mailbook\Tests\Mails\TestMail;
use Xammie\Mailbook\Tests\Mails\TranslatedMail;

it('can render default', function () {
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
            'preview' => 'http://localhost/mailbook/content?class=Xammie%5CMailbook%5CTests%5CMails%5CTestMail&locale=en',
        ]);
});

it('can get meta', function () {
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
            'preview' => 'http://localhost/mailbook/content?class=Xammie%5CMailbook%5CTests%5CMails%5COtherMail&locale=en',
        ]);
});

it('can render without locales', function () {
    Mailbook::add(TestMail::class);

    config()->set('mailbook.locales', []);

    get(route('mailbook.dashboard'))->assertSuccessful();
});

it('can render selected', function () {
    Mailbook::add(OtherMail::class);
    Mailbook::add(TestMail::class);

    get(route('mailbook.dashboard', ['selected' => TestMail::class]))
        ->assertSuccessful()
        ->assertSeeText('Mailbook')
        ->assertSeeText('Test email subject');
});

it('can render default locale', function () {
    config()->set('mailbook.locales', [
        'en' => 'English',
        'nl' => 'Dutch',
        'de' => 'German',
    ]);

    Mailbook::add(TranslatedMail::class);

    get(route('mailbook.dashboard', ['selected' => TranslatedMail::class]))
        ->assertSuccessful()
        ->assertViewHas('subject', 'Example email subject')
        ->assertViewHas('currentLocale', 'en')
        ->assertViewHas('localeLabel', 'English');
});

it('can render locale', function () {
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
        ->assertViewHas('currentLocale', 'nl')
        ->assertViewHas('localeLabel', 'Dutch');
});

it('cannot render unknown locale', function () {
    config()->set('mailbook.locales', [
        'en' => 'English',
        'nl' => 'Dutch',
        'de' => 'German',
    ]);

    Mailbook::add(TranslatedMail::class);

    get(route('mailbook.dashboard', ['selected' => TranslatedMail::class, 'locale' => 'be']))
        ->assertSuccessful()
        ->assertViewHas('currentLocale', 'en')
        ->assertViewHas('localeLabel', 'English');
});

it('can render default variant', function () {
    Mailbook::add(TestMail::class)
        ->variant('Test variant', fn () => new TestMail())
        ->variant('wrong variant', fn () => new OtherMail());

    get(route('mailbook.dashboard'))
        ->assertSuccessful()
        ->assertSeeText('Mailbook')
        ->assertSeeText('Test email subject')
        ->assertSeeText('Test variant');
});

it('can render variant', function () {
    Mailbook::add(TestMail::class)
        ->variant('wrong variant', fn () => new OtherMail())
        ->variant('Test variant', fn () => new TestMail());

    get(route('mailbook.dashboard', ['selected' => TestMail::class, 'variant' => 'test-variant']))
        ->assertSuccessful()
        ->assertSeeText('Mailbook')
        ->assertSeeText('Test email subject')
        ->assertSeeText('Test variant');
});

it('can render closure', function () {
    Mailbook::add(fn () => new TestMail());

    get(route('mailbook.dashboard'))
        ->assertSuccessful()
        ->assertSeeText('Mailbook')
        ->assertSeeText('Test email subject');
});

it('cannot render without mailables', function () {
    withoutExceptionHandling();

    get(route('mailbook.dashboard'));
})
    ->throws(MailbookException::class, 'No mailbook mailables registered');

it('can render other display', function () {
    Mailbook::add(TestMail::class);

    get(route('mailbook.dashboard', ['selected' => TestMail::class, 'display' => 'phone']))
        ->assertSuccessful()
        ->assertViewHas('display', 'phone');
});

it('can disable display preview', function () {
    config()->set('mailbook.display_preview', false);

    Mailbook::add(TestMail::class);

    get(route('mailbook.dashboard', ['selected' => TestMail::class, 'display' => 'phone']))
        ->assertSuccessful()
        ->assertViewHas('display', null);
});

it('executes the close once', function () {
    $executed = 0;

    Mailbook::add(function () use (&$executed) {
        $executed++;

        return new TestMail();
    });

    get(route('mailbook.dashboard', ['selected' => TestMail::class, 'display' => 'phone']))
        ->assertSuccessful();

    expect($executed)->toBe(1);
});
