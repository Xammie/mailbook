<?php

declare(strict_types=1);

use Xammie\Mailbook\Data\MailableGroup;
use Xammie\Mailbook\Data\MailableItem;
use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Tests\Fixtures\Mails\OtherMail;
use Xammie\Mailbook\Tests\Fixtures\Mails\TestMail;
use Xammie\Mailbook\Tests\Fixtures\Mails\TranslatedMail;

it('can get mailables', function (): void {
    Mailbook::add(TestMail::class);
    Mailbook::add(OtherMail::class);

    $mailables = Mailbook::mailables();

    expect($mailables)->toHaveCount(2);
    expect($mailables->get(0)?->getLabel())->toBe('Test Mail');
    expect($mailables->get(1)?->getLabel())->toBe('Other Mail');
});

it('can get grouped mailables', function (): void {
    Mailbook::add(TestMail::class);

    Mailbook::category('Other')->group(function (): void {
        Mailbook::add(OtherMail::class);
        Mailbook::add(TranslatedMail::class);
    });

    $mailables = Mailbook::groupedMailables();

    expect($mailables)->toHaveCount(2);
    expect($mailables->get(0))->toBeInstanceOf(MailableItem::class);
    expect($mailables->get(1))->toBeInstanceOf(MailableGroup::class);

    /** @var MailableGroup $group */
    $group = $mailables->get(1);

    expect($group->items->get(0))->toBeInstanceOf(MailableItem::class);
    expect($group->items->get(0)?->getLabel())->toBe('Other Mail');
    expect($group->items->get(1)?->getLabel())->toBe('Translated Mail');
});
