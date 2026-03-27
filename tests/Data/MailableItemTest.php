<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests\Data;

use UnexpectedValueException;
use Xammie\Mailbook\Exceptions\MailbookException;
use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Tests\Fixtures\Mails\NotificationMail;
use Xammie\Mailbook\Tests\Fixtures\Mails\OtherMail;
use Xammie\Mailbook\Tests\Fixtures\Mails\TestBinding;
use Xammie\Mailbook\Tests\Fixtures\Mails\TestMail;
use Xammie\Mailbook\Tests\Fixtures\Mails\WithAttachmentsMail;
use Xammie\Mailbook\Tests\TestCase;

class MailableItemTest extends TestCase
{
    public function test_can_render(): void
    {
        $html = Mailbook::add(TestMail::class)->content();
        $this->assertStringContainsString('Test mail', $html);
    }

    public function test_can_set_label(): void
    {
        $item = Mailbook::add(TestMail::class);
        $this->assertSame('Test Mail', $item->getLabel());
        $item->label('test');
        $this->assertSame('test', $item->getLabel());
    }

    public function test_can_render_closure(): void
    {
        $html = Mailbook::add(fn () => new TestMail)->content();
        $this->assertStringContainsString('Test mail', $html);
    }

    public function test_can_get_subject(): void
    {
        $subject = Mailbook::add(TestMail::class)->subject();
        $this->assertSame('Test email subject', $subject);
    }

    public function test_cannot_get_subject_with_container_binding_error(): void
    {
        $subject = Mailbook::add(TestBinding::class)->subject();
        $this->assertNull($subject);
    }

    public function test_cannot_get_missing_subject(): void
    {
        $subject = Mailbook::add(NotificationMail::class)->subject();
        $this->assertSame('Notification Mail', $subject);
    }

    public function test_can_render_multiple_times(): void
    {
        $item = Mailbook::add(fn () => new TestMail);
        $this->assertSame($item->content(), $item->content());
    }

    public function test_can_register_variants(): void
    {
        $item = Mailbook::add(TestMail::class)
            ->variant('Test', fn () => new TestMail)
            ->variant('Another test', fn () => new TestMail);

        $this->assertCount(2, $item->getVariants());
    }

    public function test_cannot_register_duplicate_variants(): void
    {
        $this->expectException(MailbookException::class);
        $this->expectExceptionMessage('Variant Test (test) already exists');

        Mailbook::add(TestMail::class)
            ->variant('Test', fn () => new TestMail)
            ->variant('Test', fn () => new TestMail);
    }

    public function test_throws_with_invalid_return_type(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Unexpected value returned from mailbook closure expected instance of Illuminate\Contracts\Mail\Mailable but got string');

        Mailbook::add(fn () => 'invalid')->variantResolver()->instance();
    }

    public function test_is_equal(): void
    {
        $item = Mailbook::add(TestMail::class);
        $other = Mailbook::mailables()->first();
        $this->assertTrue($item->is($other));
    }

    public function test_will_resolve_once(): void
    {
        $item = Mailbook::add(TestMail::class);
        $this->assertSame($item->resolver(), $item->resolver());
    }

    public function test_can_get_variant_resolver_without_variants(): void
    {
        $item = Mailbook::add(TestMail::class);
        $this->assertSame(TestMail::class, $item->variantResolver()->className());
    }

    public function test_can_get_variant_resolver_from_default_variant(): void
    {
        $item = Mailbook::add(TestMail::class)
            ->variant('Other one', fn () => new OtherMail);
        $this->assertSame(OtherMail::class, $item->variantResolver()->className());
    }

    public function test_can_get_default_from(): void
    {
        $item = Mailbook::add(TestMail::class);
        $this->assertEquals(['"Example" <hello@example.com>'], $item->from());
    }

    public function test_can_get_from(): void
    {
        $item = Mailbook::add(OtherMail::class);
        $this->assertEquals(['"Harry Potter" <harry@example.com>'], $item->from());
    }

    public function test_can_get_from_after_rendering(): void
    {
        $item = Mailbook::add(OtherMail::class);
        $item->content();
        $this->assertEquals(['"Harry Potter" <harry@example.com>'], $item->from());
    }

    public function test_can_get_reply_to(): void
    {
        $item = Mailbook::add(OtherMail::class);
        $this->assertEquals(['"Support" <questions@example.com>'], $item->replyTo());
    }

    public function test_can_get_to(): void
    {
        $item = Mailbook::add(OtherMail::class);
        $this->assertEquals(['"Mailbook" <example@mailbook.dev>'], $item->to());
    }

    public function test_can_get_cc(): void
    {
        $item = Mailbook::add(OtherMail::class);
        $this->assertEquals(['"Mailbook" <cc@mailbook.dev>'], $item->cc());
    }

    public function test_can_get_bcc(): void
    {
        $item = Mailbook::add(OtherMail::class);
        $this->assertEquals(['"Mailbook" <bcc@mailbook.dev>'], $item->bcc());
    }

    public function test_can_get_size(): void
    {
        $item = Mailbook::add(OtherMail::class);
        $this->assertSame('20 B', $item->size());
    }

    public function test_builds_mailable_resolved_from_instance(): void
    {
        $item = Mailbook::add(new OtherMail);
        $this->assertSame('Hello!', $item->subject());
    }

    public function test_can_get_attachments(): void
    {
        $item = Mailbook::add(WithAttachmentsMail::class);
        $this->assertEquals([
            'document.pdf',
            'rows.csv',
        ], $item->attachments());
    }

    public function test_executes_the_closure_once(): void
    {
        $executed = 0;
        $mailable = Mailbook::add(function () use (&$executed) {
            $executed++;

            return new TestMail;
        });
        $mailable->subject();
        $mailable->to();
        $mailable->content();
        $this->assertSame(1, $executed);
    }

    public function test_can_get_default_theme(): void
    {
        $item = Mailbook::add(OtherMail::class);
        $this->assertNull($item->theme());
    }

    public function test_can_get_theme(): void
    {
        $mail = new OtherMail;
        $mail->theme = 'shop';
        $item = Mailbook::add($mail);
        $this->assertSame('shop', $item->theme());
    }

    public function test_can_get_meta(): void
    {
        $item = Mailbook::add(OtherMail::class);
        $this->assertEquals([
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
        ], $item->meta());
    }
}
