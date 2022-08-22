<?php

use function Pest\Laravel\artisan;
use Xammie\Mailbook\Commands\InstallMailbookCommand;
use Xammie\Mailbook\Facades\Mailbook;

it('can install mailbook', function () {
    artisan(InstallMailbookCommand::class)
        ->expectsOutput('Installing mailbook')
        ->expectsOutput('Created routes/mailbook.php')
        ->expectsOutput('Created app/Mail/MailbookMail.php')
        ->expectsOutput('Created resources/views/mail/mailbook.blade.php')
        ->expectsOutput('Mailbook has been installed. Head over to http://localhost/mailbook to view it')
        ->assertSuccessful();

    expect(base_path('routes/mailbook.php'))->toBeFile()
        ->and(base_path('app/Mail/MailbookMail.php'))->toBeFile()
        ->and(base_path('resources/views/mail/mailbook.blade.php'))->toBeFile();
});

it('will not overwrite existing files', function () {
    $path = base_path('routes/mailbook.php');
    @mkdir(dirname($path), 0755, true);
    file_put_contents($path, 'test');

    artisan(InstallMailbookCommand::class)
        ->expectsOutput('Installing mailbook')
        ->expectsOutput('Warning: routes/mailbook.php already exists')
        ->expectsOutput('Created app/Mail/MailbookMail.php')
        ->expectsOutput('Created resources/views/mail/mailbook.blade.php')
        ->expectsOutput('Mailbook has been installed. Head over to http://localhost/mailbook to view it')
        ->assertSuccessful();

    expect($path)->toBeFile()
        ->and(file_get_contents($path))->toBe('test');
});

it('can render installable mail', function () {
    artisan(InstallMailbookCommand::class)->assertSuccessful();
    require base_path('app/Mail/MailbookMail.php');

    $mails = Mailbook::mailables();

    expect($mails)->isNotEmpty();

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
