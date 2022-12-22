<?php

namespace Xammie\Mailbook;

use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Symfony\Component\Mime\Email;
use Xammie\Mailbook\Facades\Mailbook as MailbookFacade;

class MailableSender
{
    private mixed $originalDriver = null;

    public function __construct(private Mailable|Notification $subject)
    {
    }

    public function collect(): ResolvedMail
    {
        $this->injectDriver();
        $this->useLocale();
        $this->send();

        /** @var Email $mail */
        $mail = MailbookFacade::getMessage();

        $this->cleanup();

        return new ResolvedMail($mail);
    }

    private function injectDriver(): void
    {
        $this->originalDriver = Config::get('mail.default');
        Config::set('mail.default', 'mailbook');
        Config::set('mail.driver', 'mailbook');
        Config::set('mail.mailers.mailbook', ['transport' => 'mailbook']);
    }

    private function cleanup(): void
    {
        MailbookFacade::clearMessage();

        Config::set('mail.default', $this->originalDriver);
        Config::set('mail.driver', $this->originalDriver);
    }

    private function useLocale(): void
    {
        $locale = MailbookFacade::getLocale();

        if (! $locale) {
            return;
        }

        $this->subject->locale($locale);
    }

    private function send(): void
    {
        if ($this->subject instanceof Notification) {
            NotificationFacade::route('mail', 'remove@mailbook.dev')->notifyNow($this->subject);
        } else {
            Mail::to('remove@mailbook.dev')->send($this->subject);
        }
    }
}
