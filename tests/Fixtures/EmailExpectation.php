<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests\Fixtures;

use Mockery;
use Mockery\MockInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;

class EmailExpectation
{
    private function __construct(
        public Email&MockInterface $mock,
    ) {}

    public static function factory(): self
    {
        return new self(Mockery::mock(Email::class));
    }

    public function expectsGetSubject(string $return): void
    {
        $this->mock
            ->expects('getSubject')
            ->withNoArgs()
            ->once()
            ->andReturn($return);
    }

    /**
     * @param  Address[]  $return
     */
    public function expectsGetTo(array $return): void
    {
        $this->mock
            ->expects('getTo')
            ->withNoArgs()
            ->once()
            ->andReturn($return);
    }

    /**
     * @param  Address[]  $return
     */
    public function expectsGetReplyTo(array $return): void
    {
        $this->mock
            ->expects('getReplyTo')
            ->withNoArgs()
            ->once()
            ->andReturn($return);
    }

    /**
     * @param  Address[]  $return
     */
    public function expectsGetFrom(array $return): void
    {
        $this->mock
            ->expects('getFrom')
            ->withNoArgs()
            ->once()
            ->andReturn($return);
    }

    /**
     * @param  Address[]  $return
     */
    public function expectsGetCc(array $return): void
    {
        $this->mock
            ->expects('getCc')
            ->withNoArgs()
            ->once()
            ->andReturn($return);
    }

    /**
     * @param  Address[]  $return
     */
    public function expectsGetBcc(array $return): void
    {
        $this->mock
            ->expects('getBcc')
            ->withNoArgs()
            ->once()
            ->andReturn($return);
    }

    /**
     * @param  string|resource|null  $return
     */
    public function expectsGetHtmlBody(mixed $return): void
    {
        $this->mock
            ->expects('getHtmlBody')
            ->withNoArgs()
            ->once()
            ->andReturn($return);
    }

    /**
     * @param  DataPart[]  $return
     */
    public function expectsGetAttachments(array $return): void
    {
        $this->mock
            ->expects('getAttachments')
            ->withNoArgs()
            ->once()
            ->andReturn($return);
    }
}
