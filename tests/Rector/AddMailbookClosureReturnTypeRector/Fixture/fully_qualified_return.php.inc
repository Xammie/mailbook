<?php

namespace Xammie\Mailbook\Tests\Rector\AddMailbookClosureReturnTypeRector\Fixture;

use Xammie\Mailbook\Facades\Mailbook;

Mailbook::add(function () {
    return new \App\Mail\OrderPaidMail();
});

-----
<?php

namespace Xammie\Mailbook\Tests\Rector\AddMailbookClosureReturnTypeRector\Fixture;

use Xammie\Mailbook\Facades\Mailbook;

Mailbook::add(function (): \App\Mail\OrderPaidMail {
    return new \App\Mail\OrderPaidMail();
});
