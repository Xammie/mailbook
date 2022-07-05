<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

final class TestMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct()
    {
    }

    public function build(): self
    {
        return $this->markdown('mailbook::test-email')->subject('Test email subject');
    }
}
