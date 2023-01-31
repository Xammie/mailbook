<?php

namespace Xammie\Mailbook;

use Closure;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;
use Symfony\Component\Mime\Email;
use Xammie\Mailbook\Exceptions\MailbookException;

class Mailbook
{
    /**
     * @var Collection<int, MailableItem>
     */
    protected Collection $mailables;

    protected bool $hasCollected = false;

    protected ?string $locale = null;

    protected ?Email $message = null;

    public function __construct()
    {
        $this->mailables = collect();
    }

    public function add(string|Closure|Mailable|Notification $class): MailableItem
    {
        $item = new MailableItem($class);

        $this->mailables->push($item);

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

    public function retrieve(?string $class, ?string $variant, ?string $locale, bool $fallback = true): ?MailableItem
    {
        $mailables = $this->mailables();

        if ($mailables->isEmpty()) {
            throw new MailbookException('No mailbook mailables registered');
        }

        if ($class) {
            $selected = $mailables->first(fn (MailableItem $mailable) => $mailable->class() === $class);
        } elseif ($fallback) {
            $selected = $mailables->first();
        } else {
            $selected = null;
        }

        if (! $selected instanceof MailableItem) {
            return null;
        }

        $selected->selectVariant($variant);

        $this->setLocale($locale ?? config('app.locale'));

        return $selected;
    }
}
