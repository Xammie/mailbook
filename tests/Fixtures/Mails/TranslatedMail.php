<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests\Fixtures\Mails;

use Illuminate\Mail\Mailable;

class TranslatedMail extends Mailable
{
    public function build(): self
    {
        return $this
            ->html(__('This is a test mail'))
            ->subject(__('Example email subject'));
    }
}
