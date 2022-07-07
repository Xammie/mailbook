<?php

namespace Xammie\Mailbook;

use Closure;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use UnexpectedValueException;

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

    public function content(): string
    {
        return $this->getMailable()->render();
    }

    public function getMailable(): Mailable
    {
        if ($this->mailable instanceof Mailable) {
            return $this->mailable;
        }

        $mailable = App::call($this->closure);

        if (! $mailable instanceof Mailable) {
            throw new UnexpectedValueException(sprintf('Unexpected value returned from mailbook closure expected instance of %s but got %s', Mailable::class, gettype($mailable)));
        }

        return $this->mailable = $mailable;
    }
}
