<?php

namespace Xammie\Mailbook\Tests\Rector\AddMailbookClosureReturnTypeRector\Fixture;

use App\Mail\OtherMail;
use App\Mail\VerificationMail;
use Xammie\Mailbook\Facades\Mailbook;

Mailbook::add(function () {
    $mail = new VerificationMail();

    return $mail;
});

Mailbook::add(function (bool $paid) {
    if ($paid) {
        return;
    }

    return new VerificationMail();
});

Mailbook::add(function (bool $paid) {
    if ($paid) {
        return new VerificationMail();
    }

    return new OtherMail();
});

Mailbook::add(function () {
    new VerificationMail();
});

Mailbook::add(fn () => 'not a new statement');

Mailbook::add(function () {
    return new self();
});

Mailbook::add(function () {
    return new static();
});

Mailbook::add(function () {
    return new Self();
});

Mailbook::add(function (string $status) {
    return match ($status) {
        'paid' => new VerificationMail(),
        default => new OtherMail(),
    };
});

Mailbook::add('App\Mail\VerificationMail');

Mailbook::add(VerificationMail::class);

Mailbook::add();
