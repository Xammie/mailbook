<?php

namespace Xammie\Mailbook;

use Closure;
use Illuminate\Container\Container;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Support\Facades\App;
use ReflectionFunction;
use ReflectionNamedType;
use UnexpectedValueException;
use Xammie\Mailbook\Facades\Mailbook as MailbookFacade;

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

        if ($this->instance instanceof Mailable) {
            return get_class($this->instance);
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

        $this->instance = $this->build($instance);

        return get_class($this->instance);
    }

    public function instance(): Mailable
    {
        if ($this->mailable instanceof Mailable) {
            return $this->build($this->mailable);
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

        return $this->instance = $this->build($instance);
    }

    protected function build(Mailable $instance): Mailable
    {
        MailbookFacade::withCurrentLocale(function () use ($instance) {
            Container::getInstance()->call([$instance, 'build']); // @phpstan-ignore-line
        });

        return $instance;
    }
}
