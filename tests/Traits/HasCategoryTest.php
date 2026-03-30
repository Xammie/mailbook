<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests\Traits;

use Xammie\Mailbook\Tests\TestCase;
use Xammie\Mailbook\Traits\HasCategory;

class HasCategoryTest extends TestCase
{
    public function test_can_get_category(): void
    {
        $instance = new class
        {
            use HasCategory;
        };
        self::assertNull($instance->getCategory());
    }

    public function test_can_set_category(): void
    {
        $instance = new class
        {
            use HasCategory;
        };
        $instance->category('Test category');
        self::assertSame('Test category', $instance->getCategory());
    }

    public function test_can_detect_category(): void
    {
        $instance = new class
        {
            use HasCategory;
        };
        self::assertFalse($instance->hasCategory());
        $instance->category('Test category');
        self::assertTrue($instance->hasCategory());
    }
}
