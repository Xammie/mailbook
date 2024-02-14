<?php

declare(strict_types=1);

use Xammie\Mailbook\Data\MailCollection;
use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Tests\Fixtures\Mails\TestMail;

it('can collect mails', function (): void {
    $collection = new MailCollection();

    expect($collection->all())->toBeEmpty();

    $collection->push(Mailbook::add(TestMail::class));

    expect($collection->all())->toHaveCount(1);
});
