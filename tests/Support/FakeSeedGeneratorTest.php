<?php

declare(strict_types=1);

use Faker\Generator;
use Mockery\MockInterface;
use Xammie\Mailbook\Support\FakeSeedGenerator;

it('can get random seed', function (): void {
    $mock = $this->mock(Generator::class, function (MockInterface $mock): void {
        $mock->shouldReceive('randomNumber')->once()->andReturn(123);
        $mock->shouldReceive('seed')->with(123)->once();
    });
    $this->app->bind(Generator::class.':en_US', fn () => $mock);

    $generator = new FakeSeedGenerator();

    $seed = $generator->getCurrentSeed();

    expect($seed)->toBe(123);
})
    ->skip(! function_exists('fake'));

it('cannot get random seed', function (): void {
    $generator = new FakeSeedGenerator();

    $seed = $generator->getCurrentSeed();

    expect($seed)->toBeNull();
})
    ->skip(function_exists('fake'));

it('can restore seed', function (): void {
    $mock = $this->mock(Generator::class, function (MockInterface $mock): void {
        $mock->shouldReceive('seed')->with('abc')->once();
    });
    $this->app->bind(Generator::class.':en_US', fn () => $mock);

    $generator = new FakeSeedGenerator();

    $generator->restoreSeed('abc');
})
    ->skip(! function_exists('fake'));

it('cannot restore empty seed', function (): void {
    $mock = $this->mock(Generator::class, function (MockInterface $mock): void {
        $mock->shouldReceive('seed')->never();
    });
    $this->app->bind(Generator::class.':en_US', fn () => $mock);

    $generator = new FakeSeedGenerator();

    $generator->restoreSeed(null);
})
    ->skip(! function_exists('fake'));
