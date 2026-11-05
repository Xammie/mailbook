<?php

namespace Xammie\Mailbook\Tests\Rector\AddMailbookClosureReturnTypeRector\Fixture;

use App\Mail\VerificationMail;
use App\Models\User;
use Xammie\Mailbook\Facades\Mailbook;

Mailbook::add(function () {
    $user = User::factory()->make();

    return new VerificationMail($user, '/example/url');
});

-----
<?php

namespace Xammie\Mailbook\Tests\Rector\AddMailbookClosureReturnTypeRector\Fixture;

use App\Mail\VerificationMail;
use App\Models\User;
use Xammie\Mailbook\Facades\Mailbook;

Mailbook::add(function (): VerificationMail {
    $user = User::factory()->make();

    return new VerificationMail($user, '/example/url');
});
