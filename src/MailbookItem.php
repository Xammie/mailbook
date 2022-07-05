<?php

namespace Xammie\Mailbook;

use Closure;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class MailbookItem
{
    private Mailable|null $mailable = null;

    public function __construct(public string $class, public Closure $closure)
    {
    }

    public function name(): string
    {
        return Str::title(Str::snake(class_basename($this->class), ' '));
    }

    public function subject(): string
    {
        // @phpstan-ignore-next-line
        return $this->getMailable()->build()->subject;
    }

    public function render(): string
    {
        return $this->getMailable()->render();
    }

    public function getMailable(): Mailable
    {
        if ($this->mailable instanceof Mailable) {
            return $this->mailable;
        }

        return $this->mailable = App::call($this->closure);
    }
}
