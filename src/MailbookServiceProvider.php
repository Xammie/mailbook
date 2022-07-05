<?php

namespace Xammie\Mailbook;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class MailbookServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('mailbook')
            ->hasConfigFile()
            ->hasViews('mailbook')
            ->hasRoute('routes');
    }
}
