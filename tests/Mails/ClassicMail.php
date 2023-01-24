<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests\Mails;

use Illuminate\Mail\Mailable;

class ClassicMail extends Mailable
{
    public function build(): self
    {
        return $this
            ->subject('Invoice Paid')
            ->cc('foo@example.com', 'Example Name')
            ->html('Test mail content');
    }
}
