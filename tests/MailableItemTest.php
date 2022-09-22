<?php

use Illuminate\Mail\Mailable;
use Xammie\Mailbook\Attachment;
use Xammie\Mailbook\Exceptions\MailbookException;
use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Tests\Mails\OtherMail;
use Xammie\Mailbook\Tests\Mails\TestBinding;
use Xammie\Mailbook\Tests\Mails\TestMail;
use Xammie\Mailbook\Tests\Mails\WithAttachmentsMail;

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

it('is equal', function () {
    $item = Mailbook::add(TestMail::class);
    $other = Mailbook::mailables()->first();

    expect($item->is($other))->toBeTrue();
});

it('will resolve once', function () {
    $item = Mailbook::add(TestMail::class);

    expect($item->resolver())->toBe($item->resolver());
});

it('can get variant resolver without variants', function () {
    $item = Mailbook::add(TestMail::class);

    expect($item->variantResolver()->class())->toEqual(TestMail::class);
});

it('can get variant resolver from default variant', function () {
    $item = Mailbook::add(TestMail::class)
        ->variant('Other one', fn () => new OtherMail());

    expect($item->variantResolver()->class())->toEqual(OtherMail::class);
});

it('can get default from', function () {
    $item = Mailbook::add(TestMail::class);

    expect($item->from())->toBe(['Example <hello@example.com>']);

    config()->set('mail.from', null);

    expect($item->from())->toBe([]);
});

it('can get from', function () {
    $item = Mailbook::add(OtherMail::class);

    expect($item->from())->toBe(['Harry Potter <harry@example.com>']);
});

it('can get reply to', function () {
    $item = Mailbook::add(OtherMail::class);

    expect($item->replyTo())->toBe(['Support <questions@example.com>']);
});

it('can get to', function () {
    $item = Mailbook::add(OtherMail::class);

    expect($item->to())->toBe(['Mailbook <example@mailbook.dev>']);
});

it('can get cc', function () {
    $item = Mailbook::add(OtherMail::class);

    expect($item->cc())->toBe(['Mailbook <cc@mailbook.dev>']);
});

it('can get bcc', function () {
    $item = Mailbook::add(OtherMail::class);

    expect($item->bcc())->toBe(['Mailbook <bcc@mailbook.dev>']);
});

it('can get size', function () {
    $item = Mailbook::add(OtherMail::class);

    expect($item->size())->toBe('20 B');
});

it('builds mailable resolved from instance', function () {
    $item = Mailbook::add(new OtherMail());

    expect($item->subject())->toBe('Hello!');
});

it('can get attachments', function () {
    $item = Mailbook::add(WithAttachmentsMail::class);

    expect($item->attachments()->toArray())->toEqual([
        new Attachment('document.pdf'),
        new Attachment('WithAttachmentsMail.php'),
    ]);
});
