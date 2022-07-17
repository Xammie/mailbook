<?php

namespace Xammie\Mailbook;

use Closure;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Support\Collection;

class Mailbook
{
    /**
     * @var Collection<int, MailableItem>
     */
    protected Collection $mailables;

    public function __construct()
    {
        $this->mailables = collect(); // @phpstan-ignore-line
    }

    public function add(string|Closure|Mailable $class): MailableItem
    {
        $item = new MailableItem($class);

        $this->mailables->push($item);

        return $item;
    }

    /**
     * @return Collection<int, MailableItem>
     */
    public function mailables(): Collection
    {
        return $this->mailables;
    }
}
