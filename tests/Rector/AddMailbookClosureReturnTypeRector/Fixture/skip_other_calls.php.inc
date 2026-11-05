<?php

namespace Xammie\Mailbook\Tests\Rector\AddMailbookClosureReturnTypeRector\Fixture;

use App\Mail\OtherMail;
use App\Mail\VerificationMail;
use App\OtherPackage\Facades\SomeFacade;
use stdClass;
use Xammie\Mailbook\Facades\Mailbook;

SomeFacade::add(function () {
    return new VerificationMail();
});

Mailbook::label(function () {
    return new VerificationMail();
});

$object = new stdClass();

$object->add(function () {
    return new VerificationMail();
});

$object->label('some label')->add(function () {
    return new VerificationMail();
});

Mailbook::to('example@mailbook.dev')->notAMailbookMethod(function () {})
    ->add(function () {
        return new VerificationMail();
    });

Mailbook::add(VerificationMail::class)->variant('other', function () {
    return new OtherMail();
});

$className = Mailbook::class;

$className::add(function () {
    return new VerificationMail();
});
