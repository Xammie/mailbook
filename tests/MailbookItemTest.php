<?php

use Illuminate\Mail\Mailable;
use Xammie\Mailbook\Exceptions\MailbookException;
use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Tests\Mails\TestBinding;
use Xammie\Mailbook\Tests\Mails\TestMail;

it('can render', function () {
    $html = Mailbook::add(TestMail::class)->content();

    expect($html)->toContain('Test mail');
});

it('can set label', function () {
    $item = Mailbook::add(TestMail::class);

    expect($item->getLabel())->toBe('Test Mail');

    $item->label('test');

    expect($item->getLabel())->toBe('test');
});

it('can render closure', function () {
    $html = Mailbook::add(fn () => new TestMail())->content();

    expect($html)->toContain('Test mail');
});

it('can get subject', function () {
    $subject = Mailbook::add(TestMail::class)->subject();

    expect($subject)->toEqual('Test email subject');
});

it('cannot get subject with container binding error', function () {
    $subject = Mailbook::add(TestBinding::class)->subject();

    expect($subject)->toEqual('');
});

it('cannot get missing subject', function () {
    $class = new class() extends Mailable
    {
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

it('can register variants', function () {
    $item = Mailbook::add(TestMail::class)
        ->variant('Test', fn () => new TestMail())
        ->variant('Another test', fn () => new TestMail());

    expect($item->getVariants())->toHaveCount(2);
});

it('cannot register duplicate variants', function () {
    Mailbook::add(TestMail::class)
        ->variant('Test', fn () => new TestMail())
        ->variant('Test', fn () => new TestMail());
})
    ->throws(MailbookException::class, 'Variant Test (test) already exists');

it('throws with invalid return type', function () {
    Mailbook::add(fn () => 'invalid')->variantResolver()->instance();
})
    ->throws(UnexpectedValueException::class, 'Unexpected value returned from mailbook closure expected instance of Illuminate\Contracts\Mail\Mailable but got string');
