<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class MailbookMail extends Mailable
{
    public function build(): self
    {
        return $this
            ->subject('Welcome to mailbook!')
            ->from('laravel@mailbook.dev', 'Mailbook')
            ->markdown('mail.mailbook');
    }
}
