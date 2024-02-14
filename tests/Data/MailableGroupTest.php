<?php

declare(strict_types=1);

use Xammie\Mailbook\Data\MailableGroup;

it('can create mailable group', function (): void {
    $group = new MailableGroup(
        label: 'Test group',
        items: collect()
    );

    expect($group->label)->toBe('Test group');
});
