<?php

use Illuminate\Mail\Mailable;
use function Spatie\Snapshots\assertMatchesHtmlSnapshot;
use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Tests\Mails\TestMail;

it('can render', function () {
    $html = Mailbook::add(TestMail::class)->content();

    assertMatchesHtmlSnapshot($html);
});

it('can render closure', function () {
    $html = Mailbook::add(fn () => new TestMail())->content();

    assertMatchesHtmlSnapshot($html);
});

it('can get subject', function () {
    $subject = Mailbook::add(TestMail::class)->subject();

    expect($subject)->toEqual('Test email subject');
});

it('cannot get missing subject', function () {
    $class = new class () extends Mailable {
        public function build(): self
        {
            return $this->markdown('mailbook::test-email');
        }
    };

    $subject = Mailbook::add(fn () => $class)->subject();

    expect($subject)->toEqual('NULL');
});

it('can render multiple times', function () {
    $item = Mailbook::add(fn () => new TestMail());

    expect($item->content())->toBe($item->content());
});

it('throws with invalid return type', function () {
    Mailbook::add(fn () => 'invalid')->resolver()->instance();
})
    ->throws(UnexpectedValueException::class, 'Unexpected value returned from mailbook closure expected instance of Illuminate\Contracts\Mail\Mailable but got string');
