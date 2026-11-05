<?php

namespace Xammie\Mailbook\Tests\Rector\AddMailbookClosureReturnTypeRector\Fixture;

use App\Mail\OtherMail;
use App\Mail\VerificationMail;
use Xammie\Mailbook\Facades\Mailbook;

Mailbook::add(function () {
    $formatter = function () {
        return new OtherMail();
    };

    $factory = fn () => new OtherMail();

    return new VerificationMail($formatter, $factory);
});

-----
<?php

namespace Xammie\Mailbook\Tests\Rector\AddMailbookClosureReturnTypeRector\Fixture;

use App\Mail\OtherMail;
use App\Mail\VerificationMail;
use Xammie\Mailbook\Facades\Mailbook;

Mailbook::add(function (): VerificationMail {
    $formatter = function () {
        return new OtherMail();
    };

    $factory = fn () => new OtherMail();

    return new VerificationMail($formatter, $factory);
});
