<?php

namespace Xammie\Mailbook\Tests\Fixtures\Mails;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TestNotification extends Notification
{
    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Test email subject')
            ->line('Thank you for using our application!');
    }
}
