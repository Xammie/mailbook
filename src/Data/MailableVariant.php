<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Data;

use Closure;
use Illuminate\Contracts\Mail\Mailable;
use Xammie\Mailbook\MailableResolver;

/**
 * @internal
 */
class MailableVariant
{
    private ?MailableResolver $resolver = null;

    public function __construct(
        public string $label,
        public string $slug,
        public string|Mailable|Closure $closure,
        public mixed $notifiable = null,
    ) {
    }

    public function resolver(): MailableResolver
    {
        return $this->resolver ??= new MailableResolver($this->closure, $this->notifiable);
    }
}
