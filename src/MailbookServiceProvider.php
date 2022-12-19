<?php

namespace Xammie\Mailbook;

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

    public function bootingPackage()
    {
        Mail::extend('mailbook', fn () => new MailbookTransport());
    }
}
