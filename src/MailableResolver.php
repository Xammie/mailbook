<?php

namespace Xammie\Mailbook;

use Closure;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\App;
use ReflectionFunction;
use ReflectionNamedType;
use UnexpectedValueException;

class MailableResolver
{
    private Mailable|Notification|null $instance = null;

    private ?ResolvedMail $resolved = null;

    public function __construct(public string|Closure|Mailable|Notification $subject)
    {
    }

    public function className(): string
    {
        if ($this->subject instanceof Mailable || $this->subject instanceof Notification) {
            return $this->subject::class;
        }

        if ($this->instance instanceof Mailable) {
            return $this->instance::class;
        }

        if (is_string($this->subject)) {
            return $this->subject;
        }

        $reflection = new ReflectionFunction($this->subject);
        $reflectionType = $reflection->getReturnType();

        if ($reflectionType instanceof ReflectionNamedType) {
            $type = $reflectionType->getName();

            if (class_exists($type)) {
                return $type;
            }
        }

        $instance = App::call($this->subject);

        if (! $instance instanceof Mailable && ! $instance instanceof Notification) {
            throw new UnexpectedValueException(sprintf('Unexpected value returned from mailbook closure expected instance of %s but got %s', Mailable::class, gettype($instance)));
        }

        $this->instance = $instance;

        return $this->instance::class;
    }

    public function instance(): Mailable|Notification
    {
        if ($this->subject instanceof Mailable || $this->subject instanceof Notification) {
            return $this->subject;
        }

        if ($this->instance instanceof Mailable) {
            return $this->instance;
        }

        $instance = is_callable($this->subject) ? App::call($this->subject) : app($this->subject);

        if (! $instance instanceof Mailable && ! $instance instanceof Notification) {
            throw new UnexpectedValueException(sprintf('Unexpected value returned from mailbook closure expected instance of %s but got %s', Mailable::class, gettype($instance)));
        }

        return $this->instance = $instance;
    }

    public function resolve(): ResolvedMail
    {
        if ($this->resolved instanceof ResolvedMail) {
            return $this->resolved;
        }

        $sender = new MailableSender($this->instance());

        return $this->resolved = $sender->collect();
    }
}
