<?php

namespace Xammie\Mailbook;

use Illuminate\Support\Collection;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;

class ResolvedMail
{
    public function __construct(
        private readonly Email $message
    ) {
    }

    public function subject(): ?string
    {
        return $this->message->getSubject();
    }

    public function to(): array
    {
        return collect($this->message->getTo())
            ->filter(fn (Address $address) => $address->getAddress() !== 'remove@mailbook.dev')
            ->map(fn (Address $address) => $address->toString())
            ->values()
            ->toArray();
    }

    public function replyTo(): array
    {
        return collect($this->message->getReplyTo())
            ->map(fn (Address $address) => $address->toString())
            ->toArray();
    }

    public function from(): array
    {
        return collect($this->message->getFrom())
            ->map(fn (Address $address) => $address->toString())
            ->toArray();
    }

    public function cc(): array
    {
        return collect($this->message->getCc())
            ->map(fn (Address $address) => $address->toString())
            ->toArray();
    }

    public function bcc(): array
    {
        return collect($this->message->getBcc())
            ->map(fn (Address $address) => $address->toString())
            ->toArray();
    }

    public function content(): string
    {
        return $this->message->getHtmlBody();
    }

    public function attachments(): Collection
    {
        return collect($this->message->getAttachments())
            ->map(function (DataPart $part) {
                return new Attachment($part->getName());
            });
    }
}
