<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Mail\Mailables\Envelope;

class InstallMailbookCommand extends Command
{
    public $signature = 'mailbook:install';

    public $description = 'Install mailbook into your application';

    private Filesystem $files;

    public function handle(Filesystem $files): int
    {
        $this->files = $files;

        $this->output('Installing mailbook');

        $stubs = [
            'routes/mailbook.php' => 'route-file.php',
            'app/Mail/MailbookMail.php' => class_exists(Envelope::class) ? 'MailbookEnvelopeMail.php' : 'MailbookBuildMail.php',
            'resources/views/mail/mailbook.blade.php' => 'mailbook.blade.php',
        ];

        foreach ($stubs as $target => $stub) {
            $stubPath = __DIR__.'/../../stubs/'.$stub;
            $targetPath = base_path($target);

            $this->publishFile($stubPath, $targetPath);
        }

        $this->newLine();
        $url = route('mailbook.dashboard');
        $this->output("Mailbook has been installed. Navigate to $url to view it");

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

            $this->twoColumnDetail($output, '<fg=yellow;options=bold>SKIPPED</>');
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

        $this->task(sprintf('Copying file [%s] to [%s]', $from, $to));
    }

    private function twoColumnDetail(string $first, string $second): void
    {
        /** @phpstan-ignore function.alreadyNarrowedType */
        if (property_exists($this, 'components')) {
            $this->components->twoColumnDetail($first, $second);
        } else {
            $this->info($first);
        }
    }

    private function task(string $description): void
    {
        /** @phpstan-ignore function.alreadyNarrowedType */
        if (property_exists($this, 'components')) {
            $this->components->task($description);
        } else {
            $this->info($description);
        }
    }

    public function output(string $string): void
    {
        /** @phpstan-ignore function.alreadyNarrowedType */
        if (property_exists($this, 'components')) {
            $this->components->info($string);
        } else {
            $this->info($string);
        }
    }
}
