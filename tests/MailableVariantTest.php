<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests;

use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Tests\Fixtures\Mails\TestMail;

class MailableVariantTest extends TestCase
{
    public function test_executes_the_closure_once(): void
    {
        $executed = 0;

        $mailable = Mailbook::add(TestMail::class)
            ->variant('test', function () use (&$executed) {
                $executed++;

                return new TestMail;
            });

        $mailable->selectVariant('test');
        $mailable->subject();
        $mailable->to();
        $mailable->content();

        self::assertSame(1, $executed);
    }
}
