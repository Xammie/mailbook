<?php

use Xammie\Mailbook\MailbookTransport;

it('has correct transport name', function () {
    $transport = new MailbookTransport();

    expect($transport->__toString())->toBe('mailbook');
});
