<?php

namespace Xammie\Mailbook\Tests\Rector\AddMailbookClosureReturnTypeRector\Fixture;

use App\Mail\VerificationMail;
use App\Models\User;
use Xammie\Mailbook\Mailbook;

Mailbook::add(function () {
    return new VerificationMail();
});

Mailbook::add(class: function () {
    return new VerificationMail();
});

Mailbook::to(User::factory()->make())->add(class: function () {
    return new VerificationMail();
});

-----
<?php

namespace Xammie\Mailbook\Tests\Rector\AddMailbookClosureReturnTypeRector\Fixture;

use App\Mail\VerificationMail;
use App\Models\User;
use Xammie\Mailbook\Mailbook;

Mailbook::add(function (): VerificationMail {
    return new VerificationMail();
});

Mailbook::add(class: function (): VerificationMail {
    return new VerificationMail();
});

Mailbook::to(User::factory()->make())->add(class: function (): VerificationMail {
    return new VerificationMail();
});
