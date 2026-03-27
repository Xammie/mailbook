<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests\Support;

use Faker\Generator;
use Mockery\MockInterface;
use Xammie\Mailbook\Support\FakeSeedGenerator;
use Xammie\Mailbook\Tests\TestCase;

class FakeSeedGeneratorTest extends TestCase
{
    public function test_can_get_random_seed(): void
    {
        if (! function_exists('fake')) {
            $this->markTestSkipped('Function fake() does not exist.');
        }
        $mock = $this->mock(Generator::class, function (MockInterface $mock): void {
            $mock->shouldReceive('randomNumber')->once()->andReturn(123);
            $mock->shouldReceive('seed')->with(123)->once();
        });
        $this->app->bind(Generator::class.':en_US', fn () => $mock);

        $generator = new FakeSeedGenerator;
        $seed = $generator->getCurrentSeed();
        $this->assertSame(123, $seed);
    }

    public function test_cannot_get_random_seed(): void
    {
        if (function_exists('fake')) {
            $this->markTestSkipped('Function fake() exists.');
        }
        $generator = new FakeSeedGenerator;
        $seed = $generator->getCurrentSeed();
        $this->assertNull($seed);
    }

    public function test_can_restore_seed(): void
    {
        if (! function_exists('fake')) {
            $this->markTestSkipped('Function fake() does not exist.');
        }
        $mock = $this->mock(Generator::class, function (MockInterface $mock): void {
            $mock->shouldReceive('seed')->with('abc')->once();
        });
        $this->app->bind(Generator::class.':en_US', fn () => $mock);

        $generator = new FakeSeedGenerator;
        $generator->restoreSeed('abc');
        $this->assertTrue(true); // Ensure test does not fail on void
    }

    public function test_cannot_restore_empty_seed(): void
    {
        if (! function_exists('fake')) {
            $this->markTestSkipped('Function fake() does not exist.');
        }
        $mock = $this->mock(Generator::class, function (MockInterface $mock): void {
            $mock->shouldReceive('seed')->never();
        });
        $this->app->bind(Generator::class.':en_US', fn () => $mock);

        $generator = new FakeSeedGenerator;
        $generator->restoreSeed(null);
        $this->assertTrue(true); // Ensure test does not fail on void
    }
}
