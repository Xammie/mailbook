<?php

namespace Xammie\Mailbook\Tests\Rector\AddMailbookClosureReturnTypeRector\Fixture;

use App\Mail\VerificationMail;
use Xammie\Mailbook\Mailbook;

$mailbook = new Mailbook();

$mailbook->add(function () {
    return new VerificationMail();
});

-----
<?php

namespace Xammie\Mailbook\Tests\Rector\AddMailbookClosureReturnTypeRector\Fixture;

use App\Mail\VerificationMail;
use Xammie\Mailbook\Mailbook;

$mailbook = new Mailbook();

$mailbook->add(function (): VerificationMail {
    return new VerificationMail();
});
