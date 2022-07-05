<?php

use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Tests\Mails\TestMail;
use function Spatie\Snapshots\assertMatchesHtmlSnapshot;

it('can render', function () {
    $html = Mailbook::register(TestMail::class, fn () => new TestMail())->render();

    assertMatchesHtmlSnapshot($html);
});

it('can render multiple times', function () {
    $item = Mailbook::register(TestMail::class, fn () => new TestMail());

    expect($item->render())->toBe($item->render());
});

it('throws with invalid return type', function () {
    Mailbook::register(TestMail::class, fn () => 'invalid')
        ->getMailable();
})
    ->throws(UnexpectedValueException::class, 'Unexpected value returned from mailbook closure expected instance of Illuminate\Mail\Mailable but got string');
