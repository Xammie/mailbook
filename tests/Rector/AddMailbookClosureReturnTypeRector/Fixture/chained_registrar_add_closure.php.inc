<?php

namespace Xammie\Mailbook\Tests\Rector\AddMailbookClosureReturnTypeRector\Fixture;

use App\Mail\VerificationMail;
use Xammie\Mailbook\Facades\Mailbook;

Mailbook::label('Verification')->add(function () {
    return new VerificationMail();
});

Mailbook::to('example@mailbook.dev')->add(function () {
    return new VerificationMail();
});

Mailbook::category('Account')->comment('Sent after signup')->add(function () {
    return new VerificationMail();
});

Mailbook::category('Account')->group(function () {
    Mailbook::add(function () {
        return new VerificationMail();
    });
});

-----
<?php

namespace Xammie\Mailbook\Tests\Rector\AddMailbookClosureReturnTypeRector\Fixture;

use App\Mail\VerificationMail;
use Xammie\Mailbook\Facades\Mailbook;

Mailbook::label('Verification')->add(function (): VerificationMail {
    return new VerificationMail();
});

Mailbook::to('example@mailbook.dev')->add(function (): VerificationMail {
    return new VerificationMail();
});

Mailbook::category('Account')->comment('Sent after signup')->add(function (): VerificationMail {
    return new VerificationMail();
});

Mailbook::category('Account')->group(function () {
    Mailbook::add(function (): VerificationMail {
        return new VerificationMail();
    });
});
