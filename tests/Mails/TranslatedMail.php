<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests\Mails;

use Illuminate\Mail\Mailable;

final class TranslatedMail extends Mailable
{
    public function build(): self
    {
        return $this->html(__('This is a test mail'))->subject('Test email subject');
    }
}
