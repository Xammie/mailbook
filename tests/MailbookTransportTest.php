<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests;

use Xammie\Mailbook\MailbookTransport;

class MailbookTransportTest extends TestCase
{
    public function test_has_correct_transport_name(): void
    {
        $transport = new MailbookTransport;
        self::assertSame('mailbook', $transport->__toString());
    }
}
