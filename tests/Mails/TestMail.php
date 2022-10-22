<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests\Mails;

use Illuminate\Mail\Mailable;

final class TestMail extends Mailable
{
    public function build(): self
    {
        return $this->html('Test mail')->subject('Test email subject');
    }
}
