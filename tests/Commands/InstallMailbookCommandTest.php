<?php

use function Pest\Laravel\artisan;
use Symfony\Component\Console\Command\Command;
use Xammie\Mailbook\Commands\InstallMailbookCommand;
use Xammie\Mailbook\Facades\Mailbook;

it('can install mailbook', function () {
    artisan(InstallMailbookCommand::class)->assertExitCode(Command::SUCCESS);

    expect(base_path('routes/mailbook.php'))->toBeFile()
        ->and(base_path('app/Mail/MailbookMail.php'))->toBeFile()
        ->and(base_path('resources/views/mail/mailbook.blade.php'))->toBeFile();
});

it('will not overwrite existing files', function () {
    $path = base_path('routes/mailbook.php');
    @mkdir(dirname($path), 0755, true);
    file_put_contents($path, 'test');

    artisan(InstallMailbookCommand::class)->assertExitCode(Command::SUCCESS);

    expect($path)
        ->toBeFile()
        ->and(file_get_contents($path))->toBe('test');
});

it('can collect mails from route file', function () {
    artisan(InstallMailbookCommand::class)->assertExitCode(Command::SUCCESS);

    require_once base_path('app/Mail/MailbookMail.php');

    $mails = Mailbook::mailables();

    expect($mails)->toHaveCount(1);
});

it('can will collect mails from route file once', function () {
    artisan(InstallMailbookCommand::class)->assertExitCode(Command::SUCCESS);

    require_once base_path('app/Mail/MailbookMail.php');

    Mailbook::mailables();
    $mails = Mailbook::mailables();

    expect($mails)->toHaveCount(1);
});

it('cannot collect mails from non existing route file', function () {
    config()->set('mailbook.route_file', base_path('routes/unknown.php'));

    artisan(InstallMailbookCommand::class)->assertExitCode(Command::SUCCESS);

    require_once base_path('app/Mail/MailbookMail.php');

    $mails = Mailbook::mailables();

    expect($mails)->toBeEmpty();
});

it('can render installable mail', function () {
    artisan(InstallMailbookCommand::class)->assertExitCode(Command::SUCCESS);

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
});
