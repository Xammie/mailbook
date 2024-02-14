<?php

namespace Xammie\Mailbook\Support;

use Illuminate\Support\Facades\Config;

/**
 * @internal
 */
class ConfigInjector
{
    private array $replaced = [];

    public function set(string $key, mixed $value): self
    {
        $this->replaced[$key] = Config::get($key);

        Config::set($key, $value);

        return $this;
    }

    public function revert(): void
    {
        foreach ($this->replaced as $key => $value) {
            Config::set($key, $value);
        }

        $this->replaced = [];
    }
}
