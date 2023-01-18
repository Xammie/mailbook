<?php

namespace Xammie\Mailbook;

use Closure;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;
use Symfony\Component\Mime\Email;

class Mailbook
{
    /**
     * @var Collection<int, MailableItem>
     */
    protected Collection $mailables;

    protected bool $hasCollected = false;

    protected ?string $locale = null;

    protected ?Email $message = null;

    protected mixed $notifiable = null;

    public function __construct()
    {
        $this->mailables = collect();
    }

    public function via(mixed $notifiable): self
    {
        $this->notifiable = $notifiable;

        return $this;
    }

    public function add(string|Closure|Mailable|Notification $class): MailableItem
    {
        $item = new MailableItem($class, $this->notifiable);

        $this->mailables->push($item);

        $this->notifiable = null;

        return $item;
    }

    /**
     * @return Collection<int, MailableItem>
     */
    public function mailables(): Collection
    {
        $this->collect();

        return $this->mailables;
    }

    private function collect(): void
    {
        if ($this->hasCollected) {
            return;
        }

        $filename = config('mailbook.route_file', base_path('routes/mailbook.php'));

        if (is_string($filename) && file_exists($filename)) {
            include $filename;

            $this->hasCollected = true;
        }
    }

    public function setLocale(mixed $locale): ?string
    {
        if (! is_string($locale)) {
            return null;
        }

        if (! in_array($locale, $this->localeCodes(), true)) {
            return null;
        }

        return $this->locale = $locale;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    private function localeCodes(): array
    {
        $locales = config('mailbook.locales');

        if (! is_array($locales)) {
            return [];
        }

        return array_keys($locales);
    }

    public function getMessage(): ?Email
    {
        return $this->message;
    }

    public function setMessage(Email $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function clearMessage(): self
    {
        $this->message = null;

        return $this;
    }
}
