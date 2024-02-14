<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests\Fixtures\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ShouldQueueMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function build(): self
    {
        return $this->html('Test mail')->subject('Test email subject');
    }
}
