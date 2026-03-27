<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests\Data;

use Xammie\Mailbook\Data\MailCollection;
use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Tests\Fixtures\Mails\TestMail;
use Xammie\Mailbook\Tests\TestCase;

class MailCollectionTest extends TestCase
{
    public function test_can_collect_mails(): void
    {
        $collection = new MailCollection;
        $this->assertEmpty($collection->all());
        $collection->push(Mailbook::add(TestMail::class));
        $this->assertCount(1, $collection->all());
    }
}
