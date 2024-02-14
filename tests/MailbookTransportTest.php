<?php

declare(strict_types=1);

use Xammie\Mailbook\MailbookTransport;

it('has correct transport name', function (): void {
    $transport = new MailbookTransport();

    expect($transport->__toString())->toBe('mailbook');
});
