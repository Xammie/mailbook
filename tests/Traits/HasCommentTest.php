<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests\Traits;

use Xammie\Mailbook\Tests\TestCase;
use Xammie\Mailbook\Traits\HasComment;

class HasCommentTest extends TestCase
{
    public function test_can_get_comment(): void
    {
        $instance = new class
        {
            use HasComment;
        };
        self::assertNull($instance->getComment());
    }

    public function test_can_set_comment(): void
    {
        $instance = new class
        {
            use HasComment;
        };
        $instance->comment('Test comment');
        self::assertSame('Test comment', $instance->getComment());
    }

    public function test_can_detect_comment(): void
    {
        $instance = new class
        {
            use HasComment;
        };
        self::assertFalse($instance->hasComment());
        $instance->comment('Test comment');
        self::assertTrue($instance->hasComment());
    }
}
