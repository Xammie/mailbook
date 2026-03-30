<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitSelfCallRector;
use Rector\Set\ValueObject\LevelSetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/config',
        __DIR__.'/routes',
        __DIR__.'/src',
        __DIR__.'/tests',
    ])
    ->withRules([
        PreferPHPUnitSelfCallRector::class,
    ])
    ->withSets([
        LevelSetList::UP_TO_PHP_80,
    ])
    ->withPreparedSets(
        codeQuality: true,
        earlyReturn: true,
    );
