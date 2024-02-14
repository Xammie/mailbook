<?php

declare(strict_types=1);

namespace Xammie\Mailbook;

use Stringable;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Message;
use Symfony\Component\Mime\MessageConverter;
use Xammie\Mailbook\Facades\Mailbook as MailbookFacade;

/**
 * @internal
 */
class MailbookTransport extends AbstractTransport implements Stringable
{
    protected function doSend(SentMessage $message): void
    {
        /** @var Message $originalMessage */
        $originalMessage = $message->getOriginalMessage();

        $email = MessageConverter::toEmail($originalMessage);

        MailbookFacade::setMessage($email);
    }

    public function __toString(): string
    {
        return 'mailbook';
    }
}
