<?php

namespace Xammie\Mailbook\Traits;

use Illuminate\Support\Str;

trait HasLabel
{
    private ?string $label = null;

    public function label(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getLabel(): string
    {
        if (! is_null($this->label)) {
            return $this->label;
        }

        return Str::title(Str::snake(class_basename($this->resolver()->className()), ' '));
    }

    public function hasLabel(): bool
    {
        return $this->label !== null;
    }
}
