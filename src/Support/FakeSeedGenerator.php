<?php

namespace Xammie\Mailbook\Support;

/**
 * @internal
 */
class FakeSeedGenerator
{
    public function getCurrentSeed(): ?int
    {
        if (! function_exists('fake')) {
            return null;
        }

        $seed = fake()->randomNumber();
        fake()->seed($seed);

        return $seed;
    }

    public function restoreSeed(int|string|null $seed): void
    {
        if ($seed === null) {
            return;
        }

        if (! function_exists('fake')) {
            return;
        }

        fake()->seed($seed);
    }
}
