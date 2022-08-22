<?php

namespace Xammie\Mailbook\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class InstallMailbookCommand extends Command
{
    public $signature = 'mailbook:install';

    public $description = 'Install mailbook into your application';

    public function __construct(private readonly Filesystem $files)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Installing mailbook');

        $stubs = [
            'routes/mailbook.php' => 'route-file.php',
            'app/Mail/MailbookMail.php' => 'MailbookMail.php',
            'resources/views/mail/mailbook.blade.php' => 'mailbook.blade.php',
        ];

        foreach ($stubs as $target => $stub) {
            $targetPath = base_path($target);

            if ($this->files->exists($targetPath)) {
                $this->warn("Warning: $target already exists");
            } else {
                $stubPath = __DIR__.'/../../stubs/'.$stub;

                $directory = $this->files->dirname($targetPath);
                $this->files->makeDirectory(path: $directory, recursive: true, force: true);
                $this->files->copy($stubPath, $targetPath);
                $this->info("Created $target");
            }
        }

        $url = route('mailbook.dashboard');

        $this->info("Mailbook has been installed. Head over to $url to view it");

        return self::SUCCESS;
    }
}
