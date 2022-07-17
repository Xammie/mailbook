<?php

namespace Xammie\Mailbook;

use Closure;
use Illuminate\Contracts\Mail\Mailable;

class MailableVariant
{
    public function __construct(
        public string $label,
        public string $slug,
        public string|Mailable|Closure $closure
    ) {
    }

    public function resolver(): MailableResolver
    {
        return new MailableResolver($this->closure);
    }
}
