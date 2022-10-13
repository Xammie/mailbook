<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests\Mails;

use Illuminate\Mail\Mailable;

final class OtherMail extends Mailable
{
    public function build(): self
    {
        return $this
            ->html('Are you ignoring me?')
            ->from('harry@example.com', 'Harry Potter')
            ->replyTo('questions@example.com', 'Support')
            ->to('example@mailbook.dev', 'Mailbook')
            ->cc('cc@mailbook.dev', 'Mailbook')
            ->bcc('bcc@mailbook.dev', 'Mailbook')
            ->subject('Hello!');
    }
}
