<?php

declare(strict_types=1);

namespace Xammie\Mailbook;

use Illuminate\Mail\MailManager;
use Illuminate\Support\Facades\Mail;
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
        $this->app->extend('mail.manager', function (MailManager $manager): MailManager {
            return $manager->extend('mailbook', function (): MailbookTransport {
                return new MailbookTransport();
            });
        });
    }
}
