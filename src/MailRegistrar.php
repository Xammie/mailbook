<?php

namespace Xammie\Mailbook;

use Closure;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Notifications\Notification;
use Xammie\Mailbook\Facades\Mailbook;

class MailRegistrar
{
    protected ?string $label = null;

    protected mixed $notifiable = null;

    public function __construct(protected MailCollection $collection)
    {
    }

    public static function make(MailCollection $collection): self
    {
        return new self($collection);
    }

    public function label(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function to(mixed $notifiable): self
    {
        $this->notifiable = $notifiable;

        return $this;
    }

    public function add(string|Closure|Mailable|Notification $class): MailableItem
    {
        $item = new MailableItem($class, $this->notifiable);

        if ($this->label) {
            $item->label($this->label);
        }

        $this->collection->push($item);

        return $item;
    }

    public function group(Closure $closure): self
    {
        Mailbook::setRegistrar($this);

        $closure();

        Mailbook::clearRegistrar();

        return $this;
    }
}
