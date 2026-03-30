<?php

declare(strict_types=1);

namespace Xammie\Mailbook;

use Illuminate\Mail\MailManager;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Xammie\Mailbook\Commands\InstallMailbookCommand;

class MailbookServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('mailbook')
            ->hasConfigFile()
            ->hasViews('mailbook')
            ->hasRoute('routes')
            ->hasCommand(InstallMailbookCommand::class);
    }

    public function packageBooted(): void
    {
        $this->app->extend('mail.manager', fn (MailManager $manager): MailManager => $manager->extend('mailbook', fn () => new MailbookTransport));
    }
}
