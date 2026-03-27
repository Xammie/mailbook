<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests\Traits;

use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Tests\Fixtures\Mails\TestMail;
use Xammie\Mailbook\Tests\TestCase;
use Xammie\Mailbook\Traits\HasLabel;

class HasLabelTest extends TestCase
{
    public function test_can_get_label(): void
    {
        $item = Mailbook::add(TestMail::class);
        $this->assertSame('Test Mail', $item->getLabel());
    }

    public function test_can_set_label(): void
    {
        $instance = new class
        {
            use HasLabel;
        };
        $instance->label('Test label');
        $this->assertSame('Test label', $instance->getLabel());
    }
}
