<?php

namespace Xammie\Mailbook\Tests\Rector\AddMailbookClosureReturnTypeRector\Fixture;

use App\Mail\VerificationMail;
use Xammie\Mailbook\Facades\Mailbook;

Mailbook::add(function () {
    return new class () extends VerificationMail {};
});

Mailbook::add(function (string $class) {
    return new $class();
});
