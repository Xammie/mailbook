<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests\Mails;

use Illuminate\Mail\Mailable;

final class WithAttachmentsMail extends Mailable
{
    public function __construct()
    {
    }

    public function build(): self
    {
        return $this
            ->html('This mail has attachments')
            ->subject('Test email subject')
            ->attachData('test attachment', 'document.pdf')
            ->attachFromStorage(__DIR__.'/WithAttachmentsMail.php');
    }
}
