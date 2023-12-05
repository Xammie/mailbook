<?php

use Xammie\Mailbook\FakeSeedGenerator;

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
