<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests\Mails;

use Illuminate\Mail\Mailable;

final class TestBinding extends Mailable
{
    public function __construct(string $random)
    {
    }

    public function build(): self
    {
        return $this->markdown('mailbook::test-email')->subject('Test email subject');
    }
}
