<?php

use Xammie\Mailbook\Data\MailCollection;
use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Tests\Fixtures\Mails\TestMail;

it('can collect mails', function () {
    $collection = new MailCollection();

    expect($collection->all())->toBeEmpty();

    $collection->push(Mailbook::add(TestMail::class));

    expect($collection->all())->toHaveCount(1);
});
