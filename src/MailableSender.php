<?php

namespace Xammie\Mailbook;

use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Symfony\Component\Mime\Email;
use Xammie\Mailbook\Facades\Mailbook as MailbookFacade;

class MailableSender
{
    private ConfigInjector $injector;

    public function __construct(private Mailable|Notification $subject)
    {
        $this->injector = new ConfigInjector();
    }

    public function collect(): ResolvedMail
    {
        $this->inject();
        $this->useLocale();
        $this->send();

        /** @var Email $mail */
        $mail = MailbookFacade::getMessage();

        $this->cleanup();

        return new ResolvedMail($mail);
    }

    private function inject(): void
    {
        $this->injector
            ->set('mail.default', 'mailbook')
            ->set('mail.driver', 'mailbook')
            ->set('mail.mailers.mailbook', ['transport' => 'mailbook'])
            ->set('queue.default', 'sync');
    }

    private function cleanup(): void
    {
        MailbookFacade::clearMessage();

        $this->injector->revert();
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
