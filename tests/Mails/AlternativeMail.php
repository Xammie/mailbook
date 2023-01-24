<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests\Mails;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class AlternativeMail extends Mailable
{
    public function envelope(): Envelope
    {
        return new Envelope(
            cc: [new Address('foo@example.com', 'Example Name')],
            subject: 'Invoice Paid',
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: 'Test mail content',
        );
    }
}
