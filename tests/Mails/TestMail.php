<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests\Mails;

use Illuminate\Mail\Mailable;

final class TestMail extends Mailable
{
    public function __construct()
    {
    }

    public function build(): self
    {
        return $this->markdown('mailbook::test-email')->subject('Test email subject');
    }
}
