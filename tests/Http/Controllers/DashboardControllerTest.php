<?php

use function Pest\Laravel\get;
use function Pest\Laravel\withoutExceptionHandling;
use Xammie\Mailbook\Exceptions\MailbookException;
use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Tests\Mails\OtherMail;
use Xammie\Mailbook\Tests\Mails\TestMail;

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
            'from' => [
                'Example <hello@example.com>',
            ],
            'replyTo' => [],
            'to' => [],
            'cc' => [],
            'bcc' => [],
            'attachments' => collect(),
        ]);
});

it('can render selected', function () {
    Mailbook::add(OtherMail::class);
    Mailbook::add(TestMail::class);

    get(route('mailbook.dashboard', ['selected' => TestMail::class]))
        ->assertSuccessful()
        ->assertSeeText('Mailbook')
        ->assertSeeText('Test email subject');
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
