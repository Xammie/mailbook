<?php

namespace Xammie\Mailbook;

use Closure;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;
use Symfony\Component\Mime\Email;
use Xammie\Mailbook\Data\MailableGroup;
use Xammie\Mailbook\Data\MailableItem;
use Xammie\Mailbook\Exceptions\MailbookException;

class Mailbook
{
    protected MailCollection $collection;

    protected bool $hasCollected = false;

    protected ?string $locale = null;

    protected ?Email $message = null;

    protected ?MailRegistrar $registrar = null;

    public function __construct()
    {
        $this->collection = new MailCollection();
    }

    private function registrar(): MailRegistrar
    {
        if ($this->registrar instanceof MailRegistrar) {
            return $this->registrar;
        }

        return MailRegistrar::make($this->collection);
    }

    public function label(string $label): MailRegistrar
    {
        return $this->registrar()->label($label);
    }

    public function category(string $category): MailRegistrar
    {
        return $this->registrar()->category($category);
    }

    public function to(mixed $notifiable): MailRegistrar
    {
        return $this->registrar()->to($notifiable);
    }

    public function add(string|Closure|Mailable|Notification $class): MailableItem
    {
        return $this->registrar()->add($class);
    }

    /**
     * @return Collection<int, MailableItem>
     */
    public function mailables(): Collection
    {
        $this->collect();

        return $this->collection->all();
    }

    /**
     * @return Collection<int, MailableItem|MailableGroup>
     */
    public function groupedMailables(): Collection
    {
        $output = collect();
        $items = $this->mailables();
        $categories = [];

        foreach ($items as $item) {
            if ($item->hasCategory() && ! in_array($item->getCategory(), $categories, true)) {
                $categories[] = $item->getCategory();
                $categoryItems = $items
                    ->filter(fn (MailableItem $mailable) => $mailable->getCategory() === $item->getCategory())
                    ->values();

                $output->push(new MailableGroup(
                    label: $item->getCategory() ?? '',
                    items: $categoryItems,
                ));
            } elseif (! in_array($item->getCategory(), $categories, true)) {
                $output->push($item);
            }
        }

        return $output;
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

    public function setRegistrar(MailRegistrar $registrar): void
    {
        $this->registrar = $registrar;
    }

    public function clearRegistrar(): void
    {
        $this->registrar = null;
    }

    public function retrieve(?string $class, ?string $variant, ?string $locale, bool $fallback = false): ?MailableItem
    {
        $mailables = $this->mailables();

        if ($mailables->isEmpty()) {
            throw new MailbookException('No mailbook mailables registered');
        }

        $selected = null;

        if ($class) {
            $selected = $mailables->first(fn (MailableItem $mailable) => mb_strtolower($mailable->class()) === mb_strtolower($class));
        }

        if (! $selected instanceof MailableItem && $fallback) {
            $selected = $mailables->first();
        }

        if (! $selected instanceof MailableItem) {
            return null;
        }

        $selected->selectVariant($variant);

        $this->setLocale($locale ?? config('app.locale'));

        return $selected;
    }
}
