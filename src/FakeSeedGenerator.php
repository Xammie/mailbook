<?php

namespace Xammie\Mailbook;

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
}
