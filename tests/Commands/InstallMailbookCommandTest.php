<?php

use function Pest\Laravel\artisan;
use Xammie\Mailbook\Commands\InstallMailbookCommand;
use Xammie\Mailbook\Facades\Mailbook;

it('can install mailbook', function () {
    $stubsPath = realpath(__DIR__.'/../../stubs/');

    artisan(InstallMailbookCommand::class)
        ->expectsOutputToContain('Installing mailbook.')
        ->expectsOutputToContain(sprintf('Copying file [%s] to [%s]', testFilepath($stubsPath.'/route-file.php'), 'routes/mailbook.php'))
        ->expectsOutputToContain(sprintf('Copying file [%s] to [%s]', testFilepath($stubsPath.'/MailbookMail.php'), 'app/Mail/MailbookMail.php'))
        ->expectsOutputToContain(sprintf('Copying file [%s] to [%s]', testFilepath($stubsPath.'/mailbook.blade.php'), 'resources/views/mail/mailbook.blade.php'))
        ->expectsOutputToContain('Mailbook has been installed.')
        ->assertSuccessful();

    expect(base_path('routes/mailbook.php'))->toBeFile()
        ->and(base_path('app/Mail/MailbookMail.php'))->toBeFile()
        ->and(base_path('resources/views/mail/mailbook.blade.php'))->toBeFile();
});

it('will not overwrite existing files', function () {
    $path = base_path('routes/mailbook.php');
    @mkdir(dirname($path), 0755, true);
    file_put_contents($path, 'test');

    $stubsPath = realpath(__DIR__.'/../../stubs/');

    artisan(InstallMailbookCommand::class)
        ->expectsOutputToContain('Installing mailbook.')
        ->expectsOutputToContain('File [routes/mailbook.php] already exists')
        ->expectsOutputToContain("Copying file [$stubsPath/MailbookMail.php] to [app/Mail/MailbookMail.php]")
        ->expectsOutputToContain("Copying file [$stubsPath/mailbook.blade.php] to [resources/views/mail/mailbook.blade.php]")
        ->expectsOutputToContain('Mailbook has been installed.')
        ->assertSuccessful();

    expect($path)->toBeFile()
        ->and(file_get_contents($path))->toBe('test');
});

it('can collect mails from route file', function () {
    artisan(InstallMailbookCommand::class)->assertSuccessful();
    require_once base_path('app/Mail/MailbookMail.php');

    $mails = Mailbook::mailables();

    expect($mails)->isNotEmpty();
});

it('can render installable mail', function () {
    artisan(InstallMailbookCommand::class)->assertSuccessful();
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
