<?php

namespace Xammie\Mailbook\Tests\Rector\AddMailbookClosureReturnTypeRector\Fixture;

use App\Mail\VerificationMail;
use Xammie\Mailbook\Facades\Mailbook;

Mailbook::add(function (): VerificationMail {
    return new VerificationMail();
});

Mailbook::add(fn (): VerificationMail => new VerificationMail());
