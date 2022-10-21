<?php

use Illuminate\Support\Facades\DB;
use function Pest\Laravel\get;
use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Tests\Mails\OtherMail;
use Xammie\Mailbook\Tests\Mails\TestMail;
use Xammie\Mailbook\Tests\Mails\TranslatedMail;

it('can render', function () {
    Mailbook::add(TestMail::class);
    Mailbook::add(OtherMail::class);

    get(route('mailbook.content', ['class' => TestMail::class]))
        ->assertSuccessful()
        ->assertSeeText('Test mail');
});

it('can render different locale', function () {
    app('translator')->addJsonPath(__DIR__.'/../../lang');

    Mailbook::add(TranslatedMail::class);

    get(route('mailbook.content', ['class' => TranslatedMail::class, 'locale' => 'nl']))
        ->assertSuccessful()
        ->assertSeeText('Dit is een test mail');
});

it('can render without locales', function () {
    Mailbook::add(TranslatedMail::class);

    config()->set('mailbook.locales', []);

    get(route('mailbook.content', ['class' => TranslatedMail::class]))->assertSuccessful();
});

it('can render default variant', function () {
    Mailbook::add(TestMail::class)
        ->variant('First variant', fn (): TestMail => new TestMail())
        ->variant('Second variant', fn (): OtherMail => new OtherMail());

    get(route('mailbook.content', ['class' => TestMail::class]))
        ->assertSuccessful()
        ->assertSeeText('Test mail');
});

it('can render selected variant', function () {
    Mailbook::add(TestMail::class)
        ->variant('Second variant', fn (): OtherMail => new OtherMail())
        ->variant('First variant', fn (): TestMail => new TestMail());

    get(route('mailbook.content', ['class' => TestMail::class, 'variant' => 'first-variant']))
        ->assertSuccessful()
        ->assertSeeText('Test mail');
});

it('cannot render unknown variant', function () {
    Mailbook::add(TestMail::class)
        ->variant('First variant', fn (): TestMail => new TestMail())
        ->variant('Second variant', fn (): OtherMail => new OtherMail());

    get(route('mailbook.content', ['class' => TestMail::class, 'variant' => 'unknown']))
        ->assertSuccessful()
        ->assertSeeText('Test mail');
});

it('can render closure', function () {
    Mailbook::add(fn () => new TestMail());

    get(route('mailbook.content', ['class' => TestMail::class]))
        ->assertSuccessful()
        ->assertSeeText('Test mail');
});

it('cannot render without mailables', function () {
    get(route('mailbook.content', ['class' => TestMail::class]))
        ->assertStatus(500);
});

it('cannot render with unknown mailable', function () {
    Mailbook::add(TestMail::class);

    get(route('mailbook.content', ['class' => 'test-mail']))
        ->assertStatus(400);
});

it('rolls back database changes', function () {
    config()->set('mailbook.database_rollback', true);

    expect(DB::transactionLevel())->toBe(0);

    Mailbook::add(function () {
        expect(DB::transactionLevel())->toBe(1);

        return new TestMail();
    });

    get(route('mailbook.content', ['class' => TestMail::class]))
        ->assertSuccessful()
        ->assertSeeText('Test mail');

    expect(DB::transactionLevel())->toBe(0);
});

it('does not rollback database changes when disabled', function () {
    config()->set('mailbook.database_rollback', false);

    expect(DB::transactionLevel())->toBe(0);

    Mailbook::add(function () {
        expect(DB::transactionLevel())->toBe(0);

        return new TestMail();
    });

    get(route('mailbook.content', ['class' => TestMail::class]))
        ->assertSuccessful()
        ->assertSeeText('Test mail');

    expect(DB::transactionLevel())->toBe(0);
});
