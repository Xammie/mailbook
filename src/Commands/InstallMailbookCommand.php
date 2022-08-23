<?php

namespace Xammie\Mailbook\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class InstallMailbookCommand extends Command
{
    public $signature = 'mailbook:install';

    public $description = 'Install mailbook into your application';

    public function __construct(private Filesystem $files)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->components->info('Installing mailbook');

        $stubs = [
            'routes/mailbook.php' => 'route-file.php',
            'app/Mail/MailbookMail.php' => 'MailbookMail.php',
            'resources/views/mail/mailbook.blade.php' => 'mailbook.blade.php',
        ];

        foreach ($stubs as $target => $stub) {
            $stubPath = __DIR__.'/../../stubs/'.$stub;
            $targetPath = base_path($target);

            $this->publishFile($stubPath, $targetPath);
        }

        $this->newLine();

        $url = route('mailbook.dashboard');
        $this->components->info("Mailbook has been installed. Navigate to $url to view it");

        return self::SUCCESS;
    }

    private function publishFile(string $from, string $to): void
    {
        if ($this->files->missing($to)) {
            $this->createParentDirectory(dirname($to));

            $this->files->copy($from, $to);

            $this->status($from, $to, 'file');
        } else {
            $this->components->twoColumnDetail(sprintf(
                'File [%s] already exists',
                str_replace(base_path().'/', '', realpath($to)), // @phpstan-ignore-line
            ), '<fg=yellow;options=bold>SKIPPED</>');
        }
    }

    private function createParentDirectory(string $directory): void
    {
        if (! $this->files->isDirectory($directory)) {
            $this->files->makeDirectory(path: $directory, recursive: true, force: true);
        }
    }

    private function status(string $from, string $to, string $type): void
    {
        $from = str_replace(base_path().'/', '', realpath($from)); // @phpstan-ignore-line
        $to = str_replace(base_path().'/', '', realpath($to)); // @phpstan-ignore-line

        dump($from, $to);

        $this->components->task(sprintf(
            'Copying %s [%s] to [%s]',
            $type,
            $from,
            $to,
        ));
    }
}
