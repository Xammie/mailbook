<?php

use function Pest\Laravel\artisan;
use Xammie\Mailbook\Commands\InstallMailbookCommand;

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

    artisan(InstallMailbookCommand::class)->assertSuccessful();

    expect($path)->toBeFile()
        ->and(file_get_contents($path))->toBe('test');
});
