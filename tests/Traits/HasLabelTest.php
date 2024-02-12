<?php

use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Tests\Fixtures\Mails\TestMail;
use Xammie\Mailbook\Traits\HasLabel;

it('can get label', function () {
    $item = Mailbook::add(TestMail::class);

    expect($item->getLabel())->toBe('Test Mail');
});

it('can set label', function () {
    $instance = new class
    {
        use HasLabel;
    };

    $instance->label('Test label');

    expect($instance->getLabel())->toBe('Test label');
});
