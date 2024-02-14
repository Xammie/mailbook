<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Traits;

trait HasCategory
{
    private ?string $category = null;

    public function category(string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function hasCategory(): bool
    {
        return $this->category !== null;
    }
}
