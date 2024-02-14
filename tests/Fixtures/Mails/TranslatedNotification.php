<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests\Fixtures\Mails;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TranslatedNotification extends Notification
{
    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(__('Example email subject'))
            ->line(__('This is a test mail'));
    }
}
