<?php

namespace Xammie\Mailbook\Tests\Rector\AddMailbookClosureReturnTypeRector\Fixture;

use App\Mail\OrderShippedMail;
use Xammie\Mailbook\Facades\Mailbook;

Mailbook::add(function (bool $shipped) {
    if ($shipped) {
        return new OrderShippedMail();
    }

    return new OrderShippedMail();
});

Mailbook::add(function () {
    foreach ([1, 2] as $item) {
        if ($item > 1) {
            return new OrderShippedMail();
        }
    }

    return new OrderShippedMail();
});

Mailbook::add(function (string $status) {
    try {
        return new OrderShippedMail();
    } catch (\Throwable) {
        return new OrderShippedMail();
    }
});

-----
<?php

namespace Xammie\Mailbook\Tests\Rector\AddMailbookClosureReturnTypeRector\Fixture;

use App\Mail\OrderShippedMail;
use Xammie\Mailbook\Facades\Mailbook;

Mailbook::add(function (bool $shipped): OrderShippedMail {
    if ($shipped) {
        return new OrderShippedMail();
    }

    return new OrderShippedMail();
});

Mailbook::add(function (): OrderShippedMail {
    foreach ([1, 2] as $item) {
        if ($item > 1) {
            return new OrderShippedMail();
        }
    }

    return new OrderShippedMail();
});

Mailbook::add(function (string $status): OrderShippedMail {
    try {
        return new OrderShippedMail();
    } catch (\Throwable) {
        return new OrderShippedMail();
    }
});
