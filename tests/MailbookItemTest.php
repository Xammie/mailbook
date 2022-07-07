<?php

use function Spatie\Snapshots\assertMatchesHtmlSnapshot;
use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Tests\Mails\TestMail;

it('can render', function () {
    $html = Mailbook::register(TestMail::class, fn () => new TestMail())->content();

    assertMatchesHtmlSnapshot($html);
});

it('can render multiple times', function () {
    $item = Mailbook::register(TestMail::class, fn () => new TestMail());

    expect($item->content())->toBe($item->content());
});

it('throws with invalid return type', function () {
    Mailbook::register(TestMail::class, fn () => 'invalid')
        ->getMailable();
})
    ->throws(UnexpectedValueException::class, 'Unexpected value returned from mailbook closure expected instance of Illuminate\Mail\Mailable but got string');

it('can inject dependencies', function () {
    Mailbook::register(TestMail::class, function (TestMail $testMail) {
        expect($testMail)->toBeInstanceOf(TestMail::class);

        return $testMail;
    })
        ->getMailable();
});
