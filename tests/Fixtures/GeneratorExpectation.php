<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests\Fixtures;

use Faker\Generator;
use Mockery;
use Mockery\MockInterface;

class GeneratorExpectation
{
    private function __construct(
        public Generator&MockInterface $mock,
    ) {}

    public static function factory(): self
    {
        return new self(Mockery::mock(Generator::class));
    }

    public function expectsRandomNumber(int $return): void
    {
        $this->mock
            ->expects('randomNumber')
            ->withNoArgs()
            ->once()
            ->andReturn($return);
    }

    public function expectsSeed(int|string $expects): void
    {
        $this->mock
            ->expects('seed')
            ->with($expects)
            ->once();
    }
}
