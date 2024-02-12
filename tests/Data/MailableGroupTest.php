<?php

use Xammie\Mailbook\Data\MailableGroup;

it('can create mailable group', function () {
    $group = new MailableGroup(
        label: 'Test group',
        items: collect()
    );

    expect($group->label)->toBe('Test group');
});
