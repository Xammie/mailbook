<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__.'/config',
        __DIR__.'/routes',
        __DIR__.'/src',
        __DIR__.'/tests',
    ]);

    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_80,
        SetList::CODE_QUALITY,
        SetList::EARLY_RETURN,
    ]);
};
