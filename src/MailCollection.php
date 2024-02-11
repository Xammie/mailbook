<?php

namespace Xammie\Mailbook;

use Illuminate\Support\Collection;
use Xammie\Mailbook\Data\MailableItem;

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
