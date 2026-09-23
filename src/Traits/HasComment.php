<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Traits;

trait HasComment
{
    private ?string $comment = null;

    public function comment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function hasComment(): bool
    {
        return $this->comment !== null;
    }
}
