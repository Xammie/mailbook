<?php

namespace Xammie\Mailbook\Tests\Rector\AddMailbookClosureReturnTypeRector\Fixture;

use App\Mail\VerificationMail;
use Xammie\Mailbook\Facades\Mailbook;

Mailbook::add(fn () => new VerificationMail());

Mailbook::to('example@mailbook.dev')->add(fn () => new VerificationMail());

Mailbook::add(class: fn () => new VerificationMail());

-----
<?php

namespace Xammie\Mailbook\Tests\Rector\AddMailbookClosureReturnTypeRector\Fixture;

use App\Mail\VerificationMail;
use Xammie\Mailbook\Facades\Mailbook;

Mailbook::add(fn (): VerificationMail => new VerificationMail());

Mailbook::to('example@mailbook.dev')->add(fn (): VerificationMail => new VerificationMail());

Mailbook::add(class: fn (): VerificationMail => new VerificationMail());
