<?php

use Xammie\Mailbook\Traits\HasCategory;

it('can get category', function () {
    $instance = new class
    {
        use HasCategory;
    };

    expect($instance->getCategory())->toBeNull();
});

it('can set category', function () {
    $instance = new class
    {
        use HasCategory;
    };

    $instance->category('Test category');

    expect($instance->getCategory())->toBe('Test category');
});

it('can detect category', function () {
    $instance = new class
    {
        use HasCategory;
    };

    expect($instance->hasCategory())->toBeFalse();

    $instance->category('Test category');

    expect($instance->hasCategory())->toBeTrue();
});
