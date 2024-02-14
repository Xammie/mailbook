<?php

declare(strict_types=1);

use Xammie\Mailbook\Exceptions\MailbookException;
use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Tests\Fixtures\Mails\NotificationMail;
use Xammie\Mailbook\Tests\Fixtures\Mails\OtherMail;
use Xammie\Mailbook\Tests\Fixtures\Mails\TestBinding;
use Xammie\Mailbook\Tests\Fixtures\Mails\TestMail;
use Xammie\Mailbook\Tests\Fixtures\Mails\WithAttachmentsMail;

it('can render', function (): void {
    $html = Mailbook::add(TestMail::class)->content();

    expect($html)->toContain('Test mail');
});

it('can set label', function (): void {
    $item = Mailbook::add(TestMail::class);

    expect($item->getLabel())->toBe('Test Mail');

    $item->label('test');

    expect($item->getLabel())->toBe('test');
});

it('can render closure', function (): void {
    $html = Mailbook::add(fn () => new TestMail())->content();

    expect($html)->toContain('Test mail');
});

it('can get subject', function (): void {
    $subject = Mailbook::add(TestMail::class)->subject();

    expect($subject)->toBe('Test email subject');
});

it('cannot get subject with container binding error', function (): void {
    $subject = Mailbook::add(TestBinding::class)->subject();

    expect($subject)->toBeNull();
});

it('cannot get missing subject', function (): void {
    $subject = Mailbook::add(NotificationMail::class)->subject();

    expect($subject)->toBe('Notification Mail');
});

it('can render multiple times', function (): void {
    $item = Mailbook::add(fn () => new TestMail());

    expect($item->content())->toBe($item->content());
});

it('can register variants', function (): void {
    $item = Mailbook::add(TestMail::class)
        ->variant('Test', fn () => new TestMail())
        ->variant('Another test', fn () => new TestMail());

    expect($item->getVariants())->toHaveCount(2);
});

it('cannot register duplicate variants', function (): void {
    Mailbook::add(TestMail::class)
        ->variant('Test', fn () => new TestMail())
        ->variant('Test', fn () => new TestMail());
})
    ->throws(MailbookException::class, 'Variant Test (test) already exists');

it('throws with invalid return type', function (): void {
    Mailbook::add(fn () => 'invalid')->variantResolver()->instance();
})
    ->throws(UnexpectedValueException::class, 'Unexpected value returned from mailbook closure expected instance of Illuminate\Contracts\Mail\Mailable but got string');

it('is equal', function (): void {
    $item = Mailbook::add(TestMail::class);
    $other = Mailbook::mailables()->first();

    expect($item->is($other))->toBeTrue();
});

it('will resolve once', function (): void {
    $item = Mailbook::add(TestMail::class);

    expect($item->resolver())->toBe($item->resolver());
});

it('can get variant resolver without variants', function (): void {
    $item = Mailbook::add(TestMail::class);

    expect($item->variantResolver()->className())->toEqual(TestMail::class);
});

it('can get variant resolver from default variant', function (): void {
    $item = Mailbook::add(TestMail::class)
        ->variant('Other one', fn () => new OtherMail());

    expect($item->variantResolver()->className())->toEqual(OtherMail::class);
});

it('can get default from', function (): void {
    $item = Mailbook::add(TestMail::class);

    expect($item->from())->toBe(['"Example" <hello@example.com>']);
});

it('can get from', function (): void {
    $item = Mailbook::add(OtherMail::class);

    expect($item->from())->toBe(['"Harry Potter" <harry@example.com>']);
});

it('can get from after rendering', function (): void {
    $item = Mailbook::add(OtherMail::class);

    $item->content();

    expect($item->from())->toBe(['"Harry Potter" <harry@example.com>']);
});

it('can get reply to', function (): void {
    $item = Mailbook::add(OtherMail::class);

    expect($item->replyTo())->toBe(['"Support" <questions@example.com>']);
});

it('can get to', function (): void {
    $item = Mailbook::add(OtherMail::class);

    expect($item->to())->toBe(['"Mailbook" <example@mailbook.dev>']);
});

it('can get cc', function (): void {
    $item = Mailbook::add(OtherMail::class);

    expect($item->cc())->toBe(['"Mailbook" <cc@mailbook.dev>']);
});

it('can get bcc', function (): void {
    $item = Mailbook::add(OtherMail::class);

    expect($item->bcc())->toBe(['"Mailbook" <bcc@mailbook.dev>']);
});

it('can get size', function (): void {
    $item = Mailbook::add(OtherMail::class);

    expect($item->size())->toBe('20 B');
});

it('builds mailable resolved from instance', function (): void {
    $item = Mailbook::add(new OtherMail());

    expect($item->subject())->toBe('Hello!');
});

it('can get attachments', function (): void {
    $item = Mailbook::add(WithAttachmentsMail::class);

    expect($item->attachments())->toEqual([
        'document.pdf',
        'rows.csv',
    ]);
});

it('executes the closure once', function (): void {
    $executed = 0;

    $mailable = Mailbook::add(function () use (&$executed) {
        $executed++;

        return new TestMail();
    });

    $mailable->subject();
    $mailable->to();
    $mailable->content();

    expect($executed)->toBe(1);
});

it('can get default theme', function (): void {
    $item = Mailbook::add(OtherMail::class);

    expect($item->theme())->toBeNull();
});

it('can get theme', function (): void {
    $mail = new OtherMail();
    $mail->theme = 'shop';
    $item = Mailbook::add($mail);

    expect($item->theme())->toBe('shop');
});

it('can get meta', function (): void {
    $item = Mailbook::add(OtherMail::class);

    expect($item->meta())->toBe([
        'Subject' => 'Hello!',
        'From' => [
            '"Harry Potter" <harry@example.com>',
        ],
        'Reply To' => [
            '"Support" <questions@example.com>',
        ],
        'To' => [
            '"Mailbook" <example@mailbook.dev>',
        ],
        'Cc' => [
            '"Mailbook" <cc@mailbook.dev>',
        ],
        'Bcc' => [
            '"Mailbook" <bcc@mailbook.dev>',
        ],
    ]);
});
