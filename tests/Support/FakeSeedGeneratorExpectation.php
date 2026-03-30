<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests\Support;

use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Assert;
use Xammie\Mailbook\Support\FakeSeedGenerator;

class FakeSeedGeneratorExpectation
{
    private function __construct(
        public FakeSeedGenerator&MockInterface $mock,
    ) {}

    public static function factory(): self
    {
        return new self(Mockery::mock(FakeSeedGenerator::class));
    }

    public function expectsRestoreSeed(int|string|null $expectedSeed): void
    {
        $this->mock
            ->expects('restoreSeed')
            ->with(Assert::equalTo($expectedSeed))
            ->once();
    }

    public function expectsGetCurrentSeed(?int $return): void
    {
        $this->mock
            ->expects('getCurrentSeed')
            ->withNoArgs()
            ->once()
            ->andReturn($return);
    }
}
