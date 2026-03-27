<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Tests\Commands;

use Symfony\Component\Console\Command\Command;
use Xammie\Mailbook\Commands\InstallMailbookCommand;
use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Tests\TestCase;

class InstallMailbookCommandTest extends TestCase
{
    public function test_can_install_mailbook(): void
    {
        $this->artisan(InstallMailbookCommand::class)->assertExitCode(Command::SUCCESS);
        $this->assertFileExists(base_path('routes/mailbook.php'));
        $this->assertFileExists(base_path('app/Mail/MailbookMail.php'));
        $this->assertFileExists(base_path('resources/views/mail/mailbook.blade.php'));
    }

    public function test_will_not_overwrite_existing_files(): void
    {
        $path = base_path('routes/mailbook.php');
        @mkdir(dirname($path), 0755, true);
        file_put_contents($path, 'test');
        $this->artisan(InstallMailbookCommand::class)->assertExitCode(Command::SUCCESS);
        $this->assertFileExists($path);
        $this->assertSame('test', file_get_contents($path));
    }

    public function test_can_collect_mails_from_route_file(): void
    {
        $this->artisan(InstallMailbookCommand::class)->assertExitCode(Command::SUCCESS);
        require_once base_path('app/Mail/MailbookMail.php');
        $mails = Mailbook::mailables();
        $this->assertCount(1, $mails);
    }

    public function test_can_collect_mails_from_route_file_once(): void
    {
        $this->artisan(InstallMailbookCommand::class)->assertExitCode(Command::SUCCESS);
        require_once base_path('app/Mail/MailbookMail.php');
        Mailbook::mailables();
        $mails = Mailbook::mailables();
        $this->assertCount(1, $mails);
    }

    public function test_cannot_collect_mails_from_non_existing_route_file(): void
    {
        config()->set('mailbook.route_file', base_path('routes/unknown.php'));
        $this->artisan(InstallMailbookCommand::class)->assertExitCode(Command::SUCCESS);
        require_once base_path('app/Mail/MailbookMail.php');
        $mails = Mailbook::mailables();
        $this->assertEmpty($mails);
    }

    public function test_can_render_installable_mail(): void
    {
        $this->artisan(InstallMailbookCommand::class)->assertExitCode(Command::SUCCESS);

        require_once base_path('app/Mail/MailbookMail.php');

        $mails = Mailbook::mailables();
        foreach ($mails as $mail) {
            $this->assertNotEmpty($mail->content());
            if ($mail->hasVariants()) {
                $variants = $mail->getVariants();
                foreach ($variants as $variant) {
                    $mail->selectVariant($variant->slug);
                    $this->assertNotEmpty($mail->content());
                }
            }
        }
    }
}
