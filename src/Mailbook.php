<?php

namespace Xammie\Mailbook;

use Closure;
use Illuminate\Support\Collection;

class Mailbook
{
    protected Collection $mailables;

    public function __construct()
    {
        $this->mailables = collect();
    }

    public function register(string $class, Closure $closure): Mailable
    {
        $mailable = new Mailable($class, $closure);

        $this->mailables->push($mailable);

        return $mailable;
    }

    /**
     * @return Collection<int, Mailable>
     */
    public function mailables(): Collection
    {
        return $this->mailables;
    }
}
