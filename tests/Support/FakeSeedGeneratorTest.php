<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests\Support;

use Faker\Generator;
use Xammie\Mailbook\Support\FakeSeedGenerator;
use Xammie\Mailbook\Tests\Fixtures\GeneratorExpectation;
use Xammie\Mailbook\Tests\TestCase;

class FakeSeedGeneratorTest extends TestCase
{
    private FakeSeedGenerator $subject;

    private GeneratorExpectation $generator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->generator = GeneratorExpectation::factory();
        $this->app->instance(Generator::class.':en_US', $this->generator->mock);
        $this->subject = new FakeSeedGenerator;
    }

    public function test_can_get_random_seed(): void
    {
        if (! function_exists('fake')) {
            $this->markTestSkipped('Function fake() does not exist.');
        }

        $this->generator->expectsRandomNumber(123);
        $this->generator->expectsSeed(123);

        $seed = $this->subject->getCurrentSeed();

        $this->assertSame(123, $seed);
    }

    public function test_cannot_get_random_seed(): void
    {
        if (function_exists('fake')) {
            $this->markTestSkipped('Function fake() exists.');
        }

        $seed = $this->subject->getCurrentSeed();

        $this->assertNull($seed);
    }

    public function test_can_restore_seed(): void
    {
        if (! function_exists('fake')) {
            $this->markTestSkipped('Function fake() does not exist.');
        }

        $this->generator->expectsSeed('abc');

        $this->subject->restoreSeed('abc');
    }

    public function test_cannot_restore_empty_seed(): void
    {
        if (! function_exists('fake')) {
            $this->markTestSkipped('Function fake() does not exist.');
        }

        $this->expectNotToPerformAssertions();

        $this->subject->restoreSeed(null);
    }
}
