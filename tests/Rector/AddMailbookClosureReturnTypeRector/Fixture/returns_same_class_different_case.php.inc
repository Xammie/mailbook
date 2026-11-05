<?php

namespace Xammie\Mailbook\Tests\Rector\AddMailbookClosureReturnTypeRector\Fixture;

use App\Mail\VerificationMail;
use Xammie\Mailbook\Facades\Mailbook;

Mailbook::add(function (bool $paid) {
    if ($paid) {
        return new VerificationMail();
    }

    return new \App\Mail\VERIFICATIONMAIL();
});

-----
<?php

namespace Xammie\Mailbook\Tests\Rector\AddMailbookClosureReturnTypeRector\Fixture;

use App\Mail\VerificationMail;
use Xammie\Mailbook\Facades\Mailbook;

Mailbook::add(function (bool $paid): VerificationMail {
    if ($paid) {
        return new VerificationMail();
    }

    return new \App\Mail\VERIFICATIONMAIL();
});
