<?php

namespace Xammie\Mailbook;

use RuntimeException;

/**
 * @internal
 */
class MailbookConfig
{
    public function getSendToStrict(): string
    {
        $to = config('mailbook.send_to');

        if (is_array($to)) {
            $to = $to[0];
        }

        if (! is_string($to)) {
            throw new RuntimeException('invalid config mailbook.send_to should be string');
        }

        if ($to === '' || $to === '0') {
            throw new RuntimeException('invalid config mailbook.send_to should not be empty');
        }

        return $to;
    }

    public function getSendTo(): ?string
    {
        try {
            return $this->getSendToStrict();
        } catch (RuntimeException) {
            return null;
        }
    }
}
