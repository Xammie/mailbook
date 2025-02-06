<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Data;

use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;

/**
 * @internal
 */
class ResolvedMail
{
    public function __construct(
        private Email $message
    ) {}

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
        $html = $this->message->getHtmlBody();

        if (is_resource($html)) {
            $html = stream_get_contents($html) ?: '';
        }

        /** @var null|string $html */
        return $this->replaceEmbeddedAttachments($html ?? '', $this->message->getAttachments());
    }

    /**
     * Replace embedded attachments with base64 encoded images.
     *
     * @see https://github.com/Xammie/mailbook/discussions/98
     * @see https://github.com/laravel/framework/pull/48292
     */
    protected function replaceEmbeddedAttachments(string $renderedView, array $attachments): string
    {
        if (preg_match_all('/<img.+?src=[\'"]cid:([^\'"]+)[\'"].*?>/i', $renderedView, $matches)) {
            foreach (array_unique($matches[1]) as $image) {
                foreach ($attachments as $attachment) {
                    if ($attachment->getFilename() === $image) {
                        $renderedView = str_replace(
                            'cid:'.$image,
                            'data:'.$attachment->getContentType().';base64,'.$attachment->bodyToString(),
                            $renderedView
                        );

                        break;
                    }
                }
            }
        }

        return $renderedView;
    }

    public function attachments(): array
    {
        return collect($this->message->getAttachments())
            ->map(function (DataPart $part) {
                /** @phpstan-ignore function.alreadyNarrowedType */
                if (method_exists($part, 'getName')) {
                    return $part->getName();
                }

                return $part->getPreparedHeaders()->getHeaderParameter('content-type', 'name');
            })
            ->toArray();
    }
}
