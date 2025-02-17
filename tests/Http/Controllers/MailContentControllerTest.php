<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Tests\Fixtures\Mails\OtherMail;
use Xammie\Mailbook\Tests\Fixtures\Mails\TestMail;
use Xammie\Mailbook\Tests\Fixtures\Mails\TranslatedMail;
use Xammie\Mailbook\Tests\Fixtures\Mails\TranslatedNotification;

use function Pest\Laravel\get;

it('can render', function (): void {
    Mailbook::add(TestMail::class);
    Mailbook::add(OtherMail::class);

    get(route('mailbook.content', ['class' => TestMail::class, 's' => '123']))
        ->assertSuccessful()
        ->assertSeeText('Test mail');
});

it('renders custom script', function (): void {
    Mailbook::add(TestMail::class);

    get(route('mailbook.content', ['class' => TestMail::class, 's' => '123']))
        ->assertSee(value: '<script defer>', escape: false);
});

it('cannot render without class', function (): void {
    Mailbook::add(OtherMail::class);

    get(route('mailbook.content'))
        ->assertStatus(404);
});

it('can render different locale mailable', function (): void {
    config()->set('mailbook.locales', [
        'en' => 'English',
        'nl' => 'Dutch',
        'de' => 'German',
    ]);

    app('translator')->addJsonPath(__DIR__.'/../../lang');

    Mailbook::add(TranslatedMail::class);

    get(route('mailbook.content', ['class' => TranslatedMail::class, 'locale' => 'nl']))
        ->assertSuccessful()
        ->assertSeeText('Dit is een test mail');
});

it('can render different locale notification', function (): void {
    config()->set('mailbook.locales', [
        'en' => 'English',
        'nl' => 'Dutch',
        'de' => 'German',
    ]);

    app('translator')->addJsonPath(__DIR__.'/../../lang');

    Mailbook::add(TranslatedNotification::class);

    get(route('mailbook.content', ['class' => TranslatedNotification::class, 'locale' => 'nl']))
        ->assertSuccessful()
        ->assertSeeText('Dit is een test mail');
});

it('can render without locales', function (): void {
    config()->set('mailbook.locales', []);

    Mailbook::add(TranslatedMail::class);

    get(route('mailbook.content', ['class' => TranslatedMail::class]))->assertSuccessful();
});

it('can render default variant', function (): void {
    Mailbook::add(TestMail::class)
        ->variant('First variant', fn (): TestMail => new TestMail)
        ->variant('Second variant', fn (): OtherMail => new OtherMail);

    get(route('mailbook.content', ['class' => TestMail::class]))
        ->assertSuccessful()
        ->assertSeeText('Test mail');
});

it('can render selected variant', function (): void {
    Mailbook::add(TestMail::class)
        ->variant('Second variant', fn (): OtherMail => new OtherMail)
        ->variant('First variant', fn (): TestMail => new TestMail);

    get(route('mailbook.content', ['class' => TestMail::class, 'variant' => 'first-variant']))
        ->assertSuccessful()
        ->assertSeeText('Test mail');
});

it('cannot render unknown variant', function (): void {
    Mailbook::add(TestMail::class)
        ->variant('First variant', fn (): TestMail => new TestMail)
        ->variant('Second variant', fn (): OtherMail => new OtherMail);

    get(route('mailbook.content', ['class' => TestMail::class, 'variant' => 'unknown']))
        ->assertSuccessful()
        ->assertSeeText('Test mail');
});

it('can render closure', function (): void {
    Mailbook::add(fn () => new TestMail);

    get(route('mailbook.content', ['class' => TestMail::class]))
        ->assertSuccessful()
        ->assertSeeText('Test mail');
});

it('cannot render without mailables', function (): void {
get(route('mailbook.content', ['class' => TestMail::class]))
->assertStatus(500);
    });

it('cannot render with unknown mailable', function (): void {
    Mailbook::add(TestMail::class);

    get(route('mailbook.content', ['class' => 'test-mail']))
        ->assertStatus(404);
});

it('rolls back database changes', function (): void {
    config()->set('mailbook.database_rollback', true);

    expect(DB::transactionLevel())->toBe(0);

    Mailbook::add(function () {
        expect(DB::transactionLevel())->toBe(1);

        return new TestMail;
    });

    get(route('mailbook.content', ['class' => TestMail::class]))
        ->assertSuccessful()
        ->assertSeeText('Test mail');

    expect(DB::transactionLevel())->toBe(0);
});

it('does not rollback database changes when disabled', function (): void {
    config()->set('mailbook.database_rollback', false);

    expect(DB::transactionLevel())->toBe(0);

    Mailbook::add(function () {
        expect(DB::transactionLevel())->toBe(0);

        return new TestMail;
    });

    get(route('mailbook.content', ['class' => TestMail::class]))
        ->assertSuccessful()
        ->assertSeeText('Test mail');

    expect(DB::transactionLevel())->toBe(0);
});
