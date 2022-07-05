<?php

namespace Xammie\Mailbook;

use Closure;
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

    public function register(string $class, Closure $closure): MailbookItem
    {
        $item = new MailbookItem($class, $closure);

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
