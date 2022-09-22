<?php

namespace Xammie\Mailbook;

use Closure;
use Illuminate\Container\Container;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Support\Facades\App;
use ReflectionFunction;
use ReflectionNamedType;
use UnexpectedValueException;

class MailableResolver
{
    private ?Mailable $instance = null;

    public function __construct(public string|Closure|Mailable $mailable)
    {
    }

    public function class(): string
    {
        if ($this->mailable instanceof Mailable) {
            return get_class($this->mailable);
        }

        if (is_string($this->mailable)) {
            return $this->mailable;
        }

        $reflection = new ReflectionFunction($this->mailable);
        $reflectionType = $reflection->getReturnType();

        if ($reflectionType instanceof ReflectionNamedType) {
            $type = $reflectionType->getName();

            if (class_exists($type)) {
                return $type;
            }
        }

        $instance = App::call($this->mailable);

        if (! $instance instanceof Mailable) {
            throw new UnexpectedValueException(sprintf('Unexpected value returned from mailbook closure expected instance of %s but got %s', Mailable::class, gettype($instance)));
        }

        Container::getInstance()->call([$instance, 'build']); // @phpstan-ignore-line

        $this->mailable = $instance;

        return get_class($instance);
    }

    public function instance(): Mailable
    {
        if ($this->mailable instanceof Mailable) {
            Container::getInstance()->call([$this->mailable, 'build']); // @phpstan-ignore-line

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

        Container::getInstance()->call([$instance, 'build']); // @phpstan-ignore-line

        return $this->instance = $instance;
    }
}
