<?php

use Xammie\Mailbook\Support\FakeSeedGenerator;

it('can get random seed', function () {
    $generator = new FakeSeedGenerator();

    $seed = $generator->getCurrentSeed();

    expect($seed)->toBeInt();
})
    ->skip(! function_exists('fake'));

it('cannot get random seed', function () {
    $generator = new FakeSeedGenerator();

    $seed = $generator->getCurrentSeed();

    expect($seed)->toBeNull();
})
    ->skip(function_exists('fake'));

it('can restore seed', function () {
    fake()->seed('123');

    $generator = new FakeSeedGenerator();

    $generator->restoreSeed('abc');

    expect(fake()->randomElement([1, 2, 3]))->toBe(3);
})
    ->skip(! function_exists('fake'));

it('cannot restore empty seed', function () {
    fake()->seed('123');

    $generator = new FakeSeedGenerator();

    $generator->restoreSeed(null);

    expect(fake()->randomElement([1, 2, 3]))->toBe(2);
})
    ->skip(! function_exists('fake'));
