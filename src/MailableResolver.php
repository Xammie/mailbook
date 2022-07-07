<?php

namespace Xammie\Mailbook;

use Closure;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Support\Facades\App;
use ReflectionFunction;
use UnexpectedValueException;

class MailableResolver
{
    private ?Mailable $instance = null;

    public function __construct(public string|Closure|Mailable $mailable)
    {
    }

    /**
     * @return class-string<Mailable>
     */
    public function class(): string
    {
        if ($this->mailable instanceof Mailable) {
            return get_class($this->mailable);
        }

        if (is_string($this->mailable)) {
            return $this->mailable;
        }

        $reflection = new ReflectionFunction($this->mailable);
        $type = $reflection->getReturnType()?->getName();

        if (class_exists($type)) {
            return $type;
        }

        return get_class(App::call($this->mailable));
    }

    public function instance(): Mailable
    {
        if ($this->mailable instanceof Mailable) {
            return $this->mailable;
        }

        if ($this->instance instanceof Mailable) {
            return $this->instance;
        }

        if (is_callable($this->mailable)) {
            $instance = App::call($this->mailable);
        } else {
            $instance = app($this->mailable);
        }

        if (! $instance instanceof Mailable) {
            throw new UnexpectedValueException(sprintf('Unexpected value returned from mailbook closure expected instance of %s but got %s', Mailable::class, gettype($instance)));
        }

        return $this->instance = $instance;
    }
}
