<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests\Fixtures\Mails;

use Illuminate\Mail\Mailable;

class UnsluggableSubjectMail extends Mailable
{
    public function build(): self
    {
        return $this
            ->html('This mail has an unsluggable subject')
            ->subject('***');
    }
}
