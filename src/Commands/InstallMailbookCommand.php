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
        if (property_exists($this, 'components')) {
            $this->components->info('Installing mailbook');
        } else {
            $this->info('Installing mailbook');
        }

        $stubs = [
            'routes/mailbook.php' => 'route-file.php',
            'app/Mail/MailbookMail.php' => class_exists(\Illuminate\Mail\Mailables\Envelope::class) ? 'MailbookEnvelopeMail.php' : 'MailbookBuildMail.php',
            'resources/views/mail/mailbook.blade.php' => 'mailbook.blade.php',
        ];

        foreach ($stubs as $target => $stub) {
            $stubPath = __DIR__.'/../../stubs/'.$stub;
            $targetPath = base_path($target);

            $this->publishFile($stubPath, $targetPath);
        }

        $this->newLine();

        $url = route('mailbook.dashboard');

        if (property_exists($this, 'components')) {
            $this->components->info("Mailbook has been installed. Navigate to $url to view it");
        } else {
            $this->info("Mailbook has been installed. Navigate to $url to view it");
        }

        return self::SUCCESS;
    }

    private function publishFile(string $from, string $to): void
    {
        if ($this->files->missing($to)) {
            $this->createParentDirectory(dirname($to));

            $this->files->copy($from, $to);

            $this->status($from, $to);
        } else {
            $output = sprintf(
                'File [%s] already exists',
                str_replace(base_path().DIRECTORY_SEPARATOR, '', realpath($to)) // @phpstan-ignore-line
            );

            if (property_exists($this, 'components')) {
                $this->components->twoColumnDetail($output, '<fg=yellow;options=bold>SKIPPED</>');

                return;
            }

            $this->info($output);
        }
    }

    private function createParentDirectory(string $directory): void
    {
        if (! $this->files->isDirectory($directory)) {
            $this->files->makeDirectory(path: $directory, recursive: true, force: true);
        }
    }

    private function status(string $from, string $to): void
    {
        $from = str_replace(base_path().DIRECTORY_SEPARATOR, '', realpath($from)); // @phpstan-ignore-line
        $to = str_replace(base_path().DIRECTORY_SEPARATOR, '', realpath($to)); // @phpstan-ignore-line

        $output = sprintf('Copying file [%s] to [%s]', $from, $to);

        if (property_exists($this, 'components')) {
            $this->components->task($output);

            return;
        }
        $this->info($output);
    }
}
