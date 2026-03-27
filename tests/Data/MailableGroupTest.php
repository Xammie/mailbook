<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests\Data;

use Xammie\Mailbook\Data\MailableGroup;
use Xammie\Mailbook\Tests\TestCase;

class MailableGroupTest extends TestCase
{
    public function test_can_create_mailable_group(): void
    {
        $group = new MailableGroup(
            label: 'Test group',
            items: collect()
        );
        $this->assertSame('Test group', $group->label);
    }
}
