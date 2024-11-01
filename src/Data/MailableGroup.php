<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Data;

use Illuminate\Support\Collection;

/**
 * @internal
 */
class MailableGroup
{
    /**
     * @param  Collection<int, MailableItem>  $items
     */
    public function __construct(
        public string $label,
        public Collection $items
    ) {}
}
