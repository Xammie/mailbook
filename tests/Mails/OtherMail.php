<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests\Mails;

use Illuminate\Mail\Mailable;

final class OtherMail extends Mailable
{
    public function __construct()
    {
    }

    public function build(): self
    {
        return $this->html('Are you ignoring me?')->subject('Hello!');
    }
}
