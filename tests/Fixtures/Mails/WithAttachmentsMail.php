<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests\Fixtures\Mails;

use Illuminate\Mail\Mailable;

class WithAttachmentsMail extends Mailable
{
    public function build(): self
    {
        return $this
            ->html('This mail has attachments')
            ->subject('Test email subject')
            ->attachData('test attachment', 'document.pdf')
            ->attachData('test attachment', 'rows.csv');
    }
}
