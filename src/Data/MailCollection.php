<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Data;

use Illuminate\Support\Collection;

class MailCollection
{
    /**
     * @var Collection<int, MailableItem>
     */
    protected Collection $mails;

    public function __construct()
    {
        $this->mails = collect();
    }

    public function push(MailableItem $mail): void
    {
        $this->mails->push($mail);
    }

    /**
     * @return Collection<int, MailableItem>
     */
    public function all(): Collection
    {
        return $this->mails;
    }
}
