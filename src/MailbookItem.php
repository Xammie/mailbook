<?php

namespace Xammie\Mailbook;

use Closure;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Support\Str;

class MailbookItem
{
    private ?MailableResolver $resolver = null;

    public function __construct(public string|Closure|Mailable $closure)
    {
    }

    public function name(): string
    {
        return Str::title(Str::snake(class_basename($this->class()), ' '));
    }

    public function subject(): string
    {
        // @phpstan-ignore-next-line
        return $this->resolver()->instance()->build()->subject ?? 'NULL';
    }

    public function content(): string
    {
        // @phpstan-ignore-next-line
        return $this->resolver()->instance()->render();
    }

    public function is(MailbookItem $target): bool
    {
        return $this->class() === $target->class();
    }

    public function resolver(): MailableResolver
    {
        return $this->resolver = $this->resolver ?? new MailableResolver($this->closure);
    }

    public function class(): string
    {
        return $this->resolver()->class();
    }
}
