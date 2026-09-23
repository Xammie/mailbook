<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests\Support;

use Mockery;
use Mockery\MockInterface;
use Xammie\Mailbook\Support\ConfigInjector;

class ConfigInjectorExpectation
{
    private function __construct(
        public ConfigInjector&MockInterface $mock,
    ) {}

    public static function factory(): self
    {
        return new self(Mockery::mock(ConfigInjector::class));
    }

    public function expectsSet(string $key, mixed $value): void
    {
        $this->mock
            ->expects('set')
            ->with($key, $value)
            ->once()
            ->andReturnSelf();
    }

    public function expectsRevert(): void
    {
        $this->mock
            ->expects('revert')
            ->withNoArgs()
            ->once()
            ->andReturnSelf();
    }
}
