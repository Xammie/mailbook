<?php

namespace Xammie\Mailbook;

use Closure;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Support\Collection;

class Mailbook
{
    /**
     * @var Collection<int, MailbookItem>
     */
    protected Collection $mailables;

    public function __construct()
    {
        $this->mailables = collect(); // @phpstan-ignore-line
    }

    public function add(string|Closure|Mailable $class): MailbookItem
    {
        $item = new MailbookItem($class);

        $this->mailables->push($item);

        return $item;
    }

    /**
     * @return Collection<int, MailbookItem>
     */
    public function mailables(): Collection
    {
        return $this->mailables;
    }
}
