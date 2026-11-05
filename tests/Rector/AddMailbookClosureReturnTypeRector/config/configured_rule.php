<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Xammie\Mailbook\Rector\AddMailbookClosureReturnTypeRector;

return RectorConfig::configure()
    ->withRules([AddMailbookClosureReturnTypeRector::class]);
