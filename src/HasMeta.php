<?php

namespace Xammie\Mailbook;

trait HasMeta
{
    public function meta(): array
    {
        return array_filter([
            'Subject' => $this->subject(),
            'From' => $this->from(),
            'Reply To' => $this->replyTo(),
            'To' => $this->to(),
            'Cc' => $this->cc(),
            'Bcc' => $this->bcc(),
            'Theme' => $this->theme(),
            'Mailer' => $this->mailer(),
        ]);
    }
}
